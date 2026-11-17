<?php

namespace App\Services;

use App\Models\Apar;
use App\Models\Lokasi;
use App\Models\MasterChecklist;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KartuPemeliharaanService
{
    /**
     * Simbol matriks hasil inspeksi (dapat dikonfigurasi di satu tempat terpusat)
     */
    public const SYMBOL_BAIK = '✓';

    public const SYMBOL_TIDAK_BAIK = 'X';

    public const SYMBOL_TIDAK_DIPERIKSA = '–'; // En-dash untuk lewati/tidak diperiksa

    public const SYMBOL_KOSONG = '';

    /**
     * Pemetaan nilai database pemeriksaan_details.hasil ke simbol matriks
     */
    public const SYMBOL_MAP = [
        PemeriksaanDetail::HASIL_BAIK => self::SYMBOL_BAIK,
        PemeriksaanDetail::HASIL_TIDAK_BAIK => self::SYMBOL_TIDAK_BAIK,
        PemeriksaanDetail::HASIL_TIDAK_DIPERIKSA => self::SYMBOL_TIDAK_DIPERIKSA,
    ];

    /**
     * Batas maksimal jumlah unit APAR dalam sekali cetak massal (mencegah kehabisan memori)
     */
    public const MAX_BATCH_PRINT = 60;

    /**
     * Dapatkan mapping simbol resmi untuk kebutuhan legenda UI
     */
    public static function getSymbols(): array
    {
        return [
            'baik' => self::SYMBOL_BAIK,
            'tidak_baik' => self::SYMBOL_TIDAK_BAIK,
            'tidak_diperiksa' => self::SYMBOL_TIDAK_DIPERIKSA,
            'kosong' => self::SYMBOL_KOSONG,
        ];
    }

    /**
     * Tentukan orientasi cetak dokumen A4 berdasarkan jumlah kolom checklist
     * Potret jika kolom item <= 6, lanskap jika lebih dari 6.
     */
    public static function determinePrintOrientation(array $checklistColumns): string
    {
        return count($checklistColumns) <= 6 ? 'portrait' : 'landscape';
    }

    /**
     * Susun daftar kolom checklist matriks:
     * Master Checklist aktif (urut berdasarkan urutan) + Union kolom historis
     * dari item_nama yang ada di data tahun tersebut tapi tidak cocok master aktif,
     * diberi label "(non-aktif)" di sisi kanan.
     * Pencocokan nama menggunakan trim + case-insensitive.
     *
     * @param  Apar|Collection|EloquentCollection|array|null  $apars
     * @return array<int, array{key: string, title: string, is_active: bool, urutan: int}>
     */
    public function getChecklistColumns(int $tahun, mixed $apars = null): array
    {
        $columns = [];
        $activeKeyMap = [];

        // 1. Kolom dari master_checklists yang sedang aktif (diurutkan berdasarkan urutan ASC, id ASC)
        $activeItems = MasterChecklist::active()->ordered()->get();

        if ($activeItems->isNotEmpty()) {
            foreach ($activeItems as $item) {
                $trimmed = trim($item->nama);
                $key = mb_strtolower($trimmed);
                $columns[$key] = [
                    'key' => $key,
                    'title' => $trimmed,
                    'is_active' => true,
                    'urutan' => (int) ($item->urutan ?? 0),
                ];
                $activeKeyMap[$key] = true;
            }
        } else {
            // Fallback aman ke 5 kanonikal K3RS jika tabel master kosong
            foreach (Pemeriksaan::CANONICAL_CHECKLIST_ITEMS as $idx => $name) {
                $trimmed = trim($name);
                $key = mb_strtolower($trimmed);
                $columns[$key] = [
                    'key' => $key,
                    'title' => $trimmed,
                    'is_active' => true,
                    'urutan' => $idx + 1,
                ];
                $activeKeyMap[$key] = true;
            }
        }

        // 2. Kumpulkan item_nama historis dari pemeriksaan tahun tersebut
        $historicalNames = collect();

        $aparCollection = $apars instanceof Apar
            ? collect([$apars])
            : ($apars instanceof Collection || $apars instanceof EloquentCollection ? $apars : collect());

        if ($aparCollection->isNotEmpty()) {
            $hasLoadedRelation = false;
            foreach ($aparCollection as $aparItem) {
                if ($aparItem instanceof Apar && $aparItem->relationLoaded('pemeriksaans')) {
                    $hasLoadedRelation = true;
                    foreach ($aparItem->pemeriksaans as $pem) {
                        if ((int) $pem->periode_tahun === $tahun && ! in_array($pem->status, ['dibatalkan', 'draft'])) {
                            if ($pem->relationLoaded('details')) {
                                foreach ($pem->details as $det) {
                                    if (filled($det->item_nama)) {
                                        $historicalNames->push($det->item_nama);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (! $hasLoadedRelation) {
                $aparIds = $aparCollection->pluck('id')->filter()->all();
                if (! empty($aparIds)) {
                    $historicalNames = PemeriksaanDetail::whereHas('pemeriksaan', function ($q) use ($aparIds, $tahun) {
                        $q->whereIn('apar_id', $aparIds)
                            ->where('periode_tahun', $tahun)
                            ->whereNotIn('status', ['dibatalkan', 'draft']);
                    })->pluck('item_nama');
                }
            }
        } else {
            $historicalNames = PemeriksaanDetail::whereHas('pemeriksaan', function ($q) use ($tahun) {
                $q->where('periode_tahun', $tahun)
                    ->whereNotIn('status', ['dibatalkan', 'draft']);
            })->pluck('item_nama');
        }

        // 3. Tambahkan kolom historis yang tidak ada di master aktif ke sisi kanan
        $nonActiveColumns = [];
        foreach ($historicalNames->filter() as $rawName) {
            $trimmed = trim($rawName);
            $key = mb_strtolower($trimmed);

            if (! isset($activeKeyMap[$key]) && ! isset($nonActiveColumns[$key])) {
                $nonActiveColumns[$key] = [
                    'key' => $key,
                    'title' => $trimmed.' (non-aktif)',
                    'is_active' => false,
                    'urutan' => 9999,
                ];
            }
        }

        return array_values(array_merge(array_values($columns), array_values($nonActiveColumns)));
    }

    /**
     * Bangun matriks 12 bulan untuk 1 unit APAR pada tahun tertentu.
     * Menggunakan periode_bulan + periode_tahun sebagai kunci utama.
     * Jika satu bulan punya lebih dari satu record, ambil yang terbaru (selesai) dan abaikan yang dibatalkan/draft.
     */
    public function buildAparYearMatrix(Apar $apar, int $tahun, array $columns): array
    {
        $startYear = (int) config('hafis.operational_start_year', 2026);
        $startMonth = (int) config('hafis.operational_start_month', 11);

        // Ambil pemeriksaan tahun tersebut (prioritaskan eager-loaded relation)
        if ($apar->relationLoaded('pemeriksaans')) {
            $pemeriksaans = $apar->pemeriksaans
                ->filter(function ($p) use ($tahun) {
                    return (int) $p->periode_tahun === $tahun && ! in_array($p->status, ['dibatalkan', 'draft']);
                });
        } else {
            $pemeriksaans = $apar->pemeriksaans()
                ->where('periode_tahun', $tahun)
                ->whereNotIn('status', ['dibatalkan', 'draft'])
                ->with('details')
                ->get();
        }

        // Kelompokkan berdasarkan periode_bulan
        $groupedByMonth = $pemeriksaans->groupBy('periode_bulan');

        $matrix = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $namaBulan = Pemeriksaan::NAMA_BULAN[$bulan] ?? "Bulan {$bulan}";
            $isPraDigitalisasi = ($tahun < $startYear) || ($tahun === $startYear && $bulan < $startMonth);

            $candidates = $groupedByMonth->get($bulan, collect());

            // Jika ada lebih dari satu record pada bulan yang sama, pilih record yang terbaru
            $pemeriksaan = null;
            if ($candidates->isNotEmpty()) {
                $pemeriksaan = $candidates->sortByDesc(function ($cand) {
                    $tgl = $cand->tanggal_pemeriksaan ? Carbon::parse($cand->tanggal_pemeriksaan)->timestamp : 0;

                    return sprintf('%010d_%010d', $tgl, $cand->id);
                })->first();
            }

            if (! $pemeriksaan) {
                // Bulan tanpa inspeksi selesai
                $cells = [];
                foreach ($columns as $col) {
                    $cells[$col['key']] = [
                        'symbol' => self::SYMBOL_KOSONG,
                        'hasil' => null,
                        'catatan' => null,
                    ];
                }

                $matrix[$bulan] = [
                    'bulan_nomor' => $bulan,
                    'bulan_nama' => $namaBulan,
                    'pemeriksaan_id' => null,
                    'has_pemeriksaan' => false,
                    'is_pra_digitalisasi' => $isPraDigitalisasi,
                    'pemeriksa_nama' => '-',
                    'tanggal_pemeriksaan' => null,
                    'tipe_penyelesaian' => null,
                    'is_terlambat' => false,
                    'catatan' => null,
                    'cells' => $cells,
                ];
            } else {
                // Pemeriksaan ditemukan
                $detailMap = [];
                if ($pemeriksaan->relationLoaded('details')) {
                    foreach ($pemeriksaan->details as $d) {
                        $detailMap[mb_strtolower(trim($d->item_nama))] = $d;
                    }
                } else {
                    foreach ($pemeriksaan->details()->get() as $d) {
                        $detailMap[mb_strtolower(trim($d->item_nama))] = $d;
                    }
                }

                $cells = [];
                foreach ($columns as $col) {
                    $colKey = $col['key'];
                    if (isset($detailMap[$colKey])) {
                        $detail = $detailMap[$colKey];
                        $symbol = self::SYMBOL_MAP[$detail->hasil] ?? self::SYMBOL_TIDAK_DIPERIKSA;
                        $cells[$colKey] = [
                            'symbol' => $symbol,
                            'hasil' => $detail->hasil,
                            'catatan' => $detail->catatan,
                        ];
                    } else {
                        // Tidak ada rincian item pada record inspeksi ini
                        $cells[$colKey] = [
                            'symbol' => self::SYMBOL_TIDAK_DIPERIKSA,
                            'hasil' => PemeriksaanDetail::HASIL_TIDAK_DIPERIKSA,
                            'catatan' => null,
                        ];
                    }
                }

                $isTerlambat = ($pemeriksaan->tipe_penyelesaian === Pemeriksaan::TIPE_TERLAMBAT || $pemeriksaan->tipe_penyelesaian === 'terlambat');
                $tglFormatted = $pemeriksaan->tanggal_pemeriksaan
                    ? Carbon::parse($pemeriksaan->tanggal_pemeriksaan)->format('d/m/Y')
                    : '-';

                $matrix[$bulan] = [
                    'bulan_nomor' => $bulan,
                    'bulan_nama' => $namaBulan,
                    'pemeriksaan_id' => $pemeriksaan->id,
                    'has_pemeriksaan' => true,
                    'is_pra_digitalisasi' => false,
                    'pemeriksa_nama' => $pemeriksaan->pemeriksa_nama ?: '-',
                    'tanggal_pemeriksaan' => $tglFormatted,
                    'tipe_penyelesaian' => $pemeriksaan->tipe_penyelesaian,
                    'is_terlambat' => $isTerlambat,
                    'catatan' => $pemeriksaan->catatan,
                    'cells' => $cells,
                ];
            }
        }

        return $matrix;
    }

    /**
     * Dapatkan metadata lengkap kepala kartu untuk satu unit APAR
     */
    public function getAparCardMeta(Apar $apar, int $tahun): array
    {
        $startYear = (int) config('hafis.operational_start_year', 2026);
        $isStok = ($apar->lokasi && $apar->lokasi->status === Lokasi::STATUS_STOK)
            || ($apar->status === Apar::STATUS_TIDAK_AKTIF && $apar->alasan_nonaktif === 'stok');

        $isExpired = $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->isPast() : false;

        $replacedByKode = null;
        $replacedAt = null;
        if ($apar->is_replaced) {
            $replacedByKode = $apar->aparPengganti?->kode ?? $apar->penggantianKeluar?->aparBaru?->kode;
            $tgl = $apar->penggantianKeluar?->tanggal_penggantian ?? $apar->tanggal_nonaktif;
            $replacedAt = $tgl ? Carbon::parse($tgl)->format('d/m/Y') : null;
        }

        return [
            'id' => $apar->id,
            'kode' => $apar->kode,
            'nomor_register' => $apar->nomor_register,
            'lokasi_lengkap' => $apar->lokasi_lengkap,
            'jenis_media' => $apar->jenis_media,
            'kapasitas' => $apar->kapasitas,
            'tanggal_kadaluarsa' => $apar->tanggal_kadaluarsa ? $apar->tanggal_kadaluarsa->format('d/m/Y') : '-',
            'is_expired' => $isExpired,
            'status' => $apar->status,
            'status_label' => $apar->status_label,
            'status_badge_variant' => $apar->status_badge_variant,
            'is_stok' => $isStok,
            'is_replaced' => $apar->is_replaced,
            'replaced_by_kode' => $replacedByKode,
            'replaced_at' => $replacedAt,
            'is_soft_deleted' => $apar->trashed(),
            'is_pra_digitalisasi_year' => ($tahun < $startYear),
        ];
    }

    /**
     * Ekspor CSV untuk 1 APAR (Single Card Format)
     */
    public function exportCsvSingle(Apar $apar, int $tahun, array $columns, array $matrix): StreamedResponse
    {
        $filename = "kartu-pemeliharaan-{$apar->kode}-{$tahun}.csv";
        $meta = $this->getAparCardMeta($apar, $tahun);

        return response()->streamDownload(function () use ($meta, $tahun, $columns, $matrix) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Blok Header Identitas Kartu
            fputcsv($handle, ['KARTU CEKLIST PEMELIHARAAN APAR (TAHUNAN)']);
            fputcsv($handle, ['RSUD dr. H. Moch. Ansari Saleh Banjarmasin']);
            fputcsv($handle, []);
            fputcsv($handle, ['Kode APAR', $meta['kode']]);
            fputcsv($handle, ['Nomor Register', $meta['nomor_register'] ?: '-']);
            fputcsv($handle, ['Lokasi / Ruangan', $meta['lokasi_lengkap']]);
            $kapasitasStr = str_ends_with(strtolower(trim((string) $meta['kapasitas'])), 'kg')
                ? trim((string) $meta['kapasitas'])
                : trim((string) $meta['kapasitas']).' kg';
            fputcsv($handle, ['Media & Kapasitas', $meta['jenis_media'].' ('.$kapasitasStr.')']);
            fputcsv($handle, ['Masa Berlaku Tabung', $meta['tanggal_kadaluarsa']]);
            fputcsv($handle, ['Tahun Inspeksi', $tahun]);

            if ($meta['is_replaced']) {
                fputcsv($handle, ['Status Pergantian', "Unit digantikan oleh {$meta['replaced_by_kode']} per tanggal {$meta['replaced_at']}"]);
            }
            if ($meta['is_stok']) {
                fputcsv($handle, ['Keterangan Penempatan', 'Unit berada di Stok Persediaan (Tidak Terpasang)']);
            }
            if ($meta['is_pra_digitalisasi_year']) {
                fputcsv($handle, ['Catatan Digitalisasi', 'Periode Pra-Digitalisasi HAFIS']);
            }
            fputcsv($handle, []);

            // Baris Judul Kolom Matriks
            $tableHeaders = ['No', 'Bulan'];
            foreach ($columns as $col) {
                $tableHeaders[] = $col['title'];
            }
            $tableHeaders[] = 'Petugas Pemeriksa';
            $tableHeaders[] = 'Tanggal Periksa';
            $tableHeaders[] = 'Status Inspeksi';
            $tableHeaders[] = 'Catatan';
            fputcsv($handle, $tableHeaders);

            // 12 Baris Bulan
            foreach ($matrix as $bulanNum => $row) {
                $line = [$bulanNum, $row['bulan_nama']];
                foreach ($columns as $col) {
                    $line[] = $row['cells'][$col['key']]['symbol'] ?? '';
                }
                $line[] = $row['pemeriksa_nama'];
                $line[] = $row['tanggal_pemeriksaan'] ?: '-';
                $line[] = $row['has_pemeriksaan'] ? ($row['is_terlambat'] ? 'Terlambat' : 'Tepat Waktu') : ($row['is_pra_digitalisasi'] ? 'Pra-Digitalisasi' : 'Belum Diperiksa');
                $line[] = $row['catatan'] ?: '-';
                fputcsv($handle, $line);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['LEGENDA SIMBOL:']);
            fputcsv($handle, [self::SYMBOL_BAIK, 'Kondisi Baik / Laik Pakai']);
            fputcsv($handle, [self::SYMBOL_TIDAK_BAIK, 'Kondisi Rusak / Perlu Tindakan']);
            fputcsv($handle, [self::SYMBOL_TIDAK_DIPERIKSA, 'Dilewati / Tidak Diperiksa']);
            fputcsv($handle, ['(Kosong)', 'Belum / Tidak Ada Jadwal Pemeriksaan']);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Ekspor CSV Massal (Batch Format)
     */
    public function exportCsvBatch(mixed $apars, int $tahun, array $columns): StreamedResponse
    {
        $filename = "rekap-kartu-pemeliharaan-massal-{$tahun}.csv";

        return response()->streamDownload(function () use ($apars, $tahun, $columns) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Header Tabel
            $headers = [
                'Kode APAR',
                'Nomor Register',
                'Lokasi Penempatan',
                'Media',
                'Kapasitas',
                'Masa Berlaku',
                'Tahun',
                'No Bulan',
                'Bulan',
            ];
            foreach ($columns as $col) {
                $headers[] = $col['title'];
            }
            $headers[] = 'Petugas';
            $headers[] = 'Tanggal Periksa';
            $headers[] = 'Status';
            $headers[] = 'Catatan';
            fputcsv($handle, $headers);

            foreach ($apars as $apar) {
                $matrix = $this->buildAparYearMatrix($apar, $tahun, $columns);
                $meta = $this->getAparCardMeta($apar, $tahun);

                foreach ($matrix as $bulanNum => $row) {
                    $line = [
                        $meta['kode'],
                        $meta['nomor_register'] ?: '-',
                        $meta['lokasi_lengkap'],
                        $meta['jenis_media'],
                        $meta['kapasitas'],
                        $meta['tanggal_kadaluarsa'],
                        $tahun,
                        $bulanNum,
                        $row['bulan_nama'],
                    ];
                    foreach ($columns as $col) {
                        $line[] = $row['cells'][$col['key']]['symbol'] ?? '';
                    }
                    $line[] = $row['pemeriksa_nama'];
                    $line[] = $row['tanggal_pemeriksaan'] ?: '-';
                    $line[] = $row['has_pemeriksaan'] ? ($row['is_terlambat'] ? 'Terlambat' : 'Tepat Waktu') : ($row['is_pra_digitalisasi'] ? 'Pra-Digitalisasi' : 'Belum Diperiksa');
                    $line[] = $row['catatan'] ?: '-';
                    fputcsv($handle, $line);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
