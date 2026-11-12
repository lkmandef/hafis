<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lokasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    /**
     * Tampilkan daftar seluruh jadwal inspeksi APAR
     */
    public function index(Request $request)
    {
        $search = $request->input('q');
        $statusFilter = $request->input('status');

        $query = Jadwal::with(['gedung', 'apars']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('dibuat_oleh', 'like', "%{$search}%");
            });
        }

        if ($statusFilter && in_array($statusFilter, ['draft', 'belum_dimulai', 'berjalan', 'selesai', 'dibatalkan'])) {
            $query->where('status', $statusFilter);
        }

        // Sinkronisasi status otomatis berdasarkan tanggal hari ini untuk jadwal non-final
        $allForSync = Jadwal::whereIn('status', ['belum_dimulai', 'berjalan'])->get();
        foreach ($allForSync as $j) {
            $j->sinkronkanStatus();
        }

        $jadwals = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();

        // Metrik statistik jadwal
        $totalJadwal = Jadwal::count();
        $totalBelumDimulai = Jadwal::where('status', 'belum_dimulai')->count();
        $totalBerjalan = Jadwal::where('status', 'berjalan')->count();
        $totalSelesai = Jadwal::where('status', 'selesai')->count();

        return view('jadwal.index', compact(
            'jadwals',
            'search',
            'statusFilter',
            'totalJadwal',
            'totalBelumDimulai',
            'totalBerjalan',
            'totalSelesai'
        ));
    }

    /**
     * Form pembuatan jadwal inspeksi baru
     */
    public function create()
    {
        $gedungs = Gedung::where('status', 'aktif')->orderBy('nama')->get();
        $totalAparAktif = Apar::where('status', '!=', 'tidak_aktif')
            ->where(function ($q) {
                $q->whereDoesntHave('lokasi')
                    ->orWhereHas('lokasi', fn ($lq) => $lq->where('status', '!=', Lokasi::STATUS_STOK));
            })->count();
        $allApars = Apar::with(['lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung'])
            ->where('status', '!=', 'tidak_aktif')
            ->where(function ($q) {
                $q->whereDoesntHave('lokasi')
                    ->orWhereHas('lokasi', fn ($lq) => $lq->where('status', '!=', Lokasi::STATUS_STOK));
            })
            ->orderBy('kode')
            ->get();

        return view('jadwal.create', compact('gedungs', 'totalAparAktif', 'allApars'));
    }

    /**
     * Simpan jadwal baru beserta alokasi target APAR
     */
    public function store(Request $request)
    {
        if ($request->input('status') === 'dibatalkan' && ! $request->user()?->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk membatalkan jadwal inspeksi.');
        }

        $allowedStatuses = ['draft', 'belum_dimulai', 'berjalan', 'selesai'];
        if ($request->user()?->isAdmin()) {
            $allowedStatuses[] = 'dibatalkan';
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'tipe_target' => ['required', 'string', 'in:semua,gedung,custom'],
            'gedung_id' => ['nullable', 'required_if:tipe_target,gedung', 'exists:gedungs,id'],
            'status' => ['required', 'string', Rule::in($allowedStatuses)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'dibuat_oleh' => ['nullable', 'string', 'max:100'],
            'apar_ids' => ['nullable', 'array'],
            'apar_ids.*' => ['exists:apars,id'],
        ], [
            'nama.required' => 'Nama jadwal inspeksi wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai periode wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai periode wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'tipe_target.required' => 'Pilih cakupan target APAR.',
            'gedung_id.required_if' => 'Pilih gedung target inspeksi.',
            'status.required' => 'Status jadwal wajib dipilih.',
            'status.in' => 'Hanya Administrator yang memiliki wewenang untuk membatalkan jadwal inspeksi.',
        ]);

        $validated['dibuat_oleh'] = auth()->check() ? auth()->user()->name : ($validated['dibuat_oleh'] ?: 'Admin K3RS');
        $validated['user_id'] = auth()->id();

        // Otomatisasi status awal jika status dipilih belum_dimulai/berjalan
        if (in_array($validated['status'], ['belum_dimulai', 'berjalan'])) {
            $today = Carbon::today();
            $mulai = Carbon::parse($validated['tanggal_mulai']);
            $validated['status'] = $today->lt($mulai) ? 'belum_dimulai' : 'berjalan';
        }

        $jadwal = Jadwal::create($validated);

        // Alokasikan target APAR berdasarkan pilihan target (Eksklusi stok cadangan K3RS)
        $targetIds = [];
        if ($validated['tipe_target'] === 'semua') {
            $targetIds = Apar::where('status', '!=', 'tidak_aktif')
                ->where(function ($q) {
                    $q->whereDoesntHave('lokasi')
                        ->orWhereHas('lokasi', fn ($lq) => $lq->where('status', '!=', Lokasi::STATUS_STOK));
                })
                ->pluck('id')->toArray();
        } elseif ($validated['tipe_target'] === 'gedung') {
            $targetIds = Apar::diGedung($validated['gedung_id'])
                ->where('status', '!=', 'tidak_aktif')
                ->where(function ($q) {
                    $q->whereDoesntHave('lokasi')
                        ->orWhereHas('lokasi', fn ($lq) => $lq->where('status', '!=', Lokasi::STATUS_STOK));
                })
                ->pluck('id')->toArray();
        } elseif ($validated['tipe_target'] === 'custom') {
            $targetIds = $validated['apar_ids'] ?? [];
        }

        // Simpan ke pivot jadwal_apars dengan status awal 'belum_diperiksa'
        $syncData = [];
        foreach ($targetIds as $aparId) {
            $syncData[$aparId] = [
                'status_pemeriksaan' => 'belum_diperiksa',
                'catatan' => null,
            ];
        }

        $jadwal->apars()->sync($syncData);

        return redirect()->route('jadwal.show', $jadwal->id)
            ->with('success', "Jadwal '{$jadwal->nama}' berhasil dibuat dengan ".count($syncData).' unit APAR sebagai target.');
    }

    /**
     * Tampilkan detail jadwal dan daftar target APAR
     */
    public function show(Jadwal $jadwal, Request $request)
    {
        $jadwal->sinkronkanStatus();
        $jadwal->load(['gedung']);

        $searchApar = $request->input('q_apar');
        $statusApar = $request->input('status_apar');

        $aparsQuery = $jadwal->apars()->with(['lokasi.gedung', 'lokasi.lantai', 'ruangan.lantai.gedung']);

        if ($searchApar) {
            $aparsQuery->where(function ($q) use ($searchApar) {
                $q->where('kode', 'like', "%{$searchApar}%")
                    ->orWhere('jenis_media', 'like', "%{$searchApar}%")
                    ->orWhereHas('lokasi', fn ($lq) => $lq->where('nama', 'like', "%{$searchApar}%"))
                    ->orWhereHas('ruangan', function ($rq) use ($searchApar) {
                        $rq->where('nama', 'like', "%{$searchApar}%")
                            ->orWhereHas('lantai', function ($lq) use ($searchApar) {
                                $lq->where('nama', 'like', "%{$searchApar}%")
                                    ->orWhereHas('gedung', function ($gq) use ($searchApar) {
                                        $gq->where('nama', 'like', "%{$searchApar}%");
                                    });
                            });
                    });
            });
        }

        if ($statusApar) {
            $aparsQuery->where('apars.status', $statusApar);
        }

        $targetApars = $aparsQuery->paginate(15)->withQueryString();

        return view('jadwal.show', compact('jadwal', 'targetApars', 'searchApar', 'statusApar'));
    }

    /**
     * Form edit jadwal
     */
    public function edit(Jadwal $jadwal)
    {
        $gedungs = Gedung::where('status', 'aktif')->orderBy('nama')->get();

        return view('jadwal.edit', compact('jadwal', 'gedungs'));
    }

    /**
     * Perbarui data jadwal
     */
    public function update(Request $request, Jadwal $jadwal)
    {
        if ($request->input('status') === 'dibatalkan' && ! $request->user()?->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk membatalkan jadwal inspeksi.');
        }

        $allowedStatuses = ['draft', 'belum_dimulai', 'berjalan', 'selesai'];
        if ($request->user()?->isAdmin() || $jadwal->status === 'dibatalkan') {
            $allowedStatuses[] = 'dibatalkan';
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', 'string', Rule::in($allowedStatuses)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'nama.required' => 'Nama jadwal inspeksi wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai periode wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai periode wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'status.required' => 'Status jadwal wajib dipilih.',
            'status.in' => 'Hanya Administrator yang memiliki wewenang untuk mengubah status jadwal menjadi dibatalkan.',
        ]);

        $jadwal->update($validated);

        return redirect()->route('jadwal.show', $jadwal->id)
            ->with('success', "Data jadwal '{$jadwal->nama}' berhasil diperbarui.");
    }

    /**
     * Batalkan jadwal inspeksi
     */
    public function batalkan(Jadwal $jadwal)
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk membatalkan jadwal inspeksi.');
        }

        $jadwal->update(['status' => 'dibatalkan']);

        return redirect()->back()
            ->with('success', "Jadwal '{$jadwal->nama}' berhasil dibatalkan.");
    }

    /**
     * Hapus jadwal inspeksi
     */
    public function destroy(Jadwal $jadwal)
    {
        $nama = $jadwal->nama;
        $jadwal->delete();

        return redirect()->route('jadwal.index')
            ->with('success', "Jadwal '{$nama}' beserta penugasan targetnya berhasil dihapus.");
    }
}
