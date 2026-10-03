<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AparController extends Controller
{
    /**
     * Tampilkan daftar master data APAR dengan pencarian dan filter
     */
    public function index(Request $request)
    {
        $search = $request->input('q');
        $gedungId = $request->input('gedung_id');
        $lantaiId = $request->input('lantai_id');
        $status = $request->input('status');
        $jenisMedia = $request->input('jenis_media');

        // Section 20: Default daftar fokus pada APAR aktif jika tidak ada filter atau pencarian
        if (! $request->has('status') && ! $search) {
            $status = 'aktif';
        }

        $query = Apar::with(['ruangan.lantai.gedung', 'aparPengganti', 'aparDigantikan']);

        // 1. Pencarian teks (Section 21: termasuk kode APAR dan qr_code)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%")
                    ->orWhere('nomor_register', 'like', "%{$search}%")
                    ->orWhere('merek', 'like', "%{$search}%")
                    ->orWhere('tipe_model', 'like', "%{$search}%")
                    ->orWhere('posisi_detail', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('ruangan', function ($rq) use ($search) {
                        $rq->where('nama', 'like', "%{$search}%")
                            ->orWhere('kode', 'like', "%{$search}%")
                            ->orWhereHas('lantai', function ($lq) use ($search) {
                                $lq->where('nama', 'like', "%{$search}%")
                                    ->orWhereHas('gedung', function ($gq) use ($search) {
                                        $gq->where('nama', 'like', "%{$search}%")
                                            ->orWhere('kode', 'like', "%{$search}%");
                                    });
                            });
                    });
            });
        }

        // 2. Filter Gedung
        if ($gedungId) {
            $query->whereHas('ruangan.lantai', function ($lq) use ($gedungId) {
                $lq->where('gedung_id', $gedungId);
            });
        }

        // 3. Filter Lantai
        if ($lantaiId) {
            $query->whereHas('ruangan', function ($rq) use ($lantaiId) {
                $rq->where('lantai_id', $lantaiId);
            });
        }

        // 4. Filter Status (Section 20: Semua, Aktif, Tidak Aktif, Sudah Diganti, atau kondisi)
        if ($status === 'semua') {
            // Tampilkan seluruh data tanpa memfilter status
        } elseif ($status === 'aktif') {
            $query->where('status', '!=', Apar::STATUS_TIDAK_AKTIF);
        } elseif ($status === 'tidak_aktif') {
            $query->where('status', Apar::STATUS_TIDAK_AKTIF)->whereNull('replaced_by_apar_id');
        } elseif ($status === 'sudah_diganti') {
            $query->where('status', Apar::STATUS_TIDAK_AKTIF)->whereNotNull('replaced_by_apar_id');
        } elseif ($status) {
            $query->where('status', $status);
        }

        // 5. Filter Jenis Media
        if ($jenisMedia) {
            $query->where('jenis_media', $jenisMedia);
        }

        $apars = $query->orderBy('kode', 'asc')->paginate(12)->withQueryString();

        // Data pendukung filter
        $gedungs = Gedung::with(['lantais' => fn ($q) => $q->orderBy('nomor_lantai')])
            ->orderBy('nama', 'asc')
            ->get();

        $lantaisForFilter = collect();
        if ($gedungId) {
            $lantaisForFilter = Lantai::where('gedung_id', $gedungId)
                ->orderBy('nomor_lantai', 'asc')
                ->get();
        }

        // Metrik riil kondisi & status APAR (Section 20 & 23)
        $totalApar = Apar::count();
        $totalAktif = Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)->count();
        $totalBaik = Apar::where('status', Apar::STATUS_BAIK)->count();
        $totalPerhatian = Apar::where('status', Apar::STATUS_PERHATIAN)->count();
        $totalRusak = Apar::where('status', Apar::STATUS_RUSAK)->count();
        $totalTidakAktif = Apar::where('status', Apar::STATUS_TIDAK_AKTIF)->whereNull('replaced_by_apar_id')->count();
        $totalSudahDiganti = Apar::where('status', Apar::STATUS_TIDAK_AKTIF)->whereNotNull('replaced_by_apar_id')->count();

        return view('apar.index', compact(
            'apars',
            'gedungs',
            'lantaisForFilter',
            'search',
            'gedungId',
            'lantaiId',
            'status',
            'jenisMedia',
            'totalApar',
            'totalAktif',
            'totalBaik',
            'totalPerhatian',
            'totalRusak',
            'totalTidakAktif',
            'totalSudahDiganti'
        ));
    }

    /**
     * Tampilkan formulir pendaftaran unit APAR baru
     */
    public function create()
    {
        $gedungs = Gedung::with([
            'lantais' => fn ($q) => $q->orderBy('nomor_lantai', 'asc'),
            'lantais.ruangans' => fn ($q) => $q->orderBy('nama', 'asc'),
        ])->orderBy('nama', 'asc')->get();

        $mediaOptions = Apar::JENIS_MEDIA_OPTIONS;
        $statusLabels = Apar::STATUS_LABELS;

        return view('apar.create', compact('gedungs', 'mediaOptions', 'statusLabels'));
    }

    /**
     * Simpan data unit APAR baru dengan validasi bertingkat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', 'unique:apars,kode'],
            'nomor_register' => ['nullable', 'string', 'max:100'],
            'gedung_id' => ['required', 'exists:gedungs,id'],
            'lantai_id' => [
                'required',
                'exists:lantais,id',
                function ($attribute, $value, $fail) use ($request) {
                    $lantai = Lantai::find($value);
                    if (! $lantai || $lantai->gedung_id != $request->gedung_id) {
                        $fail('Lantai yang dipilih tidak sesuai dengan gedung yang dipilih.');
                    }
                },
            ],
            'ruangan_id' => [
                'required',
                'exists:ruangans,id',
                function ($attribute, $value, $fail) use ($request) {
                    $ruangan = Ruangan::find($value);
                    if (! $ruangan || $ruangan->lantai_id != $request->lantai_id) {
                        $fail('Ruangan/Area yang dipilih tidak sesuai dengan lantai yang dipilih.');
                    }
                },
            ],
            'posisi_detail' => ['nullable', 'string', 'max:255'],
            'jenis_media' => ['required', 'string', Rule::in(Apar::JENIS_MEDIA_OPTIONS)],
            'kapasitas' => ['required', 'string', 'max:30'],
            'merek' => ['nullable', 'string', 'max:100'],
            'tipe_model' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(array_keys(Apar::STATUS_LABELS))],
            'tanggal_pemasangan' => ['nullable', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tanggal_pemasangan'],
            'tanggal_pemeriksaan_terakhir' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'kode.required' => 'Kode APAR wajib diisi.',
            'kode.unique' => 'Kode APAR tersebut sudah terdaftar di sistem.',
            'gedung_id.required' => 'Pilih gedung lokasi penempatan.',
            'gedung_id.exists' => 'Gedung yang dipilih tidak valid.',
            'lantai_id.required' => 'Pilih lantai lokasi penempatan.',
            'lantai_id.exists' => 'Lantai yang dipilih tidak valid.',
            'ruangan_id.required' => 'Pilih ruangan / area lokasi penempatan.',
            'ruangan_id.exists' => 'Ruangan / area yang dipilih tidak valid.',
            'jenis_media.required' => 'Pilih jenis media pemadam.',
            'jenis_media.in' => 'Pilihan jenis media tidak sesuai ketentuan.',
            'kapasitas.required' => 'Kapasitas tabung wajib diisi.',
            'status.required' => 'Status kondisi APAR wajib ditentukan.',
            'status.in' => 'Status kondisi tidak valid.',
            'tanggal_pemasangan.date' => 'Format tanggal pemasangan tidak valid.',
            'tanggal_kadaluarsa.date' => 'Format tanggal kedaluwarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal' => 'Tanggal kedaluwarsa tidak boleh lebih awal dari tanggal pemasangan.',
            'tanggal_pemeriksaan_terakhir.date' => 'Format tanggal pemeriksaan terakhir tidak valid.',
        ]);

        // Buat identifier unik QR Code yang siap digunakan untuk scan
        $cleanKode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $validated['kode']));
        $validated['qr_code'] = 'HAFIS-QR-'.$cleanKode.'-'.strtoupper(Str::random(6));

        $apar = Apar::create($validated);

        return redirect()->route('apar.show', $apar->id)->with('success', "Unit APAR {$apar->kode} berhasil didaftarkan ke sistem.");
    }

    /**
     * Tampilkan detail lengkap satu unit APAR
     */
    public function show($id)
    {
        $apar = is_numeric($id)
            ? Apar::with([
                'ruangan.lantai.gedung',
                'tindakLanjuts.pemeriksaan',
                'penggantianKeluar.aparBaru.ruangan.lantai.gedung',
                'penggantianMasuk.aparLama.ruangan.lantai.gedung',
                'aparPengganti',
                'aparDigantikan',
            ])->findOrFail($id)
            : Apar::with([
                'ruangan.lantai.gedung',
                'tindakLanjuts.pemeriksaan',
                'penggantianKeluar.aparBaru.ruangan.lantai.gedung',
                'penggantianMasuk.aparLama.ruangan.lantai.gedung',
                'aparPengganti',
                'aparDigantikan',
            ])->where('kode', $id)->firstOrFail();

        return view('apar.show', compact('apar'));
    }

    /**
     * Tampilkan formulir edit data unit APAR
     */
    public function edit($id)
    {
        $apar = is_numeric($id)
            ? Apar::with(['ruangan.lantai.gedung'])->findOrFail($id)
            : Apar::with(['ruangan.lantai.gedung'])->where('kode', $id)->firstOrFail();

        $gedungs = Gedung::with([
            'lantais' => fn ($q) => $q->orderBy('nomor_lantai', 'asc'),
            'lantais.ruangans' => fn ($q) => $q->orderBy('nama', 'asc'),
        ])->orderBy('nama', 'asc')->get();

        $mediaOptions = Apar::JENIS_MEDIA_OPTIONS;
        $statusLabels = Apar::STATUS_LABELS;

        return view('apar.edit', compact('apar', 'gedungs', 'mediaOptions', 'statusLabels'));
    }

    /**
     * Perbarui data master APAR dengan validasi lokasi berjenjang
     */
    public function update(Request $request, $id)
    {
        $apar = is_numeric($id)
            ? Apar::findOrFail($id)
            : Apar::where('kode', $id)->firstOrFail();

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50', Rule::unique('apars', 'kode')->ignore($apar->id)],
            'nomor_register' => ['nullable', 'string', 'max:100'],
            'gedung_id' => ['required', 'exists:gedungs,id'],
            'lantai_id' => [
                'required',
                'exists:lantais,id',
                function ($attribute, $value, $fail) use ($request) {
                    $lantai = Lantai::find($value);
                    if (! $lantai || $lantai->gedung_id != $request->gedung_id) {
                        $fail('Lantai yang dipilih tidak sesuai dengan gedung yang dipilih.');
                    }
                },
            ],
            'ruangan_id' => [
                'required',
                'exists:ruangans,id',
                function ($attribute, $value, $fail) use ($request) {
                    $ruangan = Ruangan::find($value);
                    if (! $ruangan || $ruangan->lantai_id != $request->lantai_id) {
                        $fail('Ruangan/Area yang dipilih tidak sesuai dengan lantai yang dipilih.');
                    }
                },
            ],
            'posisi_detail' => ['nullable', 'string', 'max:255'],
            'jenis_media' => ['required', 'string', Rule::in(Apar::JENIS_MEDIA_OPTIONS)],
            'kapasitas' => ['required', 'string', 'max:30'],
            'merek' => ['nullable', 'string', 'max:100'],
            'tipe_model' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', Rule::in(array_keys(Apar::STATUS_LABELS))],
            'tanggal_pemasangan' => ['nullable', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tanggal_pemasangan'],
            'tanggal_pemeriksaan_terakhir' => ['nullable', 'date'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ], [
            'kode.required' => 'Kode APAR wajib diisi.',
            'kode.unique' => 'Kode APAR tersebut sudah terdaftar di sistem.',
            'gedung_id.required' => 'Pilih gedung lokasi penempatan.',
            'gedung_id.exists' => 'Gedung yang dipilih tidak valid.',
            'lantai_id.required' => 'Pilih lantai lokasi penempatan.',
            'lantai_id.exists' => 'Lantai yang dipilih tidak valid.',
            'ruangan_id.required' => 'Pilih ruangan / area lokasi penempatan.',
            'ruangan_id.exists' => 'Ruangan / area yang dipilih tidak valid.',
            'jenis_media.required' => 'Pilih jenis media pemadam.',
            'jenis_media.in' => 'Pilihan jenis media tidak sesuai ketentuan.',
            'kapasitas.required' => 'Kapasitas tabung wajib diisi.',
            'status.required' => 'Status kondisi APAR wajib ditentukan.',
            'status.in' => 'Status kondisi tidak valid.',
            'tanggal_pemasangan.date' => 'Format tanggal pemasangan tidak valid.',
            'tanggal_kadaluarsa.date' => 'Format tanggal kedaluwarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal' => 'Tanggal kedaluwarsa tidak boleh lebih awal dari tanggal pemasangan.',
            'tanggal_pemeriksaan_terakhir.date' => 'Format tanggal pemeriksaan terakhir tidak valid.',
        ]);

        $apar->update($validated);

        return redirect()->route('apar.show', $apar->id)->with('success', "Data unit APAR {$apar->kode} berhasil diperbarui.");
    }

    /**
     * Hapus unit APAR (soft delete)
     */
    public function destroy($id)
    {
        $apar = is_numeric($id)
            ? Apar::findOrFail($id)
            : Apar::where('kode', $id)->firstOrFail();

        $kode = $apar->kode;
        $apar->delete();

        return redirect()->route('apar.index')->with('success', "Unit APAR {$kode} berhasil dihapus dari data aktif.");
    }

    /**
     * Tampilan cetak label stiker QR APAR untuk fisik di lapangan
     */
    public function cetakQr($id)
    {
        $apar = is_numeric($id)
            ? Apar::with(['ruangan.lantai.gedung'])->findOrFail($id)
            : Apar::with(['ruangan.lantai.gedung'])->where('kode', $id)->firstOrFail();

        return view('apar.cetak-qr', compact('apar'));
    }

    /**
     * Download file QR Code SVG
     */
    public function downloadQr($id)
    {
        $apar = is_numeric($id)
            ? Apar::findOrFail($id)
            : Apar::where('kode', $id)->firstOrFail();

        $svg = $apar->qr_svg;
        $fileName = 'QR-'.$apar->kode.'.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }
}
