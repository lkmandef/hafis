<?php

namespace App\Services;

use App\Models\Apar;
use App\Models\MasterChecklist;
use App\Models\Pemeriksaan;
use App\Models\User;
use Carbon\Carbon;
use DomainException;
use Illuminate\Support\Facades\DB;

class SmartInspectionService
{
    /**
     * Evaluasi konteks kepatuhan inspeksi unit APAR untuk periode berjalan (M) dan periode sebelumnya (M-1).
     *
     * @return array{
     *     apar: Apar,
     *     mode: string,
     *     current_period: array{tahun: int, bulan: int, nama_bulan: string, exists: bool, model: ?Pemeriksaan},
     *     backlog_period: array{tahun: int, bulan: int, nama_bulan: string, exists: bool, model: ?Pemeriksaan},
     *     is_eligible: bool
     * }
     */
    public function evaluateAparContext(Apar $apar, ?Carbon $now = null): array
    {
        $now = $now ? $now->copy() : Carbon::now();

        $tahunIni = (int) $now->year;
        $bulanIni = (int) $now->month;

        $prev = $now->copy()->subMonth();
        $tahunLalu = (int) $prev->year;
        $bulanLalu = (int) $prev->month;

        $isEligible = $apar->isEligibleForInspection();

        $pemBulanIni = Pemeriksaan::findByAparPeriod($apar->id, $tahunIni, $bulanIni);
        $pemBulanLalu = Pemeriksaan::findByAparPeriod($apar->id, $tahunLalu, $bulanLalu);

        $needsCurrent = ($pemBulanIni === null);
        $needsBacklog = ($pemBulanLalu === null);

        // Batas toleransi (Grace Period Boundary):
        // Jika APAR baru dipasang pada bulan M (berjalan), tidak diwajibkan tunggakan M-1
        if ($apar->tanggal_pemasangan && Carbon::parse($apar->tanggal_pemasangan)->startOfMonth()->gt($prev->copy()->endOfMonth())) {
            $needsBacklog = false;
        }

        // Baseline Operasional Resmi (November 2026):
        // Periode sebelum awal operasional resmi tidak boleh dianggap sebagai tunggakan
        $startYear = (int) config('hafis.operational_start_year', 2026);
        $startMonth = (int) config('hafis.operational_start_month', 11);
        if ($tahunLalu < $startYear || ($tahunLalu === $startYear && $bulanLalu < $startMonth)) {
            $needsBacklog = false;
        }

        $mode = match (true) {
            ! $isEligible => 'ineligible',
            $needsBacklog && $needsCurrent => 'multi_period',
            $needsBacklog && ! $needsCurrent => 'backlog_only',
            ! $needsBacklog && $needsCurrent => 'regular',
            default => 'completed',
        };

        return [
            'apar' => $apar,
            'mode' => $mode,
            'current_period' => [
                'tahun' => $tahunIni,
                'bulan' => $bulanIni,
                'nama_bulan' => Pemeriksaan::NAMA_BULAN[$bulanIni] ?? (string) $bulanIni,
                'exists' => ! $needsCurrent,
                'model' => $pemBulanIni,
            ],
            'backlog_period' => [
                'tahun' => $tahunLalu,
                'bulan' => $bulanLalu,
                'nama_bulan' => Pemeriksaan::NAMA_BULAN[$bulanLalu] ?? (string) $bulanLalu,
                'exists' => ! $needsBacklog,
                'model' => $pemBulanLalu,
            ],
            'is_eligible' => $isEligible,
        ];
    }

    /**
     * Eksekusi transaksi penyimpanan inspeksi fisik APAR secara atomik.
     * Mendukung multi-period resolution (M-1 terlambat + M tepat waktu).
     */
    public function executeInspection(Apar $apar, array $payload, ?User $user = null, ?array $context = null): Pemeriksaan
    {
        if (! $apar->isEligibleForInspection()) {
            throw new DomainException("Unit APAR ID {$apar->id} ({$apar->kode}) tidak memenuhi syarat untuk inspeksi operasional aktif.");
        }

        $context = $context ?? $this->evaluateAparContext($apar, ! empty($payload['tanggal_pemeriksaan']) ? Carbon::parse($payload['tanggal_pemeriksaan']) : null);

        if ($context['mode'] === 'ineligible') {
            throw new DomainException("Unit APAR {$apar->kode} tidak dapat diinspeksi karena berstatus tidak aktif atau berada di lokasi stok.");
        }

        if ($context['mode'] === 'completed' && empty($payload['force_update'])) {
            throw new DomainException("Unit APAR {$apar->kode} sudah memiliki catatan pemeriksaan lengkap untuk periode ini.");
        }

        return DB::transaction(function () use ($apar, $payload, $user, $context) {
            return match ($context['mode']) {
                'multi_period' => $this->resolveMultiPeriod($apar, $payload, $user, $context),
                'backlog_only' => $this->resolveBacklogOnly($apar, $payload, $user, $context),
                default => $this->resolveRegular($apar, $payload, $user, $context),
            };
        });
    }

    /**
     * Resolusi Multi-Periode: Menyimpan 1 Master Record (M) dan 1 Linked Record (M-1).
     */
    protected function resolveMultiPeriod(Apar $apar, array $payload, ?User $user, array $context): Pemeriksaan
    {
        $tanggalPemeriksaan = ! empty($payload['tanggal_pemeriksaan'])
            ? Carbon::parse($payload['tanggal_pemeriksaan'])
            : Carbon::now();

        $canonicalItems = MasterChecklist::getActiveCanonicalNames();
        $hasilKeseluruhan = $this->determineOverallResult($payload['items'], $payload['kesimpulan_manual'] ?? null);
        $pemeriksaNama = $user ? $user->name : ($payload['pemeriksa_nama'] ?? 'Petugas K3RS');

        $periodeTahun = $payload['periode_tahun'] ?? $context['current_period']['tahun'];
        $periodeBulan = $payload['periode_bulan'] ?? $context['current_period']['bulan'];

        // 1. Simpan Master Record untuk periode berjalan (M)
        $master = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'jadwal_id' => $payload['jadwal_id'] ?? null,
            'pemeriksaan_induk_id' => null,
            'periode_tahun' => $periodeTahun,
            'periode_bulan' => $periodeBulan,
            'user_id' => $user?->id,
            'pemeriksa_nama' => $pemeriksaNama,
            'tanggal_pemeriksaan' => $tanggalPemeriksaan,
            'hasil_keseluruhan' => $hasilKeseluruhan,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'status' => 'selesai',
            'catatan' => $payload['catatan_pemeriksaan'] ?? null,
            'foto_path' => $payload['foto_path'] ?? null,
        ]);

        // 2. Simpan 5 rincian item checklist pada Master Record
        $this->persistChecklistDetails($master, $payload['items'], $canonicalItems);

        // 3. Simpan Linked Record untuk periode sebelumnya (M-1) sebagai Selesai Terlambat
        $shouldResolveBacklog = ! empty($payload['resolve_backlog']);
        if ($shouldResolveBacklog) {
            $namaBulanLalu = $context['backlog_period']['nama_bulan'];
            $tahunLalu = $context['backlog_period']['tahun'];

            Pemeriksaan::create([
                'apar_id' => $apar->id,
                'jadwal_id' => $payload['jadwal_id'] ?? null,
                'pemeriksaan_induk_id' => $master->id,
                'periode_tahun' => $tahunLalu,
                'periode_bulan' => $context['backlog_period']['bulan'],
                'user_id' => $user?->id,
                'pemeriksa_nama' => $pemeriksaNama,
                'tanggal_pemeriksaan' => $tanggalPemeriksaan, // Mencatat waktu fisik riil saat QR dipindai
                'hasil_keseluruhan' => $hasilKeseluruhan,
                'tipe_penyelesaian' => Pemeriksaan::TIPE_TERLAMBAT,
                'status' => 'selesai',
                'catatan' => "Resolusi kepatuhan terlambat periode {$namaBulanLalu} {$tahunLalu} via inspeksi fisik #{$master->id}.",
                'foto_path' => $payload['foto_path'] ?? null,
            ]);
        }

        // 4. Sinkronisasi status kelaikan fisik tabung APAR
        $apar->syncStatusAfterInspection($master);

        return $master;
    }

    /**
     * Resolusi Tunggakan Saja (M-1): Menggunakan pemeriksaan fisik periode berjalan (M) yang sudah ada.
     * Tidak membuat aktivitas fisik baru atau checklist duplikat.
     */
    protected function resolveBacklogOnly(Apar $apar, array $payload, ?User $user, array $context): Pemeriksaan
    {
        $existingPhysical = $context['current_period']['model']
            ?? Pemeriksaan::findByAparPeriod($apar->id, $context['current_period']['tahun'], $context['current_period']['bulan']);

        $tanggalPemeriksaan = $existingPhysical
            ? $existingPhysical->tanggal_pemeriksaan
            : (! empty($payload['tanggal_pemeriksaan']) ? Carbon::parse($payload['tanggal_pemeriksaan']) : Carbon::now());

        $pemeriksaNama = $user ? $user->name : ($payload['pemeriksa_nama'] ?? ($existingPhysical?->pemeriksa_nama ?? 'Petugas K3RS'));
        $namaBulanLalu = $context['backlog_period']['nama_bulan'];
        $tahunLalu = $context['backlog_period']['tahun'];

        if ($existingPhysical) {
            // Gunakan pemeriksaan fisik periode berjalan yang sudah ada sebagai induk (linked record), tanpa duplikasi checklist fisik
            $record = Pemeriksaan::create([
                'apar_id' => $apar->id,
                'jadwal_id' => $payload['jadwal_id'] ?? $existingPhysical->jadwal_id,
                'pemeriksaan_induk_id' => $existingPhysical->id,
                'periode_tahun' => $tahunLalu,
                'periode_bulan' => $context['backlog_period']['bulan'],
                'user_id' => $user?->id ?? $existingPhysical->user_id,
                'pemeriksa_nama' => $pemeriksaNama,
                'tanggal_pemeriksaan' => $tanggalPemeriksaan,
                'hasil_keseluruhan' => $existingPhysical->hasil_keseluruhan,
                'tipe_penyelesaian' => Pemeriksaan::TIPE_TERLAMBAT,
                'status' => 'selesai',
                'catatan' => $payload['catatan_pemeriksaan'] ?? "Resolusi kepatuhan terlambat periode {$namaBulanLalu} {$tahunLalu} via inspeksi fisik #{$existingPhysical->id}.",
                'foto_path' => $existingPhysical->foto_path,
            ]);
            // JANGAN membuat duplikasi di pemeriksaan_details (0 baris duplikasi)
        } else {
            // Fallback jika tidak ada record fisik berjalan
            $canonicalItems = MasterChecklist::getActiveCanonicalNames();
            $hasilKeseluruhan = $this->determineOverallResult($payload['items'] ?? [], $payload['kesimpulan_manual'] ?? null);

            $record = Pemeriksaan::create([
                'apar_id' => $apar->id,
                'jadwal_id' => $payload['jadwal_id'] ?? null,
                'pemeriksaan_induk_id' => null,
                'periode_tahun' => $tahunLalu,
                'periode_bulan' => $context['backlog_period']['bulan'],
                'user_id' => $user?->id,
                'pemeriksa_nama' => $pemeriksaNama,
                'tanggal_pemeriksaan' => $tanggalPemeriksaan,
                'hasil_keseluruhan' => $hasilKeseluruhan,
                'tipe_penyelesaian' => Pemeriksaan::TIPE_TERLAMBAT,
                'status' => 'selesai',
                'catatan' => $payload['catatan_pemeriksaan'] ?? "Pemeriksaan fisik pemenuhan tunggakan bulan {$namaBulanLalu} {$tahunLalu}.",
                'foto_path' => $payload['foto_path'] ?? null,
            ]);

            if (! empty($payload['items'])) {
                $this->persistChecklistDetails($record, $payload['items'], $canonicalItems);
            }
        }

        $apar->syncStatusAfterInspection($record);

        return $record;
    }

    /**
     * Resolusi Normal (M): Pemeriksaan reguler 1 periode tepat waktu.
     */
    protected function resolveRegular(Apar $apar, array $payload, ?User $user, array $context): Pemeriksaan
    {
        $tanggalPemeriksaan = ! empty($payload['tanggal_pemeriksaan'])
            ? Carbon::parse($payload['tanggal_pemeriksaan'])
            : Carbon::now();

        $periodeTahun = $payload['periode_tahun'] ?? $context['current_period']['tahun'];
        $periodeBulan = $payload['periode_bulan'] ?? $context['current_period']['bulan'];

        $canonicalItems = MasterChecklist::getActiveCanonicalNames();
        $hasilKeseluruhan = $this->determineOverallResult($payload['items'], $payload['kesimpulan_manual'] ?? null);
        $pemeriksaNama = $user ? $user->name : ($payload['pemeriksa_nama'] ?? 'Petugas K3RS');

        // Evaluasi apakah periode target lebih lampau dari bulan tanggal pemeriksaan
        $targetDate = Carbon::createFromDate($periodeTahun, $periodeBulan, 1)->endOfMonth();
        $isLate = $tanggalPemeriksaan->gt($targetDate);
        $tipePenyelesaian = $isLate ? Pemeriksaan::TIPE_TERLAMBAT : Pemeriksaan::TIPE_TEPAT_WAKTU;

        $record = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'jadwal_id' => $payload['jadwal_id'] ?? null,
            'pemeriksaan_induk_id' => null,
            'periode_tahun' => $periodeTahun,
            'periode_bulan' => $periodeBulan,
            'user_id' => $user?->id,
            'pemeriksa_nama' => $pemeriksaNama,
            'tanggal_pemeriksaan' => $tanggalPemeriksaan,
            'hasil_keseluruhan' => $hasilKeseluruhan,
            'tipe_penyelesaian' => $tipePenyelesaian,
            'status' => 'selesai',
            'catatan' => $payload['catatan_pemeriksaan'] ?? null,
            'foto_path' => $payload['foto_path'] ?? null,
        ]);

        $this->persistChecklistDetails($record, $payload['items'], $canonicalItems);
        $apar->syncStatusAfterInspection($record);

        return $record;
    }

    /**
     * Tentukan hasil keseluruhan (Baik / Perlu Tindakan).
     */
    protected function determineOverallResult(array $items, ?string $manualConclusion): string
    {
        foreach ($items as $item) {
            if (($item['hasil'] ?? '') === 'tidak_baik') {
                return Pemeriksaan::HASIL_PERLU_TINDAKAN;
            }
        }

        return $manualConclusion ?? Pemeriksaan::HASIL_BAIK;
    }

    /**
     * Persistensikan 5 item checklist fisik kanonikal ke tabel pemeriksaan_details.
     */
    protected function persistChecklistDetails(Pemeriksaan $pemeriksaan, array $items, array $canonicalItems): void
    {
        foreach ($items as $itemData) {
            $nama = trim($itemData['nama'] ?? '');
            $canonicalIndex = array_search($nama, $canonicalItems, true);
            $urutan = $canonicalIndex !== false ? (int) $canonicalIndex : 0;

            $pemeriksaan->details()->create([
                'item_nama' => $nama,
                'hasil' => $itemData['hasil'] ?? 'tidak_diperiksa',
                'catatan' => $itemData['catatan'] ?? null,
                'urutan' => $urutan,
            ]);
        }
    }
}
