<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TindakLanjutController extends Controller
{
    /**
     * Tampilkan daftar riwayat tindak lanjut & pemeliharaan APAR
     */
    public function index(Request $request)
    {
        $query = TindakLanjut::with(['apar.ruangan.lantai.gedung', 'pemeriksaan', 'pemeriksaanDetail'])
            ->orderBy('tanggal_tindakan', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_tindakan', $request->input('jenis'));
        }

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('uraian_tindakan', 'like', "%{$q}%")
                    ->orWhere('petugas_pelaksana', 'like', "%{$q}%")
                    ->orWhere('catatan', 'like', "%{$q}%")
                    ->orWhereHas('apar', function ($aparQuery) use ($q) {
                        $aparQuery->where('kode', 'like', "%{$q}%")
                            ->orWhere('nomor_register', 'like', "%{$q}%");
                    });
            });
        }

        $tindakLanjuts = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => TindakLanjut::count(),
            'belum' => TindakLanjut::where('status', TindakLanjut::STATUS_BELUM_DITINDAKLANJUTI)->count(),
            'proses' => TindakLanjut::where('status', TindakLanjut::STATUS_DALAM_PROSES)->count(),
            'selesai' => TindakLanjut::where('status', TindakLanjut::STATUS_SELESAI)->count(),
            'dibatalkan' => TindakLanjut::where('status', TindakLanjut::STATUS_DIBATALKAN)->count(),
        ];

        return view('tindak-lanjut.index', compact('tindakLanjuts', 'stats'));
    }

    /**
     * Tampilkan formulir pembuatan tindak lanjut berdasarkan hasil pemeriksaan/temuan
     */
    public function create(Request $request)
    {
        $pemeriksaanId = $request->input('pemeriksaan_id');
        $detailId = $request->input('detail_id');
        $aparId = $request->input('apar_id');

        // Jika tidak ada pemeriksaan_id tapi ada apar_id, cari pemeriksaan terakhir yang perlu tindakan
        if (empty($pemeriksaanId) && ! empty($aparId)) {
            $latestPemeriksaan = Pemeriksaan::where('apar_id', $aparId)
                ->where('hasil_keseluruhan', Pemeriksaan::HASIL_PERLU_TINDAKAN)
                ->latest('tanggal_pemeriksaan')
                ->first();

            if ($latestPemeriksaan) {
                $pemeriksaanId = $latestPemeriksaan->id;
            } else {
                $latestPemeriksaan = Pemeriksaan::where('apar_id', $aparId)
                    ->latest('tanggal_pemeriksaan')
                    ->first();
                $pemeriksaanId = $latestPemeriksaan?->id;
            }
        }

        if (empty($pemeriksaanId)) {
            return redirect()->route('apar.index')
                ->with('warning', 'Pilih pemeriksaan fisik sumber terlebih dahulu untuk membuat tindak lanjut.');
        }

        $pemeriksaan = Pemeriksaan::with(['apar.ruangan.lantai.gedung', 'details'])->find($pemeriksaanId);
        if (! $pemeriksaan) {
            return redirect()->route('apar.index')
                ->with('error', 'Data pemeriksaan fisik sumber tidak ditemukan.');
        }

        $apar = $pemeriksaan->apar;

        // Cek detail temuan jika diberikan
        $detail = null;
        if (! empty($detailId)) {
            $detail = $pemeriksaan->details->firstWhere('id', (int) $detailId);

            if (! $detail) {
                return redirect()->route('pemeriksaan.show', $pemeriksaan->id)
                    ->with('error', 'Parameter temuan checklist tidak sesuai dengan pemeriksaan sumber.');
            }

            // Cegah duplikasi tindak lanjut untuk temuan checklist yang sama (Section 12)
            $existing = TindakLanjut::where('pemeriksaan_detail_id', $detail->id)->first();
            if ($existing) {
                return redirect()->route('tindak-lanjut.show', $existing->id)
                    ->with('info', "Tindak lanjut untuk temuan '{$detail->item_nama}' sudah pernah dibuat sebelumnya.");
            }
        }

        $petugasDefault = auth()->user()?->name ?? 'Petugas K3RS';
        $tanggalHariIni = Carbon::today()->format('Y-m-d');

        return view('tindak-lanjut.create', compact('pemeriksaan', 'apar', 'detail', 'petugasDefault', 'tanggalHariIni'));
    }

    /**
     * Simpan data tindak lanjut baru ke database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apar_id' => ['required', 'exists:apars,id'],
            'pemeriksaan_id' => ['required', 'exists:pemeriksaans,id'],
            'pemeriksaan_detail_id' => ['nullable', 'exists:pemeriksaan_details,id'],
            'jenis_tindakan' => [
                'required',
                'string',
                'in:perbaikan,pengisian_ulang,pembersihan,penggantian_komponen,pemeriksaan_ulang,lainnya',
            ],
            'uraian_tindakan' => ['required', 'string', 'max:1000'],
            'status' => [
                'required',
                'string',
                'in:belum_ditindaklanjuti,dalam_proses,selesai',
            ],
            'prioritas' => [
                'required',
                'string',
                'in:rendah,sedang,tinggi',
            ],
            'petugas_pelaksana' => ['required', 'string', 'max:100'],
            'tanggal_tindakan' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_tindakan'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'update_kondisi_apar' => ['nullable', 'boolean'],
        ], [
            'apar_id.required' => 'Identitas unit APAR wajib disertakan.',
            'pemeriksaan_id.required' => 'Pemeriksaan sumber wajib disertakan.',
            'jenis_tindakan.required' => 'Pilih jenis tindakan pemeliharaan.',
            'uraian_tindakan.required' => 'Uraian rincian tindakan wajib diisi.',
            'status.required' => 'Status tindak lanjut wajib dipilih.',
            'petugas_pelaksana.required' => 'Nama petugas pelaksana wajib diisi.',
            'tanggal_tindakan.required' => 'Tanggal pelaksanaan tindakan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal pelaksanaan tindakan.',
            'foto.image' => 'File dokumentasi harus berupa gambar (JPG/PNG).',
            'foto.max' => 'Ukuran foto dokumentasi tidak boleh melebihi 5 MB.',
        ]);

        // Verifikasi integritas relasi: APAR -> Pemeriksaan -> Detail (Section 15)
        $apar = Apar::findOrFail($validated['apar_id']);
        $pemeriksaan = Pemeriksaan::where('id', $validated['pemeriksaan_id'])
            ->where('apar_id', $apar->id)
            ->first();

        if (! $pemeriksaan) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pemeriksaan_id' => 'Data pemeriksaan sumber tidak cocok dengan unit APAR terkait.']);
        }

        $deskripsiTemuan = null;
        if (! empty($validated['pemeriksaan_detail_id'])) {
            $detail = PemeriksaanDetail::where('id', $validated['pemeriksaan_detail_id'])
                ->where('pemeriksaan_id', $pemeriksaan->id)
                ->first();

            if (! $detail) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['pemeriksaan_detail_id' => 'Detail temuan checklist tidak sesuai dengan pemeriksaan sumber.']);
            }

            // Cegah duplikasi tindak lanjut untuk temuan checklist yang sama (Section 12)
            $existing = TindakLanjut::where('pemeriksaan_detail_id', $detail->id)->first();
            if ($existing) {
                return redirect()->route('tindak-lanjut.show', $existing->id)
                    ->with('warning', 'Tindak lanjut untuk temuan checklist ini sudah dibuat sebelumnya.');
            }

            $deskripsiTemuan = $detail->item_nama.($detail->catatan ? ': '.$detail->catatan : '');
        } else {
            $deskripsiTemuan = $pemeriksaan->catatan ?: 'Temuan hasil inspeksi fisik unit APAR';
        }

        // Upload foto opsional (Section 14)
        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $request->file('foto')->store('tindak_lanjut_foto', 'public');
        }

        // Tanggal selesai otomatis terisi jika status selesai
        $tanggalSelesai = $validated['tanggal_selesai'] ?? null;
        if ($validated['status'] === TindakLanjut::STATUS_SELESAI && empty($tanggalSelesai)) {
            $tanggalSelesai = Carbon::today()->format('Y-m-d');
        }

        $petugasPelaksana = auth()->check() ? auth()->user()->name : $validated['petugas_pelaksana'];

        $tindakLanjut = DB::transaction(function () use ($apar, $pemeriksaan, $validated, $petugasPelaksana, $deskripsiTemuan, $fotoPath, $tanggalSelesai) {
            $tl = TindakLanjut::create([
                'apar_id' => $apar->id,
                'pemeriksaan_id' => $pemeriksaan->id,
                'pemeriksaan_detail_id' => $validated['pemeriksaan_detail_id'] ?? null,
                'user_id' => auth()->id(),
                'deskripsi_temuan' => $deskripsiTemuan,
                'jenis_tindakan' => $validated['jenis_tindakan'],
                'uraian_tindakan' => $validated['uraian_tindakan'],
                'status' => $validated['status'],
                'prioritas' => $validated['prioritas'],
                'petugas_pelaksana' => $petugasPelaksana,
                'tanggal_tindakan' => $validated['tanggal_tindakan'],
                'tanggal_selesai' => $tanggalSelesai,
                'catatan' => $validated['catatan'] ?? null,
                'foto_path' => $fotoPath,
            ]);

            // Pembaruan kondisi unit APAR secara manual/terkontrol jika diminta (Section 8)
            if (! empty($validated['update_kondisi_apar']) && $validated['status'] === TindakLanjut::STATUS_SELESAI) {
                if ($apar->status === Apar::STATUS_PERHATIAN || $apar->status === Apar::STATUS_RUSAK) {
                    $apar->status = Apar::STATUS_BAIK;
                    $apar->save();
                }
            }

            return $tl;
        });

        return redirect()->route('tindak-lanjut.show', $tindakLanjut->id)
            ->with('success', "Tindak lanjut pemeliharaan untuk APAR {$apar->kode} berhasil dibuat dan disimpan.");
    }

    /**
     * Tampilkan lembar rincian detail tindak lanjut & pemeliharaan (Section 10)
     */
    public function show(TindakLanjut $tindakLanjut)
    {
        $tindakLanjut->load([
            'apar.ruangan.lantai.gedung',
            'pemeriksaan.jadwal',
            'pemeriksaanDetail',
            'user',
        ]);

        return view('tindak-lanjut.show', compact('tindakLanjut'));
    }

    /**
     * Tampilkan formulir edit dan update status tindak lanjut (Section 13)
     */
    public function edit(TindakLanjut $tindakLanjut)
    {
        $tindakLanjut->load([
            'apar.ruangan.lantai.gedung',
            'pemeriksaan',
            'pemeriksaanDetail',
        ]);

        return view('tindak-lanjut.edit', compact('tindakLanjut'));
    }

    /**
     * Perbarui data dan status tindak lanjut
     */
    public function update(Request $request, TindakLanjut $tindakLanjut)
    {
        if ($request->input('status') === TindakLanjut::STATUS_DIBATALKAN && ! $request->user()?->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk membatalkan tiket tindak lanjut.');
        }

        $allowedStatuses = ['belum_ditindaklanjuti', 'dalam_proses', 'selesai'];
        if ($request->user()?->isAdmin() || $tindakLanjut->status === TindakLanjut::STATUS_DIBATALKAN) {
            $allowedStatuses[] = 'dibatalkan';
        }

        $validated = $request->validate([
            'jenis_tindakan' => [
                'required',
                'string',
                'in:perbaikan,pengisian_ulang,pembersihan,penggantian_komponen,pemeriksaan_ulang,lainnya',
            ],
            'uraian_tindakan' => ['required', 'string', 'max:1000'],
            'status' => [
                'required',
                'string',
                Rule::in($allowedStatuses),
            ],
            'prioritas' => [
                'required',
                'string',
                'in:rendah,sedang,tinggi',
            ],
            'petugas_pelaksana' => ['required', 'string', 'max:100'],
            'tanggal_tindakan' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_tindakan'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'update_kondisi_apar' => ['nullable', 'boolean'],
        ], [
            'jenis_tindakan.required' => 'Pilih jenis tindakan pemeliharaan.',
            'uraian_tindakan.required' => 'Uraian rincian tindakan wajib diisi.',
            'status.required' => 'Status tindak lanjut wajib dipilih.',
            'status.in' => 'Hanya Administrator yang memiliki wewenang untuk mengubah status tiket menjadi dibatalkan.',
            'petugas_pelaksana.required' => 'Nama petugas pelaksana wajib diisi.',
            'tanggal_tindakan.required' => 'Tanggal pelaksanaan tindakan wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal pelaksanaan tindakan.',
            'foto.image' => 'File dokumentasi harus berupa gambar (JPG/PNG).',
            'foto.max' => 'Ukuran foto dokumentasi tidak boleh melebihi 5 MB.',
        ]);

        // Upload foto baru jika ada
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            if ($tindakLanjut->foto_path && Storage::disk('public')->exists($tindakLanjut->foto_path)) {
                Storage::disk('public')->delete($tindakLanjut->foto_path);
            }
            $tindakLanjut->foto_path = $request->file('foto')->store('tindak_lanjut_foto', 'public');
        }

        // Tanggal selesai otomatis terisi jika status selesai
        $tanggalSelesai = $validated['tanggal_selesai'] ?? $tindakLanjut->tanggal_selesai;
        if ($validated['status'] === TindakLanjut::STATUS_SELESAI && empty($tanggalSelesai)) {
            $tanggalSelesai = Carbon::today()->format('Y-m-d');
        } elseif ($validated['status'] !== TindakLanjut::STATUS_SELESAI && empty($validated['tanggal_selesai'])) {
            $tanggalSelesai = null;
        }

        DB::transaction(function () use ($tindakLanjut, $validated, $tanggalSelesai) {
            $tindakLanjut->update([
                'jenis_tindakan' => $validated['jenis_tindakan'],
                'uraian_tindakan' => $validated['uraian_tindakan'],
                'status' => $validated['status'],
                'prioritas' => $validated['prioritas'],
                'petugas_pelaksana' => $validated['petugas_pelaksana'],
                'tanggal_tindakan' => $validated['tanggal_tindakan'],
                'tanggal_selesai' => $tanggalSelesai,
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Pembaruan kondisi unit APAR jika diminta (Section 8)
            if (! empty($validated['update_kondisi_apar']) && $validated['status'] === TindakLanjut::STATUS_SELESAI) {
                $apar = $tindakLanjut->apar;
                if ($apar && ($apar->status === Apar::STATUS_PERHATIAN || $apar->status === Apar::STATUS_RUSAK)) {
                    $apar->status = Apar::STATUS_BAIK;
                    $apar->save();
                }
            }
        });

        return redirect()->route('tindak-lanjut.show', $tindakLanjut->id)
            ->with('success', 'Data pemeliharaan dan status tindak lanjut berhasil diperbarui.');
    }

    /**
     * Aksi cepat transisi status tindak lanjut (Section 13: Belum Ditindaklanjuti -> Dalam Proses -> Selesai)
     */
    public function updateStatus(Request $request, TindakLanjut $tindakLanjut)
    {
        if ($tindakLanjut->status === TindakLanjut::STATUS_DIBATALKAN) {
            return redirect()->back()
                ->with('error', 'Tiket tindak lanjut yang telah dibatalkan tidak dapat diubah status pengerjaannya.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:belum_ditindaklanjuti,dalam_proses,selesai'],
            'update_kondisi_apar' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $statusLama = $tindakLanjut->status;
        $statusBaru = $validated['status'];

        $tanggalSelesai = $tindakLanjut->tanggal_selesai;
        if ($statusBaru === TindakLanjut::STATUS_SELESAI && empty($tanggalSelesai)) {
            $tanggalSelesai = Carbon::today()->format('Y-m-d');
        } elseif ($statusBaru !== TindakLanjut::STATUS_SELESAI) {
            $tanggalSelesai = null;
        }

        DB::transaction(function () use ($tindakLanjut, $statusBaru, $tanggalSelesai, $validated) {
            $tindakLanjut->status = $statusBaru;
            $tindakLanjut->tanggal_selesai = $tanggalSelesai;
            if (! empty($validated['catatan'])) {
                $catatanBaru = trim(($tindakLanjut->catatan ? $tindakLanjut->catatan."\n" : '').'['.Carbon::now()->format('d/m/Y H:i').'] '.$validated['catatan']);
                $tindakLanjut->catatan = $catatanBaru;
            }
            $tindakLanjut->save();

            // Opsi pembaruan kondisi APAR jika selesai
            if (! empty($validated['update_kondisi_apar']) && $statusBaru === TindakLanjut::STATUS_SELESAI) {
                $apar = $tindakLanjut->apar;
                if ($apar && ($apar->status === Apar::STATUS_PERHATIAN || $apar->status === Apar::STATUS_RUSAK)) {
                    $apar->status = Apar::STATUS_BAIK;
                    $apar->save();
                }
            }
        });

        return redirect()->back()
            ->with('success', "Status tindak lanjut berhasil diubah dari '{$statusLama}' menjadi '{$tindakLanjut->status_label}'.");
    }

    /**
     * Batalkan tiket tindak lanjut dengan alasan wajib untuk kebutuhan audit K3RS
     */
    public function batalkan(Request $request, TindakLanjut $tindakLanjut)
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk membatalkan tiket tindak lanjut.');
        }

        if ($tindakLanjut->status === TindakLanjut::STATUS_DIBATALKAN) {
            return redirect()->route('tindak-lanjut.show', $tindakLanjut->id)
                ->with('warning', 'Tiket tindak lanjut ini sudah dalam status dibatalkan.');
        }

        $validated = $request->validate([
            'alasan_pembatalan' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan tiket tindak lanjut wajib diisi untuk kebutuhan audit.',
            'alasan_pembatalan.min' => 'Alasan pembatalan minimal berisi 5 karakter.',
            'alasan_pembatalan.max' => 'Alasan pembatalan maksimal 1000 karakter.',
        ]);

        $userName = auth()->user()?->name ?? 'Administrator';
        $timestamp = Carbon::now()->format('d/m/Y H:i');
        $catatanAudit = "[{$timestamp}] Dibatalkan oleh {$userName}. Alasan: {$validated['alasan_pembatalan']}";
        $catatanLengkap = trim(($tindakLanjut->catatan ? $tindakLanjut->catatan."\n" : '').$catatanAudit);

        DB::transaction(function () use ($tindakLanjut, $validated, $catatanLengkap) {
            $tindakLanjut->update([
                'status' => TindakLanjut::STATUS_DIBATALKAN,
                'alasan_pembatalan' => $validated['alasan_pembatalan'],
                'dibatalkan_oleh_id' => auth()->id(),
                'dibatalkan_at' => Carbon::now(),
                'catatan' => $catatanLengkap,
            ]);
        });

        return redirect()->route('tindak-lanjut.show', $tindakLanjut->id)
            ->with('success', "Tiket tindak lanjut #{$tindakLanjut->id} berhasil dibatalkan dan tercatat dalam riwayat audit K3RS.");
    }

    /**
     * Hapus data tindak lanjut — dialihkan ke pembatalan beralasan demi kepatuhan audit trail K3RS
     */
    public function destroy(Request $request, TindakLanjut $tindakLanjut)
    {
        return $this->batalkan($request, $tindakLanjut);
    }
}
