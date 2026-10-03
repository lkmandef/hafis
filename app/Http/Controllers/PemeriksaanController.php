<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Jadwal;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PemeriksaanController extends Controller
{
    /**
     * Halaman persiapan inspeksi APAR (Tahap 5: Mulai Pemeriksaan)
     * Menghubungkan target APAR yang dipilih/discan dengan Jadwal Inspeksi aktif.
     */
    public function mulai(Request $request)
    {
        $aparId = $request->input('apar_id');
        $jadwalId = $request->input('jadwal_id');

        if (empty($aparId)) {
            return redirect()->route('scan.index')
                ->with('info', 'Silakan scan QR code atau masukkan kode APAR terlebih dahulu untuk memulai pemeriksaan.');
        }

        $apar = Apar::with(['ruangan.lantai.gedung'])->find($aparId);
        if (!$apar) {
            return redirect()->route('scan.index')
                ->with('error', 'Data APAR yang dipilih tidak ditemukan.');
        }

        // Validasi: APAR tidak aktif tidak boleh diperiksa (Section 17)
        if ($apar->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $apar->id)
                ->with('warning', 'Unit APAR ini berstatus Tidak Aktif dan tidak dapat dilakukan pemeriksaan.');
        }

        // Tentukan jadwal inspeksi terkait
        $jadwal = null;
        if (!empty($jadwalId)) {
            $jadwal = Jadwal::find($jadwalId);
        }

        // Fallback: periksa apakah APAR memiliki jadwal aktif
        if (!$jadwal) {
            $jadwal = $apar->active_jadwal;
        }

        // Cegah duplikasi pemeriksaan jika sudah selesai pada jadwal yang sama (Section 3)
        if ($jadwal) {
            $existing = Pemeriksaan::where('apar_id', $apar->id)
                ->where('jadwal_id', $jadwal->id)
                ->latest('tanggal_pemeriksaan')
                ->first();

            if ($existing && $existing->status === 'selesai') {
                return redirect()->route('pemeriksaan.show', $existing->id)
                    ->with('info', "Unit APAR {$apar->kode} sudah diperiksa pada jadwal '{$jadwal->nama}'. Berikut hasil checklist pemeriksaannya.");
            }
        }

        $waktuSekarang = Carbon::now();
        $petugas = auth()->user()?->name ?? 'Petugas K3RS';

        return view('pemeriksaan.mulai', compact('apar', 'jadwal', 'waktuSekarang', 'petugas'));
    }

    /**
     * Tampilkan formulir checklist pemeriksaan fisik APAR (Tahap 6)
     */
    public function create(Request $request)
    {
        $aparId = $request->input('apar_id');
        $jadwalId = $request->input('jadwal_id');

        if (empty($aparId)) {
            return redirect()->route('scan.index')
                ->with('info', 'Pilih atau scan unit APAR terlebih dahulu.');
        }

        $apar = Apar::with(['ruangan.lantai.gedung'])->find($aparId);
        if (!$apar) {
            return redirect()->route('scan.index')
                ->with('error', 'Data unit APAR tidak ditemukan.');
        }

        if ($apar->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $apar->id)
                ->with('warning', 'Unit APAR berstatus Tidak Aktif tidak dapat dilakukan pemeriksaan.');
        }

        $jadwal = null;
        if (!empty($jadwalId)) {
            $jadwal = Jadwal::find($jadwalId);
        }
        if (!$jadwal) {
            $jadwal = $apar->active_jadwal;
        }

        // Cegah duplicate pemeriksaan untuk APAR + jadwal yang sama (Section 3)
        if ($jadwal) {
            $existing = Pemeriksaan::where('apar_id', $apar->id)
                ->where('jadwal_id', $jadwal->id)
                ->latest('tanggal_pemeriksaan')
                ->first();

            if ($existing && $existing->status === 'selesai') {
                return redirect()->route('pemeriksaan.show', $existing->id)
                    ->with('info', "Pemeriksaan untuk unit APAR {$apar->kode} pada jadwal ini sudah selesai dilakukan.");
            }
        }

        $checklistItems = Pemeriksaan::DEFAULT_CHECKLIST_ITEMS;
        $petugas = auth()->user()?->name ?? 'Petugas K3RS';
        $waktuSekarang = Carbon::now();

        return view('pemeriksaan.create', compact('apar', 'jadwal', 'checklistItems', 'petugas', 'waktuSekarang'));
    }

    /**
     * Simpan hasil pemeriksaan fisik ke database beserta seluruh detail checklist (Tahap 6)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apar_id' => ['required', 'exists:apars,id'],
            'jadwal_id' => ['nullable', 'exists:jadwals,id'],
            'pemeriksa_nama' => ['required', 'string', 'max:100'],
            'kesimpulan_manual' => ['nullable', 'string', 'in:baik,perlu_tindakan'],
            'catatan_pemeriksaan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:5120'], // Max 5MB
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama' => ['required', 'string', 'max:200'],
            'items.*.hasil' => ['required', 'string', 'in:baik,tidak_baik,tidak_diperiksa'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'apar_id.required' => 'Identitas APAR wajib disertakan.',
            'pemeriksa_nama.required' => 'Nama petugas pemeriksa wajib diisi.',
            'items.required' => 'Checklist pemeriksaan fisik wajib diisi.',
            'items.*.hasil.required' => 'Setiap parameter checklist wajib memiliki pilihan hasil evaluasi.',
            'foto.image' => 'File foto dokumentasi harus berformat gambar (JPG/PNG).',
            'foto.max' => 'Ukuran foto dokumentasi tidak boleh melebihi 5 MB.',
        ]);

        $apar = Apar::findOrFail($validated['apar_id']);

        // Cegah pemeriksaan APAR tidak aktif (Section 17)
        if ($apar->status === Apar::STATUS_TIDAK_AKTIF) {
            return redirect()->route('apar.show', $apar->id)
                ->with('error', 'Tidak dapat menyimpan pemeriksaan untuk APAR berstatus Tidak Aktif.');
        }

        // Cegah duplicate pemeriksaan untuk APAR + Jadwal yang sama (Section 3 & 17)
        $jadwal = null;
        if (!empty($validated['jadwal_id'])) {
            $jadwal = Jadwal::find($validated['jadwal_id']);
            if ($jadwal) {
                $existing = Pemeriksaan::where('apar_id', $apar->id)
                    ->where('jadwal_id', $jadwal->id)
                    ->first();
                if ($existing && $existing->status === 'selesai') {
                    return redirect()->route('pemeriksaan.show', $existing->id)
                        ->with('warning', 'Pemeriksaan fisik untuk APAR ini pada jadwal tersebut telah tersimpan sebelumnya.');
                }
            }
        }

        // Tentukan Hasil Keseluruhan (Section 8: jika ada 1 item "Tidak Baik", otomatis "Perlu Tindakan")
        $hasTidakBaik = false;
        foreach ($validated['items'] as $itemData) {
            if ($itemData['hasil'] === 'tidak_baik') {
                $hasTidakBaik = true;
                break;
            }
        }

        $hasilKeseluruhan = $hasTidakBaik ? Pemeriksaan::HASIL_PERLU_TINDAKAN : ($validated['kesimpulan_manual'] ?? Pemeriksaan::HASIL_BAIK);

        // Upload foto opsional (Section 9)
        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $request->file('foto')->store('pemeriksaan_foto', 'public');
        }

        // Simpan menggunakan DB Transaction (Section 10)
        $pemeriksaan = DB::transaction(function () use ($apar, $jadwal, $validated, $hasilKeseluruhan, $fotoPath) {
            $pem = Pemeriksaan::create([
                'apar_id' => $apar->id,
                'jadwal_id' => $jadwal?->id,
                'user_id' => auth()->id(),
                'pemeriksa_nama' => $validated['pemeriksa_nama'],
                'tanggal_pemeriksaan' => Carbon::now(),
                'hasil_keseluruhan' => $hasilKeseluruhan,
                'status' => 'selesai',
                'catatan' => $validated['catatan_pemeriksaan'] ?? null,
                'foto_path' => $fotoPath,
            ]);

            // Simpan detail setiap item checklist
            foreach ($validated['items'] as $index => $itemData) {
                $pem->details()->create([
                    'item_nama' => $itemData['nama'],
                    'hasil' => $itemData['hasil'],
                    'catatan' => $itemData['catatan'] ?? null,
                    'urutan' => $index,
                ]);
            }

            // Update catatan tanggal pemeriksaan terakhir pada APAR
            $apar->tanggal_pemeriksaan_terakhir = Carbon::today();
            if ($hasilKeseluruhan === Pemeriksaan::HASIL_PERLU_TINDAKAN) {
                // Tandai kondisi tabung perlu perhatian jika ada temuan
                if ($apar->status === Apar::STATUS_BAIK) {
                    $apar->status = Apar::STATUS_PERHATIAN;
                }
            }
            $apar->save();

            // Jika terhubung dengan Jadwal Inspeksi, update status_pemeriksaan pada pivot jadwal_apars
            if ($jadwal) {
                $jadwal->apars()->syncWithoutDetaching([
                    $apar->id => [
                        'status_pemeriksaan' => 'sudah_diperiksa',
                        'catatan' => $validated['catatan_pemeriksaan'] ?? null,
                    ]
                ]);
            }

            return $pem;
        });

        return redirect()->route('pemeriksaan.show', $pemeriksaan->id)
            ->with('success', "Pemeriksaan fisik unit APAR {$apar->kode} berhasil disimpan dan dicatat dalam log riwayat.");
    }

    /**
     * Tampilkan detail lembar hasil checklist pemeriksaan fisik (Section 12)
     */
    public function show(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->load(['apar.ruangan.lantai.gedung', 'jadwal', 'user', 'details']);

        return view('pemeriksaan.show', compact('pemeriksaan'));
    }

    /**
     * Kompatibilitas route GET /pemeriksaan
     */
    public function index(Request $request)
    {
        if ($request->has('apar_id')) {
            return $this->mulai($request);
        }

        return redirect()->route('jadwal.index');
    }
}
