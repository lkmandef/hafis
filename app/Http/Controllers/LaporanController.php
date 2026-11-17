<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\PenggantianApar;
use App\Models\TindakLanjut;
use App\Services\KartuPemeliharaanService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Hub utama menu Laporan & Rekapitulasi (Section 8)
     */
    public function index()
    {
        $stats = [
            // Laporan 1: Data APAR
            'apar_total' => Apar::count(),
            'apar_aktif' => Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)->count(),
            'apar_rusak' => Apar::where('status', Apar::STATUS_RUSAK)->count(),
            'apar_perhatian' => Apar::where('status', Apar::STATUS_PERHATIAN)->count(),

            // Laporan 2: Pemeriksaan APAR
            'pemeriksaan_total' => Pemeriksaan::count(),
            'pemeriksaan_baik' => Pemeriksaan::where('hasil_keseluruhan', Pemeriksaan::HASIL_BAIK)->count(),
            'pemeriksaan_perlu_tindakan' => Pemeriksaan::where('hasil_keseluruhan', Pemeriksaan::HASIL_PERLU_TINDAKAN)->count(),

            // Laporan 3: Temuan Pemeriksaan
            'temuan_total' => PemeriksaanDetail::where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK)->count(),
            'temuan_belum_selesai' => PemeriksaanDetail::where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK)
                ->where(function ($q) {
                    $q->doesntHave('tindakLanjuts')
                        ->orWhereHas('tindakLanjuts', fn ($tq) => $tq->where('status', '!=', TindakLanjut::STATUS_SELESAI));
                })->count(),

            // Laporan 4: Tindak Lanjut & Pemeliharaan
            'tindak_lanjut_total' => TindakLanjut::count(),
            'tindak_lanjut_proses' => TindakLanjut::where('status', TindakLanjut::STATUS_DALAM_PROSES)->count(),
            'tindak_lanjut_selesai' => TindakLanjut::where('status', TindakLanjut::STATUS_SELESAI)->count(),

            // Laporan 5: Penggantian APAR
            'penggantian_total' => PenggantianApar::count(),
            'penggantian_rusak' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_RUSAK)->count(),
        ];

        return view('laporan.index', compact('stats'));
    }

    /**
     * LAPORAN 1: Laporan Data APAR (Section 3)
     */
    public function apar(Request $request)
    {
        $query = Apar::with(['lokasi', 'ruangan.lantai.gedung', 'aparPengganti', 'aparDigantikan']);

        // 1. Filter Lokasi (Prioritas: Lokasi K3RS, dengan fallback hierarki gedung & lantai)
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        } elseif ($request->filled('gedung_id')) {
            $query->diGedung($request->gedung_id);
            if ($request->filled('lantai_id')) {
                $query->diLantai($request->lantai_id);
            }
        }
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        // 2. Filter Status Penggunaan
        $status = $request->input('status');
        if ($status === 'aktif') {
            $query->where('status', '!=', Apar::STATUS_TIDAK_AKTIF);
        } elseif ($status === 'tidak_aktif') {
            $query->where('status', Apar::STATUS_TIDAK_AKTIF)->whereNull('replaced_by_apar_id');
        } elseif ($status === 'sudah_diganti') {
            $query->where('status', Apar::STATUS_TIDAK_AKTIF)->whereNotNull('replaced_by_apar_id');
        }

        // 3. Filter Kondisi Fisik
        if ($request->filled('kondisi')) {
            $query->where('status', $request->kondisi);
        }

        // 4. Filter Jenis Media
        if ($request->filled('jenis_media')) {
            $query->where('jenis_media', $request->jenis_media);
        }

        // 5. Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('kode', 'like', "%{$q}%")
                    ->orWhere('qr_code', 'like', "%{$q}%")
                    ->orWhere('nomor_register', 'like', "%{$q}%")
                    ->orWhere('merek', 'like', "%{$q}%")
                    ->orWhere('tipe_model', 'like', "%{$q}%")
                    ->orWhere('posisi_detail', 'like', "%{$q}%")
                    ->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
            });
        }

        $query->orderBy('kode', 'asc');

        // Rekap riil database
        $rekap = [
            'total' => Apar::count(),
            'aktif' => Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)->count(),
            'tidak_aktif' => Apar::where('status', Apar::STATUS_TIDAK_AKTIF)->count(),
            'perhatian' => Apar::where('status', Apar::STATUS_PERHATIAN)->count(),
            'rusak' => Apar::where('status', Apar::STATUS_RUSAK)->count(),
            'terfilter' => (clone $query)->count(),
        ];

        // Format CSV Export
        if ($request->input('format') === 'csv') {
            $rows = (clone $query)->get()->map(function ($a, $i) {
                return [
                    $i + 1,
                    $a->kode,
                    $a->nomor_register ?: '-',
                    $a->qr_code,
                    $a->jenis_media,
                    $a->kapasitas,
                    $a->merek ?: 'Belum terdata',
                    $a->tipe_model ?: '-',
                    $a->gedung?->nama ?? '-',
                    $a->lantai?->nama ?? '-',
                    $a->lokasi?->nama ?? $a->ruangan?->nama ?? '-',
                    $a->posisi_detail ?: '-',
                    $a->status_label,
                    $a->tanggal_kadaluarsa ? $a->tanggal_kadaluarsa->format('d/m/Y') : '-',
                    $a->tanggal_pemasangan ? $a->tanggal_pemasangan->format('d/m/Y') : '-',
                ];
            });

            $headers = [
                'No', 'Kode APAR', 'Nomor Register', 'QR Code', 'Jenis Media', 'Kapasitas',
                'Merek', 'Tipe Model', 'Gedung', 'Lantai', 'Ruangan', 'Posisi Detail',
                'Status Operasional', 'Tgl Kadaluarsa', 'Tgl Pemasangan',
            ];

            return $this->exportCsv('laporan-data-apar-'.date('Ymd-His').'.csv', $headers, $rows);
        }

        // Format Print View
        if ($request->input('format') === 'print') {
            $apars = (clone $query)->get();
            $filtersSummary = $this->buildFilterSummary($request);

            return view('laporan.apar', [
                'apars' => $apars,
                'rekap' => $rekap,
                'isPrint' => true,
                'filtersSummary' => $filtersSummary,
                'lokasis' => collect(),
                'gedungs' => collect(),
                'lantais' => collect(),
            ]);
        }

        // Web View (Paginated)
        $apars = $query->paginate(15)->withQueryString();
        $lokasis = Lokasi::where('status', '!=', Lokasi::STATUS_STOK)->orderBy('nama')->get();
        $gedungs = Gedung::orderBy('nama', 'asc')->get();
        $lantais = $request->filled('gedung_id')
            ? Lantai::where('gedung_id', $request->gedung_id)->orderBy('nomor_lantai', 'asc')->get()
            : collect();

        return view('laporan.apar', [
            'apars' => $apars,
            'rekap' => $rekap,
            'isPrint' => false,
            'filtersSummary' => [],
            'lokasis' => $lokasis,
            'gedungs' => $gedungs,
            'lantais' => $lantais,
        ]);
    }

    /**
     * LAPORAN 2: Laporan Riwayat Pemeriksaan APAR (Section 4)
     */
    public function pemeriksaan(Request $request)
    {
        $query = Pemeriksaan::with(['apar.lokasi', 'apar.ruangan.lantai.gedung', 'jadwal', 'user', 'pemeriksaanInduk']);

        // 1. Filter Periode Tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_pemeriksaan', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_pemeriksaan', '<=', $request->sampai_tanggal);
        }

        // 2. Filter Lokasi APAR (Prioritas: Lokasi K3RS, dengan hierarki gedung/lantai terpadu)
        if ($request->filled('lokasi_id')) {
            $query->whereHas('apar', fn ($q) => $q->where('lokasi_id', $request->lokasi_id));
        } elseif ($request->filled('gedung_id')) {
            $query->whereHas('apar', fn ($q) => $q->diGedung($request->gedung_id));
            if ($request->filled('lantai_id')) {
                $query->whereHas('apar', fn ($q) => $q->diLantai($request->lantai_id));
            }
        }
        if ($request->filled('ruangan_id')) {
            $query->whereHas('apar', fn ($q) => $q->where('ruangan_id', $request->ruangan_id));
        }

        // 3. Filter Hasil Pemeriksaan
        if ($request->filled('hasil')) {
            $query->where('hasil_keseluruhan', $request->hasil);
        }

        // 4. Filter Tipe Penyelesaian / Kepatuhan
        if ($request->filled('tipe_penyelesaian')) {
            $query->where('tipe_penyelesaian', $request->tipe_penyelesaian);
        }

        // 5. Filter Jadwal
        if ($request->filled('jadwal_id')) {
            if ($request->jadwal_id === 'mandiri') {
                $query->whereNull('jadwal_id');
            } else {
                $query->where('jadwal_id', $request->jadwal_id);
            }
        }

        // 6. Filter Pemeriksa
        if ($request->filled('pemeriksa')) {
            $query->where('pemeriksa_nama', 'like', "%{$request->pemeriksa}%");
        }

        // 7. Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('catatan', 'like', "%{$q}%")
                    ->orWhere('pemeriksa_nama', 'like', "%{$q}%")
                    ->orWhereHas('apar', fn ($aq) => $aq->where('kode', 'like', "%{$q}%")->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$q}%")));
            });
        }

        $query->orderBy('tanggal_pemeriksaan', 'desc');

        $rekap = [
            'total' => Pemeriksaan::count(),
            'aktivitas_fisik' => Pemeriksaan::aktivitasFisik()->count(),
            'tepat_waktu' => Pemeriksaan::where('tipe_penyelesaian', Pemeriksaan::TIPE_TEPAT_WAKTU)->count(),
            'terlambat' => Pemeriksaan::where('tipe_penyelesaian', Pemeriksaan::TIPE_TERLAMBAT)->count(),
            'baik' => Pemeriksaan::where('hasil_keseluruhan', Pemeriksaan::HASIL_BAIK)->count(),
            'perlu_tindakan' => Pemeriksaan::where('hasil_keseluruhan', Pemeriksaan::HASIL_PERLU_TINDAKAN)->count(),
            'terjadwal' => Pemeriksaan::whereNotNull('jadwal_id')->count(),
            'mandiri' => Pemeriksaan::whereNull('jadwal_id')->count(),
            'terfilter' => (clone $query)->count(),
        ];

        // Format CSV Export
        if ($request->input('format') === 'csv') {
            $rows = (clone $query)->get()->map(function ($p, $i) {
                return [
                    $i + 1,
                    $p->tanggal_pemeriksaan ? $p->tanggal_pemeriksaan->format('d/m/Y H:i') : '-',
                    $p->periode_label,
                    $p->tipe_penyelesaian_label.($p->isLinkedRecord() ? " (Resolusi #{$p->pemeriksaan_induk_id})" : ''),
                    $p->isLinkedRecord() ? 'Resolusi Multi-Periode' : 'Inspeksi Fisik Langsung',
                    $p->apar?->kode ?? '-',
                    $p->apar?->lokasi?->nama ?? $p->apar?->lokasi_lengkap ?? '-',
                    $p->jadwal?->nama ?? 'Pemeriksaan Mandiri (Ad-Hoc)',
                    $p->pemeriksa_nama,
                    $p->hasil_label,
                    $p->catatan ?: '-',
                    ucfirst($p->status ?? 'selesai'),
                ];
            });

            $headers = [
                'No', 'Tanggal Pemeriksaan Fisik', 'Periode Kepatuhan', 'Tipe Kepatuhan', 'Kategori Log',
                'Kode APAR', 'Lokasi', 'Jadwal Inspeksi', 'Pemeriksa', 'Hasil Keseluruhan', 'Catatan', 'Status',
            ];

            return $this->exportCsv('laporan-pemeriksaan-apar-'.date('Ymd-His').'.csv', $headers, $rows);
        }

        // Format Print View
        if ($request->input('format') === 'print') {
            $pemeriksaans = (clone $query)->get();
            $filtersSummary = $this->buildFilterSummary($request);

            return view('laporan.pemeriksaan', [
                'pemeriksaans' => $pemeriksaans,
                'rekap' => $rekap,
                'isPrint' => true,
                'filtersSummary' => $filtersSummary,
                'lokasis' => collect(),
                'gedungs' => collect(),
                'lantais' => collect(),
                'jadwals' => collect(),
            ]);
        }

        // Web View
        $pemeriksaans = $query->paginate(15)->withQueryString();
        $lokasis = Lokasi::where('status', '!=', Lokasi::STATUS_STOK)->orderBy('nama')->get();
        $gedungs = Gedung::orderBy('nama', 'asc')->get();
        $lantais = $request->filled('gedung_id')
            ? Lantai::where('gedung_id', $request->gedung_id)->orderBy('nomor_lantai', 'asc')->get()
            : collect();
        $jadwals = Jadwal::orderBy('tanggal_mulai', 'desc')->take(15)->get();

        return view('laporan.pemeriksaan', [
            'pemeriksaans' => $pemeriksaans,
            'rekap' => $rekap,
            'isPrint' => false,
            'filtersSummary' => [],
            'lokasis' => $lokasis,
            'gedungs' => $gedungs,
            'lantais' => $lantais,
            'jadwals' => $jadwals,
        ]);
    }

    /**
     * LAPORAN 3: Laporan Temuan Pemeriksaan Fisik (Section 5)
     */
    public function temuan(Request $request)
    {
        $query = PemeriksaanDetail::with(['pemeriksaan.apar.lokasi', 'pemeriksaan.apar.ruangan.lantai.gedung', 'tindakLanjuts'])
            ->where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK);

        // 1. Filter Periode Tanggal Pemeriksaan
        if ($request->filled('dari_tanggal')) {
            $query->whereHas('pemeriksaan', fn ($q) => $q->whereDate('tanggal_pemeriksaan', '>=', $request->dari_tanggal));
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereHas('pemeriksaan', fn ($q) => $q->whereDate('tanggal_pemeriksaan', '<=', $request->sampai_tanggal));
        }

        // 2. Filter Lokasi APAR (Prioritas: Lokasi K3RS, dengan hierarki gedung/lantai terpadu)
        if ($request->filled('lokasi_id')) {
            $query->whereHas('pemeriksaan.apar', fn ($q) => $q->where('lokasi_id', $request->lokasi_id));
        } elseif ($request->filled('gedung_id')) {
            $query->whereHas('pemeriksaan.apar', fn ($q) => $q->diGedung($request->gedung_id));
            if ($request->filled('lantai_id')) {
                $query->whereHas('pemeriksaan.apar', fn ($q) => $q->diLantai($request->lantai_id));
            }
        }
        if ($request->filled('ruangan_id')) {
            $query->whereHas('pemeriksaan.apar', fn ($q) => $q->where('ruangan_id', $request->ruangan_id));
        }

        // 3. Filter Status Tindak Lanjut
        if ($request->filled('status_tl')) {
            $stl = $request->status_tl;
            if ($stl === 'belum_ada_tl') {
                $query->doesntHave('tindakLanjuts');
            } elseif ($stl === 'selesai') {
                $query->whereHas('tindakLanjuts', fn ($tq) => $tq->where('status', TindakLanjut::STATUS_SELESAI));
            } elseif ($stl === 'dalam_proses') {
                $query->whereHas('tindakLanjuts', fn ($tq) => $tq->where('status', TindakLanjut::STATUS_DALAM_PROSES));
            } elseif ($stl === 'belum_ditindaklanjuti') {
                $query->where(function ($q) {
                    $q->doesntHave('tindakLanjuts')
                        ->orWhereHas('tindakLanjuts', fn ($tq) => $tq->where('status', TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI));
                });
            }
        }

        // 4. Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('item_nama', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%")
                    ->orWhereHas('pemeriksaan', function ($pq) use ($q) {
                        $pq->where('pemeriksa_nama', 'like', "%{$q}%")
                            ->orWhereHas('apar', fn ($aq) => $aq->where('kode', 'like', "%{$q}%")->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$q}%")));
                    });
            });
        }

        $query->orderBy('created_at', 'desc');

        $rekap = [
            'total' => PemeriksaanDetail::where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK)->count(),
            'selesai' => PemeriksaanDetail::where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK)
                ->whereHas('tindakLanjuts', fn ($q) => $q->where('status', TindakLanjut::STATUS_SELESAI))->count(),
            'dalam_proses' => PemeriksaanDetail::where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK)
                ->whereHas('tindakLanjuts', fn ($q) => $q->where('status', TindakLanjut::STATUS_DALAM_PROSES))->count(),
            'belum_ditindaklanjuti' => PemeriksaanDetail::where('hasil', PemeriksaanDetail::HASIL_TIDAK_BAIK)
                ->where(function ($q) {
                    $q->doesntHave('tindakLanjuts')
                        ->orWhereHas('tindakLanjuts', fn ($tq) => $tq->where('status', TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI));
                })->count(),
            'terfilter' => (clone $query)->count(),
        ];

        // Format CSV Export
        if ($request->input('format') === 'csv') {
            $rows = (clone $query)->get()->map(function ($d, $i) {
                $tl = $d->tindakLanjut;
                $statusTl = $tl ? $tl->status_label : 'Belum Dibuat Tindak Lanjut';

                return [
                    $i + 1,
                    $d->pemeriksaan?->tanggal_pemeriksaan ? $d->pemeriksaan->tanggal_pemeriksaan->format('d/m/Y') : '-',
                    $d->pemeriksaan?->apar?->kode ?? '-',
                    $d->pemeriksaan?->apar?->lokasi?->nama ?? $d->pemeriksaan?->apar?->lokasi_lengkap ?? '-',
                    $d->item_nama,
                    'Tidak Baik',
                    $d->catatan ?: '-',
                    $d->pemeriksaan?->pemeriksa_nama ?? '-',
                    $statusTl,
                ];
            });

            $headers = [
                'No', 'Tanggal Temuan', 'Kode APAR', 'Lokasi', 'Item Parameter',
                'Hasil', 'Catatan Temuan Masalah', 'Pemeriksa', 'Status Tindak Lanjut',
            ];

            return $this->exportCsv('laporan-temuan-pemeriksaan-'.date('Ymd-His').'.csv', $headers, $rows);
        }

        // Format Print View
        if ($request->input('format') === 'print') {
            $temuans = (clone $query)->get();
            $filtersSummary = $this->buildFilterSummary($request);

            return view('laporan.temuan', [
                'temuans' => $temuans,
                'rekap' => $rekap,
                'isPrint' => true,
                'filtersSummary' => $filtersSummary,
                'lokasis' => collect(),
                'gedungs' => collect(),
                'lantais' => collect(),
            ]);
        }

        // Web View
        $temuans = $query->paginate(15)->withQueryString();
        $lokasis = Lokasi::where('status', '!=', Lokasi::STATUS_STOK)->orderBy('nama')->get();
        $gedungs = Gedung::orderBy('nama', 'asc')->get();
        $lantais = $request->filled('gedung_id')
            ? Lantai::where('gedung_id', $request->gedung_id)->orderBy('nomor_lantai', 'asc')->get()
            : collect();

        return view('laporan.temuan', [
            'temuans' => $temuans,
            'rekap' => $rekap,
            'isPrint' => false,
            'filtersSummary' => [],
            'lokasis' => $lokasis,
            'gedungs' => $gedungs,
            'lantais' => $lantais,
        ]);
    }

    /**
     * LAPORAN 4: Laporan Tindak Lanjut & Pemeliharaan APAR (Section 6)
     */
    public function tindakLanjut(Request $request)
    {
        $query = TindakLanjut::with(['apar.lokasi', 'apar.ruangan.lantai.gedung', 'pemeriksaan']);

        // 1. Filter Periode Tanggal Tindakan
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_tindakan', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_tindakan', '<=', $request->sampai_tanggal);
        }

        // 2. Filter Lokasi APAR (Prioritas: Lokasi K3RS, dengan hierarki gedung/lantai terpadu)
        if ($request->filled('lokasi_id')) {
            $query->whereHas('apar', fn ($q) => $q->where('lokasi_id', $request->lokasi_id));
        } elseif ($request->filled('gedung_id')) {
            $query->whereHas('apar', fn ($q) => $q->diGedung($request->gedung_id));
            if ($request->filled('lantai_id')) {
                $query->whereHas('apar', fn ($q) => $q->diLantai($request->lantai_id));
            }
        }
        if ($request->filled('ruangan_id')) {
            $query->whereHas('apar', fn ($q) => $q->where('ruangan_id', $request->ruangan_id));
        }

        // 3. Filter Jenis Tindakan
        if ($request->filled('jenis_tindakan')) {
            $query->where('jenis_tindakan', $request->jenis_tindakan);
        }

        // 4. Filter Status Tindakan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 5. Filter Petugas Pelaksana
        if ($request->filled('petugas_pelaksana')) {
            $query->where('petugas_pelaksana', 'like', "%{$request->petugas_pelaksana}%");
        }

        // 6. Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('deskripsi_temuan', 'like', "%{$q}%")
                    ->orWhere('uraian_tindakan', 'like', "%{$q}%")
                    ->orWhere('petugas_pelaksana', 'like', "%{$q}%")
                    ->orWhereHas('apar', fn ($aq) => $aq->where('kode', 'like', "%{$q}%")->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$q}%")));
            });
        }

        $query->orderBy('tanggal_tindakan', 'desc');

        $rekap = [
            'total' => TindakLanjut::count(),
            'belum_ditindaklanjuti' => TindakLanjut::where('status', TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI)->count(),
            'dalam_proses' => TindakLanjut::where('status', TindakLanjut::STATUS_DALAM_PROSES)->count(),
            'selesai' => TindakLanjut::where('status', TindakLanjut::STATUS_SELESAI)->count(),
            'dibatalkan' => TindakLanjut::where('status', TindakLanjut::STATUS_DIBATALKAN)->count(),
            'terfilter' => (clone $query)->count(),
        ];

        // Format CSV Export
        if ($request->input('format') === 'csv') {
            $rows = (clone $query)->get()->map(function ($tl, $i) {
                return [
                    $i + 1,
                    $tl->tanggal_tindakan ? $tl->tanggal_tindakan->format('d/m/Y') : '-',
                    $tl->apar?->kode ?? '-',
                    $tl->apar?->lokasi?->nama ?? $tl->apar?->lokasi_lengkap ?? '-',
                    $tl->pemeriksaan ? 'Inspeksi '.$tl->pemeriksaan->tanggal_pemeriksaan?->format('d/m/Y') : 'Mandiri',
                    $tl->deskripsi_temuan ?: '-',
                    $tl->jenis_label,
                    $tl->uraian_tindakan ?: '-',
                    $tl->petugas_pelaksana,
                    $tl->status_label,
                    $tl->tanggal_selesai ? $tl->tanggal_selesai->format('d/m/Y') : '-',
                    $tl->catatan ?: '-',
                ];
            });

            $headers = [
                'No', 'Tanggal Tindakan', 'Kode APAR', 'Lokasi', 'Sumber Pemeriksaan',
                'Temuan Masalah', 'Jenis Tindakan', 'Uraian Tindakan', 'Pelaksana',
                'Status', 'Tanggal Selesai', 'Catatan',
            ];

            return $this->exportCsv('laporan-tindak-lanjut-pemeliharaan-'.date('Ymd-His').'.csv', $headers, $rows);
        }

        // Format Print View
        if ($request->input('format') === 'print') {
            $tindakLanjuts = (clone $query)->get();
            $filtersSummary = $this->buildFilterSummary($request);

            return view('laporan.tindak_lanjut', [
                'tindakLanjuts' => $tindakLanjuts,
                'rekap' => $rekap,
                'isPrint' => true,
                'filtersSummary' => $filtersSummary,
                'lokasis' => collect(),
                'gedungs' => collect(),
                'lantais' => collect(),
            ]);
        }

        // Web View
        $tindakLanjuts = $query->paginate(15)->withQueryString();
        $lokasis = Lokasi::where('status', '!=', Lokasi::STATUS_STOK)->orderBy('nama')->get();
        $gedungs = Gedung::orderBy('nama', 'asc')->get();
        $lantais = $request->filled('gedung_id')
            ? Lantai::where('gedung_id', $request->gedung_id)->orderBy('nomor_lantai', 'asc')->get()
            : collect();

        return view('laporan.tindak_lanjut', [
            'tindakLanjuts' => $tindakLanjuts,
            'rekap' => $rekap,
            'isPrint' => false,
            'filtersSummary' => [],
            'lokasis' => $lokasis,
            'gedungs' => $gedungs,
            'lantais' => $lantais,
        ]);
    }

    /**
     * LAPORAN 5: Laporan Penggantian APAR (Section 7)
     */
    public function penggantian(Request $request)
    {
        $query = PenggantianApar::with([
            'aparLama.lokasi',
            'aparLama.ruangan.lantai.gedung',
            'aparBaru.lokasi',
            'aparBaru.ruangan.lantai.gedung',
            'user',
        ]);

        // 1. Filter Periode Tanggal Penggantian
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_penggantian', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_penggantian', '<=', $request->sampai_tanggal);
        }

        // 2. Filter Lokasi APAR Baru / Lama (Prioritas: Lokasi K3RS, dengan hierarki gedung/lantai terpadu)
        if ($request->filled('lokasi_id')) {
            $locId = $request->lokasi_id;
            $query->where(function ($sub) use ($locId) {
                $sub->whereHas('aparBaru', fn ($q) => $q->where('lokasi_id', $locId))
                    ->orWhereHas('aparLama', fn ($q) => $q->where('lokasi_id', $locId));
            });
        } elseif ($request->filled('gedung_id')) {
            $query->where(function ($sub) use ($request) {
                $sub->whereHas('aparBaru', fn ($q) => $q->diGedung($request->gedung_id))
                    ->orWhereHas('aparLama', fn ($q) => $q->diGedung($request->gedung_id));
            });
            if ($request->filled('lantai_id')) {
                $query->where(function ($sub) use ($request) {
                    $sub->whereHas('aparBaru', fn ($q) => $q->diLantai($request->lantai_id))
                        ->orWhereHas('aparLama', fn ($q) => $q->diLantai($request->lantai_id));
                });
            }
        }
        if ($request->filled('ruangan_id')) {
            $query->where(function ($sub) use ($request) {
                $sub->whereHas('aparBaru', fn ($q) => $q->where('ruangan_id', $request->ruangan_id))
                    ->orWhereHas('aparLama', fn ($q) => $q->where('ruangan_id', $request->ruangan_id));
            });
        }

        // 3. Filter Alasan Penggantian
        if ($request->filled('alasan')) {
            $query->where('alasan', $request->alasan);
        }

        // 4. Pencarian teks
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('petugas_nama', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%")
                    ->orWhere('alasan_keterangan', 'like', "%{$q}%")
                    ->orWhereHas('aparLama', fn ($aq) => $aq->where('kode', 'like', "%{$q}%")->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$q}%")))
                    ->orWhereHas('aparBaru', fn ($aq) => $aq->where('kode', 'like', "%{$q}%")->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$q}%")));
            });
        }

        $query->orderBy('tanggal_penggantian', 'desc');

        $rekap = [
            'total' => PenggantianApar::count(),
            'rusak' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_RUSAK)->count(),
            'kadaluarsa' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_KADALUARSA)->count(),
            'peremajaan' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_PENGGANTIAN_UNIT)->count(),
            'terfilter' => (clone $query)->count(),
        ];

        // Format CSV Export
        if ($request->input('format') === 'csv') {
            $rows = (clone $query)->get()->map(function ($p, $i) {
                return [
                    $i + 1,
                    $p->tanggal_penggantian ? $p->tanggal_penggantian->format('d/m/Y') : '-',
                    $p->aparLama?->kode ?? '-',
                    $p->aparLama?->jenis_media ?? '-',
                    $p->aparLama?->kapasitas ?? '-',
                    $p->aparBaru?->kode ?? '-',
                    $p->aparBaru?->jenis_media ?? '-',
                    $p->aparBaru?->kapasitas ?? '-',
                    $p->aparBaru?->lokasi?->nama ?? $p->aparBaru?->lokasi_lengkap ?? $p->aparLama?->lokasi?->nama ?? $p->aparLama?->lokasi_lengkap ?? '-',
                    $p->alasan_label.($p->alasan_keterangan ? " ({$p->alasan_keterangan})" : ''),
                    $p->petugas_nama,
                    $p->catatan ?: '-',
                ];
            });

            $headers = [
                'No', 'Tanggal Penggantian', 'Kode APAR Lama', 'Media Lama', 'Kapasitas Lama',
                'Kode APAR Baru (Pengganti)', 'Media Baru', 'Kapasitas Baru', 'Lokasi Penempatan',
                'Alasan Penggantian', 'Petugas', 'Catatan',
            ];

            return $this->exportCsv('laporan-penggantian-apar-'.date('Ymd-His').'.csv', $headers, $rows);
        }

        // Format Print View
        if ($request->input('format') === 'print') {
            $penggantians = (clone $query)->get();
            $filtersSummary = $this->buildFilterSummary($request);

            return view('laporan.penggantian', [
                'penggantians' => $penggantians,
                'rekap' => $rekap,
                'isPrint' => true,
                'filtersSummary' => $filtersSummary,
                'lokasis' => collect(),
                'gedungs' => collect(),
                'lantais' => collect(),
            ]);
        }

        // Web View
        $penggantians = $query->paginate(15)->withQueryString();
        $lokasis = Lokasi::where('status', '!=', Lokasi::STATUS_STOK)->orderBy('nama')->get();
        $gedungs = Gedung::orderBy('nama', 'asc')->get();
        $lantais = $request->filled('gedung_id')
            ? Lantai::where('gedung_id', $request->gedung_id)->orderBy('nomor_lantai', 'asc')->get()
            : collect();

        return view('laporan.penggantian', [
            'penggantians' => $penggantians,
            'rekap' => $rekap,
            'isPrint' => false,
            'filtersSummary' => [],
            'lokasis' => $lokasis,
            'gedungs' => $gedungs,
            'lantais' => $lantais,
        ]);
    }

    /**
     * LAPORAN 6: Kartu Ceklist Pemeliharaan APAR Tahunan (Section 8)
     */
    public function kartuPemeliharaan(Request $request, KartuPemeliharaanService $kartuService)
    {
        $currentYear = (int) now()->timezone('Asia/Makassar')->format('Y');
        $tahun = (int) $request->input('tahun', $currentYear);
        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = $currentYear;
        }

        // 1. Opsi filter dropdown APAR (hanya unit aktif dan terpasang yang muncul di daftar pilihan)
        $dropdownQuery = Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
            ->where(function ($q) {
                $q->whereDoesntHave('lokasi')
                    ->orWhereHas('lokasi', fn ($lq) => $lq->where('status', '!=', Lokasi::STATUS_STOK));
            });

        if ($request->filled('gedung_id')) {
            $dropdownQuery->diGedung($request->gedung_id);
            if ($request->filled('lantai_id')) {
                $dropdownQuery->diLantai($request->lantai_id);
            }
        }
        $aparsDropdown = $dropdownQuery->orderBy('kode')->get();

        // 2. Tentukan APAR yang dipilih (gunakan withTrashed agar APAR soft-deleted tetap bisa dibuka via URL)
        $selectedApar = null;
        if ($request->filled('apar_id')) {
            $selectedApar = Apar::withTrashed()
                ->with(['lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung', 'aparPengganti', 'penggantianKeluar.aparBaru'])
                ->find($request->apar_id);
        }

        if (! $selectedApar) {
            $selectedApar = $aparsDropdown->first();
        }

        if (! $selectedApar) {
            $selectedApar = Apar::withTrashed()
                ->with(['lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung', 'aparPengganti', 'penggantianKeluar.aparBaru'])
                ->first();
        }

        // Mode Cetak Massal atau Ekspor CSV Massal
        $isBatch = $request->input('mode') === 'batch'
            || (! $request->filled('apar_id') && ($request->filled('gedung_id') || $request->filled('lantai_id')));

        // Format CSV Export
        if ($request->input('format') === 'csv') {
            if ($isBatch) {
                $batchQuery = Apar::query()->with([
                    'lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung',
                    'aparPengganti', 'penggantianKeluar.aparBaru',
                    'pemeriksaans' => function ($q) use ($tahun) {
                        $q->where('periode_tahun', $tahun)
                            ->whereNotIn('status', ['dibatalkan', 'draft'])
                            ->with('details');
                    },
                ]);

                if ($request->filled('gedung_id')) {
                    $batchQuery->diGedung($request->gedung_id);
                    if ($request->filled('lantai_id')) {
                        $batchQuery->diLantai($request->lantai_id);
                    }
                }

                $batchCount = (clone $batchQuery)->count();
                if ($batchCount > KartuPemeliharaanService::MAX_BATCH_PRINT) {
                    return redirect()->route('laporan.kartu-pemeliharaan', $request->except(['format', 'mode']))
                        ->with('warning', "Jumlah APAR yang terfilter ({$batchCount} unit) melebihi batas maksimal (".KartuPemeliharaanService::MAX_BATCH_PRINT.' unit). Silakan pilih filter gedung atau lantai yang lebih spesifik.');
                }

                $apars = $batchQuery->orderBy('kode')->get();
                $columns = $kartuService->getChecklistColumns($tahun, $apars);

                return $kartuService->exportCsvBatch($apars, $tahun, $columns);
            }

            if (! $selectedApar) {
                return redirect()->route('laporan.kartu-pemeliharaan')->with('error', 'Tidak ada data APAR yang dapat diekspor.');
            }

            $selectedApar->load([
                'pemeriksaans' => function ($q) use ($tahun) {
                    $q->where('periode_tahun', $tahun)
                        ->whereNotIn('status', ['dibatalkan', 'draft'])
                        ->with('details');
                },
            ]);
            $columns = $kartuService->getChecklistColumns($tahun, $selectedApar);
            $matrix = $kartuService->buildAparYearMatrix($selectedApar, $tahun, $columns);

            return $kartuService->exportCsvSingle($selectedApar, $tahun, $columns, $matrix);
        }

        // Format Print View
        if ($request->input('format') === 'print') {
            if ($isBatch) {
                $batchQuery = Apar::query()->with([
                    'lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung',
                    'aparPengganti', 'penggantianKeluar.aparBaru',
                    'pemeriksaans' => function ($q) use ($tahun) {
                        $q->where('periode_tahun', $tahun)
                            ->whereNotIn('status', ['dibatalkan', 'draft'])
                            ->with('details');
                    },
                ]);

                if ($request->filled('gedung_id')) {
                    $batchQuery->diGedung($request->gedung_id);
                    if ($request->filled('lantai_id')) {
                        $batchQuery->diLantai($request->lantai_id);
                    }
                }

                $batchCount = (clone $batchQuery)->count();
                if ($batchCount > KartuPemeliharaanService::MAX_BATCH_PRINT) {
                    return redirect()->route('laporan.kartu-pemeliharaan', $request->except(['format', 'mode']))
                        ->with('warning', "Jumlah APAR yang terfilter ({$batchCount} unit) melebihi batas maksimal cetak massal (".KartuPemeliharaanService::MAX_BATCH_PRINT.' unit). Silakan pilih filter gedung atau lantai yang lebih spesifik.');
                }

                $apars = $batchQuery->orderBy('kode')->get();
                $columns = $kartuService->getChecklistColumns($tahun, $apars);

                $cardsData = [];
                foreach ($apars as $aparItem) {
                    $cardsData[] = [
                        'apar' => $aparItem,
                        'meta' => $kartuService->getAparCardMeta($aparItem, $tahun),
                        'matrix' => $kartuService->buildAparYearMatrix($aparItem, $tahun, $columns),
                    ];
                }

                $orientation = KartuPemeliharaanService::determinePrintOrientation($columns);
                $filtersSummary = $this->buildFilterSummary($request);
                $filtersSummary['Tahun'] = (string) $tahun;

                return view('laporan.kartu-pemeliharaan', [
                    'isPrint' => true,
                    'isBatch' => true,
                    'orientation' => $orientation,
                    'tahun' => $tahun,
                    'columns' => $columns,
                    'cardsData' => $cardsData,
                    'filtersSummary' => $filtersSummary,
                    'symbols' => KartuPemeliharaanService::getSymbols(),
                    'selectedApar' => null,
                    'aparsDropdown' => collect(),
                    'gedungs' => collect(),
                    'lantais' => collect(),
                    'rekapTahunan' => null,
                ]);
            }

            if (! $selectedApar) {
                return redirect()->route('laporan.kartu-pemeliharaan')->with('error', 'Tidak ada data APAR yang dipilih.');
            }

            $selectedApar->load([
                'pemeriksaans' => function ($q) use ($tahun) {
                    $q->where('periode_tahun', $tahun)
                        ->whereNotIn('status', ['dibatalkan', 'draft'])
                        ->with('details');
                },
            ]);
            $columns = $kartuService->getChecklistColumns($tahun, $selectedApar);
            $matrix = $kartuService->buildAparYearMatrix($selectedApar, $tahun, $columns);
            $meta = $kartuService->getAparCardMeta($selectedApar, $tahun);
            $orientation = KartuPemeliharaanService::determinePrintOrientation($columns);

            $filtersSummary = $this->buildFilterSummary($request);
            $filtersSummary['Tahun'] = (string) $tahun;
            $filtersSummary['Unit APAR'] = $selectedApar->kode;

            return view('laporan.kartu-pemeliharaan', [
                'isPrint' => true,
                'isBatch' => false,
                'orientation' => $orientation,
                'tahun' => $tahun,
                'columns' => $columns,
                'cardsData' => [
                    [
                        'apar' => $selectedApar,
                        'meta' => $meta,
                        'matrix' => $matrix,
                    ],
                ],
                'filtersSummary' => $filtersSummary,
                'symbols' => KartuPemeliharaanService::getSymbols(),
                'selectedApar' => $selectedApar,
                'aparsDropdown' => collect(),
                'gedungs' => collect(),
                'lantais' => collect(),
                'rekapTahunan' => null,
            ]);
        }

        // Web View (Interaktif)
        $cardsData = [];
        $columns = [];
        $rekapTahunan = null;

        if ($selectedApar) {
            $selectedApar->load([
                'pemeriksaans' => function ($q) use ($tahun) {
                    $q->where('periode_tahun', $tahun)
                        ->whereNotIn('status', ['dibatalkan', 'draft'])
                        ->with('details');
                },
            ]);
            $columns = $kartuService->getChecklistColumns($tahun, $selectedApar);
            $matrix = $kartuService->buildAparYearMatrix($selectedApar, $tahun, $columns);
            $meta = $kartuService->getAparCardMeta($selectedApar, $tahun);

            // Hitung rekap bulanan untuk kartu statistik ringkas
            $bulanTerinspeksi = 0;
            $bulanBaik = 0;
            $bulanPerluTindakan = 0;

            foreach ($matrix as $mRow) {
                if ($mRow['has_pemeriksaan']) {
                    $bulanTerinspeksi++;
                    $adaTemuan = false;
                    foreach ($columns as $c) {
                        if (($mRow['cells'][$c['key']]['hasil'] ?? null) === PemeriksaanDetail::HASIL_TIDAK_BAIK) {
                            $adaTemuan = true;
                            break;
                        }
                    }
                    if ($adaTemuan) {
                        $bulanPerluTindakan++;
                    } else {
                        $bulanBaik++;
                    }
                }
            }

            $rekapTahunan = [
                'terinspeksi' => $bulanTerinspeksi,
                'baik' => $bulanBaik,
                'perlu_tindakan' => $bulanPerluTindakan,
                'belum_inspeksi' => max(0, 12 - $bulanTerinspeksi),
            ];

            $cardsData[] = [
                'apar' => $selectedApar,
                'meta' => $meta,
                'matrix' => $matrix,
            ];
        }

        $gedungs = Gedung::orderBy('nama', 'asc')->get();
        $lantais = $request->filled('gedung_id')
            ? Lantai::where('gedung_id', $request->gedung_id)->orderBy('nomor_lantai', 'asc')->get()
            : collect();

        // Daftar tahun yang tersedia untuk filter (dari operational_start_year sampai current year + 1)
        $startYear = (int) config('hafis.operational_start_year', 2026);
        $minYear = min($startYear - 1, $tahun);
        $maxYear = max($currentYear + 1, $tahun);
        $tahunList = range($maxYear, $minYear);

        return view('laporan.kartu-pemeliharaan', [
            'isPrint' => false,
            'isBatch' => false,
            'orientation' => KartuPemeliharaanService::determinePrintOrientation($columns),
            'tahun' => $tahun,
            'tahunList' => $tahunList,
            'columns' => $columns,
            'cardsData' => $cardsData,
            'selectedApar' => $selectedApar,
            'aparsDropdown' => $aparsDropdown,
            'gedungs' => $gedungs,
            'lantais' => $lantais,
            'symbols' => KartuPemeliharaanService::getSymbols(),
            'rekapTahunan' => $rekapTahunan,
            'filtersSummary' => [],
        ]);
    }

    /**
     * Helper stream CSV download tanpa dependency eksternal
     */
    private function exportCsv(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            // Tulis BOM UTF-8 agar kompatibel dengan Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Helper merangkum filter aktif untuk kop print
     */
    private function buildFilterSummary(Request $request): array
    {
        $summary = [];

        if ($request->filled('dari_tanggal') || $request->filled('sampai_tanggal')) {
            $dari = $request->dari_tanggal ? date('d/m/Y', strtotime($request->dari_tanggal)) : 'Awal';
            $sampai = $request->sampai_tanggal ? date('d/m/Y', strtotime($request->sampai_tanggal)) : 'Sekarang';
            $summary['Periode'] = "{$dari} s/d {$sampai}";
        }

        if ($request->filled('lokasi_id')) {
            $lok = Lokasi::find($request->lokasi_id);
            if ($lok) {
                $summary['Lokasi K3RS'] = $lok->nama;
            }
        } elseif ($request->filled('gedung_id')) {
            $g = Gedung::find($request->gedung_id);
            if ($g) {
                $summary['Gedung'] = $g->nama;
            }
            if ($request->filled('lantai_id')) {
                $l = Lantai::find($request->lantai_id);
                if ($l) {
                    $summary['Lantai'] = $l->nama;
                }
            }
        }

        if ($request->filled('q')) {
            $summary['Kata Kunci'] = $request->q;
        }

        if ($request->filled('status')) {
            $summary['Status'] = ucfirst(str_replace('_', ' ', $request->status));
        }

        if ($request->filled('kondisi')) {
            $summary['Kondisi'] = ucfirst($request->kondisi);
        }

        if ($request->filled('hasil')) {
            $summary['Hasil'] = ucfirst(str_replace('_', ' ', $request->hasil));
        }

        if ($request->filled('tipe_penyelesaian')) {
            $tipeLabels = [
                Pemeriksaan::TIPE_TEPAT_WAKTU => 'Tepat Waktu',
                Pemeriksaan::TIPE_TERLAMBAT => 'Selesai Terlambat',
                Pemeriksaan::TIPE_KOREKSI => 'Koreksi Data',
            ];
            $summary['Tipe Kepatuhan'] = $tipeLabels[$request->tipe_penyelesaian] ?? ucfirst($request->tipe_penyelesaian);
        }

        if ($request->filled('status_tl')) {
            $stlLabels = [
                'belum_ada_tl' => 'Belum Dibuat TL',
                'belum_ditindaklanjuti' => 'Belum Ditindaklanjuti',
                'dalam_proses' => 'Dalam Proses',
                'selesai' => 'Selesai',
            ];
            $summary['Status TL'] = $stlLabels[$request->status_tl] ?? ucfirst(str_replace('_', ' ', $request->status_tl));
        }

        if ($request->filled('pemeriksa')) {
            $summary['Pemeriksa'] = $request->pemeriksa;
        }

        if ($request->filled('petugas_pelaksana')) {
            $summary['Petugas Pelaksana'] = $request->petugas_pelaksana;
        }

        if ($request->filled('jenis_media')) {
            $summary['Jenis Media'] = $request->jenis_media;
        }

        if ($request->filled('jenis_tindakan')) {
            $summary['Jenis Tindakan'] = ucfirst(str_replace('_', ' ', $request->jenis_tindakan));
        }

        if ($request->filled('alasan')) {
            $summary['Alasan'] = ucfirst(str_replace('_', ' ', $request->alasan));
        }

        if ($request->filled('tahun')) {
            $summary['Tahun'] = $request->tahun;
        }

        if ($request->filled('apar_id')) {
            $a = Apar::withTrashed()->find($request->apar_id);
            if ($a) {
                $summary['Unit APAR'] = $a->kode;
            }
        }

        return $summary;
    }
}
