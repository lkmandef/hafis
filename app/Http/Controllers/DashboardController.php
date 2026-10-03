<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Pemeriksaan;
use App\Models\PenggantianApar;
use App\Models\TindakLanjut;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Monitoring APAR & Aktivitas Inspeksi Rumah Sakit (Tahap 10)
     */
    public function index()
    {
        $now = Carbon::now();

        // 1. Ringkasan Utama Dashboard (Step 2)
        $totalAparAktif = Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)->count();
        $totalAparPerhatian = Apar::where('status', Apar::STATUS_PERHATIAN)->count();
        $totalAparRusak = Apar::where('status', Apar::STATUS_RUSAK)->count();
        $totalAparAll = Apar::count();

        $pemeriksaanBulanIni = Pemeriksaan::whereMonth('tanggal_pemeriksaan', $now->month)
            ->whereYear('tanggal_pemeriksaan', $now->year)
            ->count();
        $pemeriksaanTotal = Pemeriksaan::count();

        $tindakLanjutBelumSelesai = TindakLanjut::where('status', '!=', TindakLanjut::STATUS_SELESAI)->count();

        $summaryStats = [
            'apar_aktif' => $totalAparAktif,
            'apar_perhatian' => $totalAparPerhatian,
            'apar_rusak' => $totalAparRusak,
            'pemeriksaan_bulan_ini' => $pemeriksaanBulanIni,
            'pemeriksaan_total' => $pemeriksaanTotal,
            'tl_belum_selesai' => $tindakLanjutBelumSelesai,
            'apar_total' => $totalAparAll,
            'periode_bulan' => $now->translatedFormat('F Y'),
        ];

        // 2. Status APAR Keseluruhan (Step 3)
        $kondisiCounts = [
            'baik' => Apar::where('status', Apar::STATUS_BAIK)->count(),
            'perhatian' => $totalAparPerhatian,
            'rusak' => $totalAparRusak,
            'tidak_aktif' => Apar::where('status', Apar::STATUS_TIDAK_AKTIF)->count(),
        ];

        $totalUntukPersen = max($totalAparAll, 1);
        $kondisiPersen = [
            'baik' => round(($kondisiCounts['baik'] / $totalUntukPersen) * 100),
            'perhatian' => round(($kondisiCounts['perhatian'] / $totalUntukPersen) * 100),
            'rusak' => round(($kondisiCounts['rusak'] / $totalUntukPersen) * 100),
            'tidak_aktif' => round(($kondisiCounts['tidak_aktif'] / $totalUntukPersen) * 100),
        ];

        // 3. Ringkasan Pemeriksaan & Riwayat Terkini (Step 4)
        $pemeriksaanStats = [
            'total' => $pemeriksaanTotal,
            'bulan_ini' => $pemeriksaanBulanIni,
            'baik' => Pemeriksaan::where('hasil_keseluruhan', Pemeriksaan::HASIL_BAIK)->count(),
            'perlu_tindakan' => Pemeriksaan::where('hasil_keseluruhan', Pemeriksaan::HASIL_PERLU_TINDAKAN)->count(),
        ];

        $pemeriksaanTerbaru = Pemeriksaan::with(['apar.ruangan.lantai.gedung', 'jadwal'])
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->take(5)
            ->get();

        // 4. Ringkasan Tindak Lanjut & Pemeliharaan (Step 5)
        $tindakLanjutStats = [
            'total' => TindakLanjut::count(),
            'belum' => TindakLanjut::where('status', TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI)->count(),
            'proses' => TindakLanjut::where('status', TindakLanjut::STATUS_DALAM_PROSES)->count(),
            'selesai' => TindakLanjut::where('status', TindakLanjut::STATUS_SELESAI)->count(),
        ];

        $tindakLanjutTerbaru = TindakLanjut::with(['apar.ruangan.lantai.gedung'])
            ->orderBy('tanggal_tindakan', 'desc')
            ->take(4)
            ->get();

        // 5. Ringkasan Jadwal Inspeksi (Step 6)
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

        // 6. Section Khusus: APAR Memerlukan Perhatian (Step 7)
        $aparPerluPerhatian = Apar::with(['ruangan.lantai.gedung', 'tindakLanjuts' => fn ($q) => $q->latest('tanggal_tindakan')])
            ->whereIn('status', [Apar::STATUS_PERHATIAN, Apar::STATUS_RUSAK])
            ->orderByRaw("CASE WHEN status = 'rusak' THEN 1 ELSE 2 END")
            ->take(5)
            ->get();

        // 7. Penggantian APAR Terbaru (Step 8)
        $penggantianTerbaru = PenggantianApar::with(['aparLama', 'aparBaru'])
            ->orderBy('tanggal_penggantian', 'desc')
            ->take(3)
            ->get();

        // 8. Ringkasan Lokasi Gedung (Step 9)
        $gedungRingkasan = Gedung::where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->take(5)
            ->get()
            ->map(function ($g) {
                $baseQuery = Apar::whereHas('ruangan.lantai', fn ($q) => $q->where('gedung_id', $g->id));

                return [
                    'id' => $g->id,
                    'nama' => $g->nama,
                    'kode' => $g->kode,
                    'total' => (clone $baseQuery)->count(),
                    'baik' => (clone $baseQuery)->where('status', Apar::STATUS_BAIK)->count(),
                    'perhatian' => (clone $baseQuery)->where('status', Apar::STATUS_PERHATIAN)->count(),
                    'rusak' => (clone $baseQuery)->where('status', Apar::STATUS_RUSAK)->count(),
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
            'jadwalData',
            'aparPerluPerhatian',
            'penggantianTerbaru',
            'gedungRingkasan'
        ));
    }
}
