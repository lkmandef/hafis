<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\PenggantianApar;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PenggantianAparController extends Controller
{
    /**
     * Tampilkan riwayat seluruh penggantian APAR rumah sakit
     */
    public function index(Request $request)
    {
        $query = PenggantianApar::with([
            'aparLama.ruangan.lantai.gedung',
            'aparBaru.ruangan.lantai.gedung',
            'user',
        ])->orderBy('tanggal_penggantian', 'desc');

        if ($request->filled('alasan')) {
            $query->where('alasan', $request->input('alasan'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('petugas_nama', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%")
                    ->orWhere('alasan_keterangan', 'like', "%{$q}%")
                    ->orWhereHas('aparLama', fn ($aq) => $aq->where('kode', 'like', "%{$q}%"))
                    ->orWhereHas('aparBaru', fn ($aq) => $aq->where('kode', 'like', "%{$q}%"));
            });
        }

        $penggantians = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PenggantianApar::count(),
            'rusak' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_RUSAK)->count(),
            'kadaluarsa' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_KADALUARSA)->count(),
            'peremajaan' => PenggantianApar::where('alasan', PenggantianApar::ALASAN_PENGGANTIAN_UNIT)->count(),
        ];

        return view('penggantian.index', compact('penggantians', 'stats'));
    }

    /**
     * Tampilkan formulir penggantian unit APAR lama dengan APAR baru (Section 8)
     */
    public function create($id)
    {
        $aparLama = is_numeric($id)
            ? Apar::with(['ruangan.lantai.gedung', 'penggantianKeluar.aparBaru'])->findOrFail($id)
            : Apar::with(['ruangan.lantai.gedung', 'penggantianKeluar.aparBaru'])->where('kode', $id)->firstOrFail();

        // Validasi: APAR lama tidak boleh diganti jika sudah pernah diganti (Section 24)
        if ($aparLama->is_replaced) {
            $penggantiKode = $aparLama->aparPengganti?->kode ?? 'unit pengganti';

            return redirect()->route('apar.show', $aparLama->id)
                ->with('warning', "Unit APAR {$aparLama->kode} sudah pernah digantikan oleh {$penggantiKode} dan tidak dapat diganti kembali.");
        }

        // Validasi: APAR berstatus Tidak Aktif tidak dapat diganti (Section 24)
        if ($aparLama->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $aparLama->id)
                ->with('error', "Unit APAR {$aparLama->kode} berstatus Tidak Aktif dan tidak memenuhi syarat untuk diproses penggantian.");
        }

        $gedungs = Gedung::with([
            'lantais' => fn ($q) => $q->orderBy('nomor_lantai', 'asc'),
            'lantais.ruangans' => fn ($q) => $q->orderBy('nama', 'asc'),
        ])->orderBy('nama', 'asc')->get();

        $mediaOptions = Apar::JENIS_MEDIA_OPTIONS;
        $alasanOptions = PenggantianApar::ALASAN_LABELS;
        $tanggalHariIni = Carbon::today()->format('Y-m-d');
        $petugasDefault = auth()->user()?->name ?? 'Petugas K3RS';

        return view('apar.ganti', compact(
            'aparLama',
            'gedungs',
            'mediaOptions',
            'alasanOptions',
            'tanggalHariIni',
            'petugasDefault'
        ));
    }

    /**
     * Eksekusi transaksi penggantian APAR secara atomic (Section 12)
     */
    public function store(Request $request, $id)
    {
        $aparLama = is_numeric($id)
            ? Apar::with(['ruangan.lantai.gedung'])->findOrFail($id)
            : Apar::with(['ruangan.lantai.gedung'])->where('kode', $id)->firstOrFail();

        // 1. Guard check keamanan (Section 24)
        if ($aparLama->is_replaced) {
            return redirect()->route('apar.show', $aparLama->id)
                ->with('error', "Unit APAR {$aparLama->kode} sudah pernah digantikan sebelumnya. Tindakan penggantian ganda dicegah.");
        }

        if ($aparLama->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $aparLama->id)
                ->with('error', 'Tidak dapat melakukan penggantian pada APAR yang berstatus Tidak Aktif.');
        }

        // 2. Validasi input data APAR baru & informasi penggantian
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                'unique:apars,kode',
                Rule::notIn([$aparLama->kode]),
            ],
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
                        $fail('Ruangan / area yang dipilih tidak sesuai dengan lantai yang dipilih.');
                    }
                },
            ],
            'posisi_detail' => ['nullable', 'string', 'max:255'],
            'salin_titik_denah' => ['nullable', 'boolean'],
            'jenis_media' => ['required', 'string', Rule::in(Apar::JENIS_MEDIA_OPTIONS)],
            'kapasitas' => ['required', 'string', 'max:30'],
            'merek' => ['nullable', 'string', 'max:100'],
            'tipe_model' => ['nullable', 'string', 'max:100'],
            'tanggal_pemasangan' => ['nullable', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tanggal_pemasangan'],
            'tanggal_penggantian' => ['required', 'date'],
            'alasan' => ['required', 'string', Rule::in(array_keys(PenggantianApar::ALASAN_LABELS))],
            'alasan_keterangan' => ['nullable', 'string', 'max:255', 'required_if:alasan,lainnya'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'petugas_nama' => ['required', 'string', 'max:100'],
        ], [
            'kode.required' => 'Kode APAR baru wajib diisi.',
            'kode.unique' => 'Kode APAR baru tersebut sudah terdaftar di sistem rumah sakit.',
            'kode.not_in' => 'Kode APAR baru tidak boleh sama dengan kode APAR lama.',
            'ruangan_id.required' => 'Lokasi penempatan ruangan/area wajib ditentukan.',
            'jenis_media.required' => 'Pilih jenis media pemadam untuk APAR baru.',
            'kapasitas.required' => 'Kapasitas tabung APAR baru wajib diisi.',
            'tanggal_penggantian.required' => 'Tanggal penggantian unit wajib diisi.',
            'alasan.required' => 'Alasan penggantian unit wajib dipilih.',
            'alasan_keterangan.required_if' => 'Mohon jelaskan rincian jika memilih alasan lainnya.',
            'petugas_nama.required' => 'Nama petugas pelaksana penggantian wajib diisi.',
        ]);

        // 3. Eksekusi Atomic Transaction (Section 12)
        [$aparBaru, $penggantian] = DB::transaction(function () use ($aparLama, $validated) {
            // A. Generate QR Code unik baru (Section 13)
            $cleanKode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $validated['kode']));
            $qrCodeBaru = 'HAFIS-QR-'.$cleanKode.'-'.strtoupper(Str::random(6));

            // B. Penanganan titik denah lantai (Section 14 & 15)
            $koordinatX = null;
            $koordinatY = null;
            if (! empty($validated['salin_titik_denah']) && $aparLama->has_titik) {
                // Warisi koordinat denah lama ke unit baru
                $koordinatX = $aparLama->koordinat_x;
                $koordinatY = $aparLama->koordinat_y;

                // Kosongkan koordinat pada APAR lama agar 1 titik tidak dimiliki 2 unit aktif
                $aparLama->koordinat_x = null;
                $aparLama->koordinat_y = null;
            }

            // C. Simpan record APAR baru (Section 4)
            $aparBaru = Apar::create([
                'kode' => $validated['kode'],
                'nomor_register' => $validated['nomor_register'] ?? null,
                'qr_code' => $qrCodeBaru,
                'ruangan_id' => $validated['ruangan_id'],
                'posisi_detail' => $validated['posisi_detail'] ?? $aparLama->posisi_detail,
                'koordinat_x' => $koordinatX,
                'koordinat_y' => $koordinatY,
                'jenis_media' => $validated['jenis_media'],
                'kapasitas' => $validated['kapasitas'],
                'merek' => $validated['merek'] ?? null,
                'tipe_model' => $validated['tipe_model'] ?? null,
                'status' => Apar::STATUS_BAIK, // Unit baru selalu berstatus aktif dan kondisi Baik
                'tanggal_pemasangan' => $validated['tanggal_pemasangan'] ?? $validated['tanggal_penggantian'],
                'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'] ?? null,
                'keterangan' => 'Unit baru pengganti dari '.$aparLama->kode,
                'replaces_apar_id' => $aparLama->id,
            ]);

            // D. Catat entri replacement historis (Section 5, 10, 11)
            $penggantian = PenggantianApar::create([
                'apar_lama_id' => $aparLama->id,
                'apar_baru_id' => $aparBaru->id,
                'tanggal_penggantian' => $validated['tanggal_penggantian'],
                'alasan' => $validated['alasan'],
                'alasan_keterangan' => $validated['alasan_keterangan'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'petugas_nama' => $validated['petugas_nama'],
                'user_id' => auth()->id(),
            ]);

            // E. Nonaktifkan APAR lama (Section 2 & 3)
            $aparLama->status = Apar::STATUS_TIDAK_AKTIF;
            $aparLama->alasan_nonaktif = 'sudah_diganti';
            $aparLama->tanggal_nonaktif = $validated['tanggal_penggantian'];
            $aparLama->replaced_by_apar_id = $aparBaru->id;
            $aparLama->save();

            // F. Penanganan Jadwal Inspeksi Aktif (Section 16)
            $pendingJadwals = $aparLama->jadwals()
                ->wherePivot('status_pemeriksaan', 'belum_diperiksa')
                ->where('jadwals.status', 'berjalan')
                ->get();

            foreach ($pendingJadwals as $jadwal) {
                // Perbarui catatan di pivot APAR lama
                $jadwal->apars()->updateExistingPivot($aparLama->id, [
                    'catatan' => "Unit dinonaktifkan karena diganti oleh unit {$aparBaru->kode}",
                ]);

                // Hubungkan APAR baru ke jadwal tersebut sebagai target aktif yang perlu diperiksa
                $jadwal->apars()->syncWithoutDetaching([
                    $aparBaru->id => [
                        'status_pemeriksaan' => 'belum_diperiksa',
                        'catatan' => "Unit pengganti dari {$aparLama->kode}",
                    ],
                ]);
            }

            return [$aparBaru, $penggantian];
        });

        return redirect()->route('apar.show', $aparBaru->id)
            ->with('success', "Unit APAR {$aparLama->kode} berhasil digantikan oleh unit baru {$aparBaru->kode}. APAR lama dinonaktifkan dan tersimpan sebagai rekaman riwayat.");
    }

    /**
     * Tampilkan detail rincian peristiwa penggantian unit APAR (Section 19)
     */
    public function show(PenggantianApar $penggantian)
    {
        $penggantian->load([
            'aparLama.ruangan.lantai.gedung',
            'aparBaru.ruangan.lantai.gedung',
            'user',
        ]);

        return view('penggantian.show', compact('penggantian'));
    }
}
