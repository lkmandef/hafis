<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LokasiController extends Controller
{
    /**
     * Tampilkan halaman utama struktur lokasi (Gedung -> Lantai -> Lokasi / Ruangan)
     */
    public function index(Request $request)
    {
        $search = $request->input('q');
        $tab = $request->input('tab', 'hirarki'); // hirarki, gedung, lantai, ruangan

        $gedungsQuery = Gedung::with([
            'lantais.lokasis.apars',
            'lantais.lokasis.ruangan',
            'lantais.ruangans.apars',
            'lantais.ruangans.lokasi',
            'lantais.denah',
        ])->withCount(['lantais', 'ruangans', 'lokasis']);

        if ($search) {
            $gedungsQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('lantais', function ($lq) use ($search) {
                        $lq->where('nama', 'like', "%{$search}%")
                            ->orWhereHas('ruangans', function ($rq) use ($search) {
                                $rq->where('nama', 'like', "%{$search}%")
                                    ->orWhere('kode', 'like', "%{$search}%");
                            })
                            ->orWhereHas('lokasis', function ($lkq) use ($search) {
                                $lkq->where('nama', 'like', "%{$search}%")
                                    ->orWhere('kode', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $gedungs = $gedungsQuery->orderBy('nama', 'asc')->get();
        $allGedungs = $search ? Gedung::withCount(['lantais', 'ruangans', 'lokasis'])->orderBy('nama', 'asc')->get() : $gedungs;
        $allLantais = Lantai::with(['gedung', 'denah'])->withCount(['ruangans', 'lokasis'])->orderBy('gedung_id')->orderBy('nomor_lantai')->get();
        $allRuangans = Ruangan::with(['lantai.gedung', 'lokasi', 'apars'])->orderBy('nama', 'asc')->get();
        $allLokasis = Lokasi::with(['gedung', 'lantai', 'ruangan', 'apars'])->orderBy('nama', 'asc')->get();

        // Ringkasan metrik
        $totalGedung = $allGedungs->count();
        $totalLantai = $allLantais->count();
        $totalRuangan = $allRuangans->count();
        $totalLokasi = $allLokasis->count();

        return view('lokasi.index', compact(
            'gedungs',
            'allGedungs',
            'allLantais',
            'allRuangans',
            'allLokasis',
            'totalGedung',
            'totalLantai',
            'totalRuangan',
            'totalLokasi',
            'search',
            'tab'
        ));
    }

    /**
     * Tampilkan detail hierarki satu gedung
     */
    public function showGedung(Gedung $gedung)
    {
        $gedung->load([
            'lantais.denah',
            'lantais.lokasis.apars',
            'lantais.lokasis.ruangan',
            'lantais.ruangans.apars',
            'lantais.ruangans.lokasi',
        ]);
        $allGedungs = Gedung::orderBy('nama', 'asc')->get();

        return view('lokasi.show', compact('gedung', 'allGedungs'));
    }

    // ==========================================
    // CRUD GEDUNG
    // ==========================================

    public function storeGedung(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120', 'unique:gedungs,nama'],
            'kode' => ['nullable', 'string', 'max:30', 'unique:gedungs,kode'],
            'keterangan' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:aktif,nonaktif'],
        ], [
            'nama.required' => 'Nama gedung wajib diisi.',
            'nama.unique' => 'Nama gedung sudah terdaftar.',
            'kode.unique' => 'Kode gedung sudah digunakan.',
        ]);

        if (empty($validated['kode'])) {
            $words = explode(' ', trim($validated['nama']));
            $abbr = '';
            foreach ($words as $w) {
                $abbr .= strtoupper(substr($w, 0, 1));
            }
            $validated['kode'] = 'GDG-'.($abbr ?: rand(100, 999));
        }

        $validated['status'] = $validated['status'] ?? 'aktif';

        Gedung::create($validated);

        return redirect()->back()->with('success', 'Gedung '.$validated['nama'].' berhasil ditambahkan.');
    }

    public function updateGedung(Request $request, Gedung $gedung)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120', Rule::unique('gedungs', 'nama')->ignore($gedung->id)],
            'kode' => ['required', 'string', 'max:30', Rule::unique('gedungs', 'kode')->ignore($gedung->id)],
            'keterangan' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:aktif,nonaktif'],
        ], [
            'nama.required' => 'Nama gedung wajib diisi.',
            'nama.unique' => 'Nama gedung sudah terdaftar.',
            'kode.required' => 'Kode gedung wajib diisi.',
            'kode.unique' => 'Kode gedung sudah digunakan.',
        ]);

        $gedung->update($validated);

        return redirect()->back()->with('success', 'Data gedung '.$gedung->nama.' berhasil diperbarui.');
    }

    public function destroyGedung(Gedung $gedung)
    {
        $nama = $gedung->nama;
        $gedung->delete();

        return redirect()->route('lokasi.index')->with('success', 'Gedung '.$nama.' beserta lantai dan ruangannya berhasil dihapus.');
    }

    // ==========================================
    // CRUD LANTAI
    // ==========================================

    public function storeLantai(Request $request)
    {
        $validated = $request->validate([
            'gedung_id' => ['required', 'exists:gedungs,id'],
            'nomor_lantai' => [
                'required',
                'integer',
                Rule::unique('lantais', 'nomor_lantai')->where(fn ($query) => $query->where('gedung_id', $request->gedung_id)),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'gedung_id.required' => 'Pilih gedung induk terlebih dahulu.',
            'gedung_id.exists' => 'Gedung yang dipilih tidak valid.',
            'nomor_lantai.required' => 'Nomor lantai wajib diisi.',
            'nomor_lantai.unique' => 'Nomor lantai tersebut sudah ada pada gedung yang dipilih.',
            'nama.required' => 'Label nama lantai wajib diisi.',
        ]);

        Lantai::create($validated);

        return redirect()->back()->with('success', 'Lantai '.$validated['nama'].' berhasil ditambahkan.');
    }

    public function updateLantai(Request $request, Lantai $lantai)
    {
        $validated = $request->validate([
            'nomor_lantai' => [
                'required',
                'integer',
                Rule::unique('lantais', 'nomor_lantai')
                    ->where(fn ($query) => $query->where('gedung_id', $lantai->gedung_id))
                    ->ignore($lantai->id),
            ],
            'nama' => ['required', 'string', 'max:100'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nomor_lantai.required' => 'Nomor lantai wajib diisi.',
            'nomor_lantai.unique' => 'Nomor lantai tersebut sudah terdaftar pada gedung ini.',
            'nama.required' => 'Label nama lantai wajib diisi.',
        ]);

        $lantai->update($validated);

        return redirect()->back()->with('success', 'Data lantai '.$lantai->nama.' berhasil diperbarui.');
    }

    public function destroyLantai(Lantai $lantai)
    {
        $nama = $lantai->nama;
        $lantai->delete();

        return redirect()->back()->with('success', 'Lantai '.$nama.' berhasil dihapus.');
    }

    // ==========================================
    // CRUD RUANGAN / AREA
    // ==========================================

    public function storeRuangan(Request $request)
    {
        $validated = $request->validate([
            'lantai_id' => ['required', 'exists:lantais,id'],
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ruangans', 'nama')->where(fn ($query) => $query->where('lantai_id', $request->lantai_id)),
            ],
            'kode' => ['nullable', 'string', 'max:50'],
            'kategori' => ['nullable', 'string', 'max:60'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'lantai_id.required' => 'Pilih lantai terlebih dahulu.',
            'lantai_id.exists' => 'Lantai yang dipilih tidak valid.',
            'nama.required' => 'Nama ruangan / area wajib diisi.',
            'nama.unique' => 'Nama ruangan sudah terdaftar pada lantai yang dipilih.',
        ]);

        Ruangan::create($validated);

        return redirect()->back()->with('success', 'Ruangan/Area '.$validated['nama'].' berhasil ditambahkan.');
    }

    public function updateRuangan(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ruangans', 'nama')
                    ->where(fn ($query) => $query->where('lantai_id', $ruangan->lantai_id))
                    ->ignore($ruangan->id),
            ],
            'kode' => ['nullable', 'string', 'max:50'],
            'kategori' => ['nullable', 'string', 'max:60'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'nama.required' => 'Nama ruangan / area wajib diisi.',
            'nama.unique' => 'Nama ruangan sudah terdaftar pada lantai ini.',
        ]);

        $ruangan->update($validated);

        return redirect()->back()->with('success', 'Data ruangan '.$ruangan->nama.' berhasil diperbarui.');
    }

    public function destroyRuangan(Ruangan $ruangan)
    {
        $nama = $ruangan->nama;
        $ruangan->delete();

        return redirect()->back()->with('success', 'Ruangan/Area '.$nama.' berhasil dihapus.');
    }
}
