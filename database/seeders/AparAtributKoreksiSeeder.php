<?php

namespace Database\Seeders;

use App\Models\Apar;
use App\Models\Lokasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AparAtributKoreksiSeeder extends Seeder
{
    public const TARGET_TOTAL_APAR = 173;

    public const ALLOWED_MEDIA = [
        'Dry Chemical Powder',
        'CO2 (Carbon Dioxide)',
    ];

    public const ALLOWED_KAPASITAS = [
        '3 kg',
        '5 kg',
        '6 kg',
        '7 kg',
        '25 kg',
    ];

    /**
     * Seeder koreksi satu kali (idempotent & hardened) untuk memperbaiki atribut APAR
     * (media, kapasitas, dan pengosongan asumsi merek) berdasarkan Tabel 12
     * Laporan Triwulan Ke-3 2026 RSUD dr. H. Moch. Ansari Saleh.
     *
     * Dilengkapi validasi pra-eksekusi menyeluruh (fail-fast) untuk mencegah
     * silent-skip, fallback parsial, atau distorsi kuota per lokasi.
     */
    public function run(): void
    {
        // 1. TAHAP VALIDASI PRA-EKSEKUSI (Pre-validation pass)
        // Membangun rencana pembaruan deterministik sebelum menyentuh data database
        $updatePlans = $this->validateAndBuildCorrectionPlan();

        // 2. TAHAP EKSEKUSI TRANSAKSIONAL (Atomic Execution)
        $updatedCount = 0;
        $totalPowder = 0;
        $totalCo2 = 0;

        DB::transaction(function () use ($updatePlans, &$updatedCount, &$totalPowder, &$totalCo2) {
            foreach ($updatePlans as $plan) {
                /** @var Apar $apar */
                $apar = $plan['apar'];

                $apar->update([
                    'jenis_media' => $plan['media'],
                    'kapasitas' => $plan['kapasitas'],
                    'merek' => null, // Reset merek asumsi ke NULL karena tidak ada data merek resmi di sumber
                ]);

                $updatedCount++;
                if ($plan['media'] === 'Dry Chemical Powder') {
                    $totalPowder++;
                } else {
                    $totalCo2++;
                }
            }
        });

        if (isset($this->command)) {
            $this->command->info(sprintf(
                'Koreksi atribut selesai: %d unit diperbarui (%d Powder, %d CO2). Merek direset ke NULL.',
                $updatedCount,
                $totalPowder,
                $totalCo2
            ));
        }
    }

    /**
     * Validasi ketat seluruh lokasi, kuota, kategori, dan pemetaan unit sebelum update.
     * Melempar RuntimeException jika ditemukan anomali atau ketidaksesuaian.
     *
     * @return array<int, array{apar: Apar, media: string, kapasitas: string}>
     *
     * @throws RuntimeException
     */
    public function validateAndBuildCorrectionPlan(?array $locationsData = null): array
    {
        $locationsData = $locationsData ?? LokasiAktualSeeder::LOCATIONS_DATA;
        $updatePlans = [];
        $seenAparIds = [];
        $totalPlannedUnits = 0;

        // 1. Validasi per Definisi Lokasi dan Kuota Masing-Masing
        foreach ($locationsData as $locationDef) {
            $namaLokasi = $locationDef['nama'] ?? 'Tanpa Nama';
            $targetCount = $locationDef['target_apar'] ?? 0;

            // Lokasi kosong dan stok sediaan diabaikan dari alokasi unit terpasang
            if ($targetCount <= 0 || $namaLokasi === 'GUDANG K3RS') {
                continue;
            }

            // Hapus silent skip: Lokasi wajib terdaftar di master lokasis
            $lokasi = Lokasi::where('nama', $namaLokasi)->first();
            if (! $lokasi) {
                throw new RuntimeException(sprintf(
                    "Validasi gagal: Lokasi master '%s' tidak ditemukan di database (tabel lokasis).",
                    $namaLokasi
                ));
            }

            $items = $locationDef['items'] ?? [];

            // Validasi kelengkapan matriks dalam kode
            if (count($items) !== $targetCount) {
                throw new RuntimeException(sprintf(
                    "Validasi gagal: Matriks items pada lokasi '%s' berisi %d item, tidak sesuai dengan target_apar (%d).",
                    $namaLokasi,
                    count($items),
                    $targetCount
                ));
            }

            // Validasi keabsahan kategori media dan kapasitas di setiap item matriks
            foreach ($items as $itemIdx => $item) {
                $media = $item['media'] ?? null;
                $kapasitas = $item['kapasitas'] ?? null;

                if (! in_array($media, self::ALLOWED_MEDIA, true)) {
                    throw new RuntimeException(sprintf(
                        "Validasi gagal: Jenis media '%s' pada lokasi '%s' (item indeks ke-%d) tidak valid.",
                        $media ?? 'null',
                        $namaLokasi,
                        $itemIdx
                    ));
                }

                if (! in_array($kapasitas, self::ALLOWED_KAPASITAS, true)) {
                    throw new RuntimeException(sprintf(
                        "Validasi gagal: Kapasitas '%s' pada lokasi '%s' (item indeks ke-%d) tidak valid.",
                        $kapasitas ?? 'null',
                        $namaLokasi,
                        $itemIdx
                    ));
                }
            }

            // Ambil record APAR aktif yang ada di database pada lokasi ini
            $existingApars = Apar::where('lokasi_id', $lokasi->id)
                ->where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
                ->orderBy('kode')
                ->get();

            // Hapus fallback otomatis: jumlah unit harus sama persis dengan matriks
            if ($existingApars->count() !== count($items)) {
                $selisih = $existingApars->count() - count($items);
                $tandaSelisih = $selisih > 0 ? "+{$selisih}" : (string) $selisih;
                throw new RuntimeException(sprintf(
                    "Ketidaksesuaian jumlah APAR pada lokasi '%s' (ID: %d): ditemukan %d unit di database, tetapi matriks Tabel 12 mensyaratkan %d unit (selisih: %s unit).",
                    $lokasi->nama,
                    $lokasi->id,
                    $existingApars->count(),
                    count($items),
                    $tandaSelisih
                ));
            }

            // Petakan setiap unit ke spesifikasi matriks secara deterministik
            foreach ($existingApars as $index => $apar) {
                if (isset($seenAparIds[$apar->id])) {
                    throw new RuntimeException(sprintf(
                        'Validasi gagal: APAR ID %d (Kode: %s) terdeteksi diproses lebih dari 1 kali.',
                        $apar->id,
                        $apar->kode
                    ));
                }

                $seenAparIds[$apar->id] = true;
                $itemSpec = $items[$index];

                $updatePlans[] = [
                    'apar' => $apar,
                    'media' => $itemSpec['media'],
                    'kapasitas' => $itemSpec['kapasitas'],
                ];
                $totalPlannedUnits++;
            }
        }

        // 2. Validasi Total Akhir Unit yang Direncanakan
        if ($totalPlannedUnits !== self::TARGET_TOTAL_APAR) {
            throw new RuntimeException(sprintf(
                'Validasi gagal: Total unit yang direncanakan untuk diperbarui adalah %d unit, bukan %d unit.',
                $totalPlannedUnits,
                self::TARGET_TOTAL_APAR
            ));
        }

        // 3. Validasi Total APAR Aktif di Database & Tidak Ada yang Terlewat
        $allActiveApars = Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)->get();
        if ($allActiveApars->count() !== self::TARGET_TOTAL_APAR) {
            throw new RuntimeException(sprintf(
                'Validasi gagal: Total APAR aktif di database adalah %d unit, diharapkan tepat %d unit.',
                $allActiveApars->count(),
                self::TARGET_TOTAL_APAR
            ));
        }

        $unprocessedApars = $allActiveApars->whereNotIn('id', array_keys($seenAparIds));
        if ($unprocessedApars->isNotEmpty()) {
            $unprocessedCodes = $unprocessedApars->pluck('kode')->take(5)->implode(', ');
            throw new RuntimeException(sprintf(
                'Validasi gagal: Terdapat %d APAR aktif di database yang tidak terpetakan dalam rencana koreksi (Contoh: %s).',
                $unprocessedApars->count(),
                $unprocessedCodes
            ));
        }

        return $updatePlans;
    }
}
