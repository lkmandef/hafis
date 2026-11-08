<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\PenggantianApar;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Monitoring APAR & Aktivitas Inspeksi Rumah Sakit (Tahap 10)
     */
    public function index()
    {
        $now = Carbon::now();

        // 1. Ringkasan Utama Dashboard & Status APAR Terkonsolidasi (1 Kueri)
        $aparStatusCounts = Apar::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $totalAparBaik = (int) ($aparStatusCounts[Apar::STATUS_BAIK] ?? 0);
        $totalAparPerhatian = (int) ($aparStatusCounts[Apar::STATUS_PERHATIAN] ?? 0);
        $totalAparRusak = (int) ($aparStatusCounts[Apar::STATUS_RUSAK] ?? 0);
        $totalAparTidakAktif = (int) ($aparStatusCounts[Apar::STATUS_TIDAK_AKTIF] ?? 0);
        $totalAparAll = array_sum($aparStatusCounts);
        $totalAparAktif = $totalAparAll - $totalAparTidakAktif;

        // Tindak Lanjut Stats Terkonsolidasi (1 Kueri)
        $tindakLanjutCounts = TindakLanjut::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $tlBelum = (int) ($tindakLanjutCounts[TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI] ?? 0);
        $tlProses = (int) ($tindakLanjutCounts[TindakLanjut::STATUS_DALAM_PROSES] ?? 0);
        $tlSelesai = (int) ($tindakLanjutCounts[TindakLanjut::STATUS_SELESAI] ?? 0);
        $tlTotal = array_sum($tindakLanjutCounts);
        $tindakLanjutBelumSelesai = $tlBelum + $tlProses;

        $tindakLanjutStats = [
            'total' => $tlTotal,
            'belum' => $tlBelum,
            'proses' => $tlProses,
            'selesai' => $tlSelesai,
        ];

        // Pemeriksaan Stats Terkonsolidasi (1 Kueri Hasil + 1 Kueri Bulan Ini)
        $pemeriksaanHasilCounts = Pemeriksaan::selectRaw('hasil_keseluruhan, count(*) as count')
            ->groupBy('hasil_keseluruhan')
            ->pluck('count', 'hasil_keseluruhan')
            ->all();

        $pemeriksaanTotal = array_sum($pemeriksaanHasilCounts);
        $pemeriksaanBaik = (int) ($pemeriksaanHasilCounts[Pemeriksaan::HASIL_BAIK] ?? 0);
        $pemeriksaanPerluTindakan = (int) ($pemeriksaanHasilCounts[Pemeriksaan::HASIL_PERLU_TINDAKAN] ?? 0);

        $pemeriksaanBulanIni = Pemeriksaan::whereMonth('tanggal_pemeriksaan', $now->month)
            ->whereYear('tanggal_pemeriksaan', $now->year)
            ->count();

        $pemeriksaanStats = [
            'total' => $pemeriksaanTotal,
            'bulan_ini' => $pemeriksaanBulanIni,
            'baik' => $pemeriksaanBaik,
            'perlu_tindakan' => $pemeriksaanPerluTindakan,
        ];

        $namaBulanIndo = Pemeriksaan::NAMA_BULAN[(int) $now->month] ?? 'Oktober';
        $totalGedungAktif = Gedung::where('status', 'aktif')->count();
        $totalLokasiTerdaftar = Lokasi::count();

        $summaryStats = [
            'apar_aktif' => $totalAparAktif,
            'apar_perhatian' => $totalAparPerhatian,
            'apar_rusak' => $totalAparRusak,
            'pemeriksaan_bulan_ini' => $pemeriksaanBulanIni,
            'pemeriksaan_total' => $pemeriksaanTotal,
            'tl_belum_selesai' => $tindakLanjutBelumSelesai,
            'apar_total' => $totalAparAll,
            'total_gedung' => $totalGedungAktif,
            'total_lokasi' => $totalLokasiTerdaftar,
            'periode_bulan' => "{$namaBulanIndo} {$now->year}",
        ];

        // 2. Status APAR Keseluruhan
        $kondisiCounts = [
            'baik' => $totalAparBaik,
            'perhatian' => $totalAparPerhatian,
            'rusak' => $totalAparRusak,
            'tidak_aktif' => $totalAparTidakAktif,
        ];

        $totalUntukPersen = max($totalAparAll, 1);
        $kondisiPersen = [
            'baik' => round(($kondisiCounts['baik'] / $totalUntukPersen) * 100),
            'perhatian' => round(($kondisiCounts['perhatian'] / $totalUntukPersen) * 100),
            'rusak' => round(($kondisiCounts['rusak'] / $totalUntukPersen) * 100),
            'tidak_aktif' => round(($kondisiCounts['tidak_aktif'] / $totalUntukPersen) * 100),
        ];

        $pemeriksaanTerbaru = Pemeriksaan::with(['apar.lokasi', 'apar.ruangan.lantai.gedung', 'jadwal'])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->take(5)
            ->get();

        $tindakLanjutTerbaru = TindakLanjut::with(['apar.lokasi', 'apar.ruangan.lantai.gedung'])
            ->orderBy('tanggal_tindakan', 'desc')
            ->take(4)
            ->get();

        // 5. Progres Inspeksi Bulanan (Tahap 4)
        $tahunSekarang = (int) $now->year;
        $bulanSekarang = (int) $now->month;
        $namaBulanSekarang = Pemeriksaan::NAMA_BULAN[$bulanSekarang] ?? $now->translatedFormat('F');

        $eligibleAparFilter = fn ($q) => $q->where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
            ->whereNotNull('lokasi_id')
            ->whereDoesntHave('lokasi', fn ($loc) => $loc->where('status', Lokasi::STATUS_STOK));

        $totalTargetBulanan = Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
            ->whereNotNull('lokasi_id')
            ->whereDoesntHave('lokasi', fn ($q) => $q->where('status', Lokasi::STATUS_STOK))
            ->count();

        $sudahDiperiksaBulanan = Pemeriksaan::where('periode_tahun', $tahunSekarang)
            ->where('periode_bulan', $bulanSekarang)
            ->whereHas('apar', $eligibleAparFilter)
            ->distinct('apar_id')
            ->count('apar_id');

        $inspeksiHasilBulanan = Pemeriksaan::where('periode_tahun', $tahunSekarang)
            ->where('periode_bulan', $bulanSekarang)
            ->whereHas('apar', $eligibleAparFilter)
            ->selectRaw('hasil_keseluruhan, count(*) as count')
            ->groupBy('hasil_keseluruhan')
            ->pluck('count', 'hasil_keseluruhan')
            ->all();

        $baikBulanan = (int) ($inspeksiHasilBulanan[Pemeriksaan::HASIL_BAIK] ?? 0);
        $perluTindakanBulanan = (int) ($inspeksiHasilBulanan[Pemeriksaan::HASIL_PERLU_TINDAKAN] ?? 0);

        $belumDiperiksaBulanan = max(0, $totalTargetBulanan - $sudahDiperiksaBulanan);
        $progressPctBulanan = $totalTargetBulanan > 0 ? round(($sudahDiperiksaBulanan / $totalTargetBulanan) * 100) : 0;

        $hariDalamBulan = (int) $now->daysInMonth;
        $hariSekarang = (int) $now->day;
        $sisaHariBulanIni = max(0, $hariDalamBulan - $hariSekarang);

        $inspeksiBulanan = [
            'tahun' => $tahunSekarang,
            'bulan' => $bulanSekarang,
            'nama_bulan' => $namaBulanSekarang,
            'periode_label' => "{$namaBulanSekarang} {$tahunSekarang}",
            'tanggal_mulai' => 1,
            'tanggal_akhir' => $hariDalamBulan,
            'sisa_hari' => $sisaHariBulanIni,
            'rentang_label' => "1 – {$hariDalamBulan} {$namaBulanSekarang} {$tahunSekarang}",
            'total_target' => $totalTargetBulanan,
            'sudah_diperiksa' => $sudahDiperiksaBulanan,
            'belum_diperiksa' => $belumDiperiksaBulanan,
            'baik' => $baikBulanan,
            'perlu_tindakan' => $perluTindakanBulanan,
            'progress_pct' => $progressPctBulanan,
        ];

        // Kompatibilitas alur jadwal legacy jika masih ada komponen yang memerlukan
        $jadwalAktif = Jadwal::with(['apars'])
            ->whereIn('status', ['berjalan', 'belum_dimulai'])
            ->orderByRaw("CASE WHEN status = 'berjalan' THEN 1 ELSE 2 END")
            ->orderBy('tanggal_mulai', 'asc')
            ->first();

        $jadwalData = null;
        if ($jadwalAktif) {
            $totalTarget = $jadwalAktif->apars->count();
            $sudahDiperiksa = $jadwalAktif->apars->where('pivot.status_pemeriksaan', 'sudah_diperiksa')->count();
            $progressPct = $totalTarget > 0 ? round(($sudahDiperiksa / $totalTarget) * 100) : 0;

            $jadwalData = [
                'model' => $jadwalAktif,
                'total_target' => $totalTarget,
                'sudah_diperiksa' => $sudahDiperiksa,
                'progress_pct' => $progressPct,
            ];
        }

        // 6. Section Khusus: Seluruh APAR Memerlukan Perhatian & Rusak (Berdasarkan Prioritas)
        $aparPerluPerhatian = Apar::with([
            'lokasi.gedung',
            'lokasi.lantai',
            'ruangan.lantai.gedung',
            'tindakLanjuts' => fn ($q) => $q->latest('tanggal_tindakan'),
        ])
            ->whereIn('status', [Apar::STATUS_PERHATIAN, Apar::STATUS_RUSAK])
            ->orderByRaw("CASE WHEN status = 'rusak' THEN 1 ELSE 2 END")
            ->orderBy('updated_at', 'desc')
            ->get();

        // 7. Penggantian APAR Terbaru (Step 8)
        $penggantianTerbaru = PenggantianApar::with(['aparLama', 'aparBaru'])
            ->orderBy('tanggal_penggantian', 'desc')
            ->take(3)
            ->get();

        // 8. Ringkasan Lokasi Gedung (Step 9) - 1 Kueri Agregasi Terkonsolidasi
        $topGedungs = Gedung::where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->take(5)
            ->get(['id', 'nama', 'kode']);

        $topGedungIds = $topGedungs->pluck('id')->all();

        $gedungCounts = DB::table('apars')
            ->leftJoin('lokasis', 'apars.lokasi_id', '=', 'lokasis.id')
            ->leftJoin('ruangans', 'apars.ruangan_id', '=', 'ruangans.id')
            ->leftJoin('lantais', 'ruangans.lantai_id', '=', 'lantais.id')
            ->selectRaw('COALESCE(lokasis.gedung_id, lantais.gedung_id) as gedung_id, apars.status, count(*) as count')
            ->whereIn(DB::raw('COALESCE(lokasis.gedung_id, lantais.gedung_id)'), $topGedungIds)
            ->whereNull('apars.deleted_at')
            ->groupBy(DB::raw('COALESCE(lokasis.gedung_id, lantais.gedung_id)'), 'apars.status')
            ->get();

        $gedungStatsMap = [];
        foreach ($gedungCounts as $row) {
            $gId = (int) $row->gedung_id;
            $gedungStatsMap[$gId][$row->status] = (int) $row->count;
        }

        $gedungRingkasan = $topGedungs->map(function ($g) use ($gedungStatsMap) {
            $stats = $gedungStatsMap[$g->id] ?? [];
            $baik = $stats[Apar::STATUS_BAIK] ?? 0;
            $perhatian = $stats[Apar::STATUS_PERHATIAN] ?? 0;
            $rusak = $stats[Apar::STATUS_RUSAK] ?? 0;
            $total = array_sum($stats);

            return [
                'id' => $g->id,
                'nama' => $g->nama,
                'kode' => $g->kode,
                'total' => $total,
                'baik' => $baik,
                'perhatian' => $perhatian,
                'rusak' => $rusak,
            ];
        });

        return view('dashboard', compact(
            'summaryStats',
            'kondisiCounts',
            'kondisiPersen',
            'pemeriksaanStats',
            'pemeriksaanTerbaru',
            'tindakLanjutStats',
            'tindakLanjutTerbaru',
            'inspeksiBulanan',
            'jadwalData',
            'aparPerluPerhatian',
            'penggantianTerbaru',
            'gedungRingkasan'
        ));
    }
}
