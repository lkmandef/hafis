<?php

namespace App\Http\Controllers;

use App\Models\Apar;
use App\Models\Jadwal;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    /**
     * Halaman Ikhtisar Pemeriksaan Bulanan APAR (Arsitektur K3RS Baru)
     * Menampilkan daftar seluruh unit APAR operasional dan status pemeriksaannya pada periode bulan/tahun berjalan.
     */
    public function index(Request $request)
    {
        // Kompatibilitas alur legacy: jika ada param apar_id, arahkan ke mulai()
        if ($request->has('apar_id')) {
            return $this->mulai($request);
        }

        $tahun = (int) $request->input('tahun', Carbon::now()->year);
        $bulan = (int) $request->input('bulan', Carbon::now()->month);

        if ($bulan < 1 || $bulan > 12) {
            $bulan = (int) Carbon::now()->month;
        }

        if ($tahun < 2000 || $tahun > 2100) {
            $tahun = (int) Carbon::now()->year;
        }

        $search = $request->input('q');
        $lokasiId = $request->input('lokasi_id');
        $statusPeriksa = $request->input('status_periksa'); // 'sudah', 'belum', null

        // Ambil seluruh master lokasi aktif untuk filter
        $lokasis = Lokasi::where('status', '!=', Lokasi::STATUS_STOK)
            ->orderBy('nama')
            ->get();

        // Query unit APAR terpasang operasional (tidak termasuk unit tidak aktif atau stok sediaan GUDANG K3RS)
        $query = Apar::with(['lokasi', 'ruangan.lantai.gedung', 'pemeriksaans' => function ($q) use ($tahun, $bulan) {
            $q->where('periode_tahun', $tahun)
                ->where('periode_bulan', $bulan);
        }])
            ->where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
            ->whereNotNull('lokasi_id')
            ->whereDoesntHave('lokasi', fn ($q) => $q->where('status', Lokasi::STATUS_STOK));

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('qr_code', 'like', "%{$search}%")
                    ->orWhereHas('lokasi', fn ($loc) => $loc->where('nama', 'like', "%{$search}%"));
            });
        }

        if (! empty($lokasiId)) {
            $query->where('lokasi_id', $lokasiId);
        }

        $aparsCollection = $query->orderBy('kode')->get();

        // Filter berdasarkan status pemeriksaan periode jika diminta
        if ($statusPeriksa === 'sudah') {
            $aparsCollection = $aparsCollection->filter(fn ($a) => $a->pemeriksaans->isNotEmpty());
        } elseif ($statusPeriksa === 'baik') {
            $aparsCollection = $aparsCollection->filter(fn ($a) => $a->pemeriksaans->first()?->hasil_keseluruhan === Pemeriksaan::HASIL_BAIK);
        } elseif ($statusPeriksa === 'perlu_tindakan') {
            $aparsCollection = $aparsCollection->filter(fn ($a) => $a->pemeriksaans->first()?->hasil_keseluruhan === Pemeriksaan::HASIL_PERLU_TINDAKAN);
        } elseif ($statusPeriksa === 'belum') {
            $aparsCollection = $aparsCollection->filter(fn ($a) => $a->pemeriksaans->isEmpty());
        }

        // Metrik kepatuhan operasional untuk seluruh unit wajib periksa (hanya APAR aktif terpasang)
        $totalWajib = Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
            ->whereNotNull('lokasi_id')
            ->whereDoesntHave('lokasi', fn ($q) => $q->where('status', Lokasi::STATUS_STOK))
            ->count();

        $eligibleAparFilter = fn ($q) => $q->where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
            ->whereNotNull('lokasi_id')
            ->whereDoesntHave('lokasi', fn ($loc) => $loc->where('status', Lokasi::STATUS_STOK));

        $totalSudah = Pemeriksaan::where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->whereHas('apar', $eligibleAparFilter)
            ->distinct('apar_id')
            ->count('apar_id');

        $totalBaik = Pemeriksaan::where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->where('hasil_keseluruhan', Pemeriksaan::HASIL_BAIK)
            ->whereHas('apar', $eligibleAparFilter)
            ->count();

        $totalPerluTindakan = Pemeriksaan::where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->where('hasil_keseluruhan', Pemeriksaan::HASIL_PERLU_TINDAKAN)
            ->whereHas('apar', $eligibleAparFilter)
            ->count();

        $totalBelum = max(0, $totalWajib - $totalSudah);
        $persentaseKepatuhan = $totalWajib > 0 ? round(($totalSudah / $totalWajib) * 100, 1) : 0;

        return view('pemeriksaan.index', [
            'apars' => $aparsCollection,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'totalWajib' => $totalWajib,
            'totalSudah' => $totalSudah,
            'totalBaik' => $totalBaik,
            'totalPerluTindakan' => $totalPerluTindakan,
            'totalBelum' => $totalBelum,
            'persentaseKepatuhan' => $persentaseKepatuhan,
            'lokasis' => $lokasis,
            'search' => $search,
            'lokasiId' => $lokasiId,
            'statusPeriksa' => $statusPeriksa,
        ]);
    }

    /**
     * Halaman persiapan inspeksi APAR (Tahap 5: Mulai Pemeriksaan)
     * Menghubungkan target APAR yang dipilih/discan dengan penugasan atau alur mandiri.
     */
    public function mulai(Request $request)
    {
        $aparId = $request->input('apar_id');
        $jadwalId = $request->input('jadwal_id');

        if (empty($aparId)) {
            return redirect()->route('scan.index')
                ->with('info', 'Silakan scan QR code atau masukkan kode APAR terlebih dahulu untuk memulai pemeriksaan.');
        }

        $apar = Apar::with(['lokasi', 'ruangan.lantai.gedung'])->find($aparId);
        if (! $apar) {
            return redirect()->route('scan.index')
                ->with('error', 'Data APAR yang dipilih tidak ditemukan.');
        }

        // Validasi: APAR harus memenuhi syarat operasional
        if (! $apar->isEligibleForInspection()) {
            return redirect()->route('apar.show', $apar->id)
                ->with('warning', 'Unit APAR ini tidak memenuhi syarat untuk dilakukan pemeriksaan operasional.');
        }

        // Tentukan jadwal inspeksi terkait jika ada
        $jadwal = null;
        if (! empty($jadwalId)) {
            $jadwal = Jadwal::find($jadwalId);
        }
        if (! $jadwal) {
            $jadwal = $apar->active_jadwal;
        }

        // Cegah duplikasi pemeriksaan jika sudah selesai pada jadwal yang sama
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
     * Tampilkan formulir checklist pemeriksaan fisik APAR
     */
    public function create(Request $request)
    {
        $aparId = $request->input('apar_id');
        $jadwalId = $request->input('jadwal_id');

        if (empty($aparId)) {
            return redirect()->route('scan.index')
                ->with('info', 'Pilih atau scan unit APAR terlebih dahulu.');
        }

        $apar = Apar::with(['lokasi', 'ruangan.lantai.gedung'])->find($aparId);
        if (! $apar) {
            return redirect()->route('scan.index')
                ->with('error', 'Data unit APAR tidak ditemukan.');
        }

        if (! $apar->isEligibleForInspection()) {
            return redirect()->route('apar.show', $apar->id)
                ->with('warning', 'Unit APAR tidak dapat dilakukan pemeriksaan karena berstatus tidak aktif atau berada di gudang stok.');
        }

        $jadwal = null;
        if (! empty($jadwalId)) {
            $jadwal = Jadwal::find($jadwalId);
        }
        if (! $jadwal) {
            $jadwal = $apar->active_jadwal;
        }

        // Cegah duplicate pemeriksaan untuk APAR + Jadwal yang sama pada alur legacy jadwal (Section 3)
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

        // Periode pemeriksaan
        $periodeTahun = $request->has('periode_tahun') ? (int) $request->input('periode_tahun') : (int) Carbon::now()->year;
        $periodeBulan = $request->has('periode_bulan') ? (int) $request->input('periode_bulan') : (int) Carbon::now()->month;

        // Cek apakah sudah ada pemeriksaan bulanan pada periode ini (Same-month correction routing)
        $existing = Pemeriksaan::findByAparPeriod($apar->id, $periodeTahun, $periodeBulan);
        if ($existing) {
            return redirect()->route('pemeriksaan.edit', $existing->id)
                ->with('info', "Unit APAR {$apar->kode} sudah memiliki catatan pemeriksaan untuk periode {$periodeBulan}/{$periodeTahun}. Formulir dialihkan ke mode koreksi.");
        }

        // Checklist items: 5 parameter K3RS jika bulanan, atau default jika legacy schedule ad-hoc
        // Workflow bulanan berlaku jika ada parameter periode eksplisit ATAU untuk inspeksi mandiri APAR terpasang operasional
        $isExplicitLegacySchedule = ! empty($jadwalId) && ! $request->has('periode_bulan') && ! $request->has('periode_tahun');
        $isMonthlyWorkflow = ! $isExplicitLegacySchedule && ($request->has('periode_bulan') || $request->has('periode_tahun') || ! empty($apar->lokasi_id));
        $checklistItems = $isMonthlyWorkflow ? Pemeriksaan::K3RS_CHECKLIST_ITEMS : Pemeriksaan::DEFAULT_CHECKLIST_ITEMS;

        $petugas = auth()->user()?->name ?? 'Petugas K3RS';
        $waktuSekarang = Carbon::now();

        return view('pemeriksaan.create', compact('apar', 'jadwal', 'checklistItems', 'petugas', 'waktuSekarang', 'periodeTahun', 'periodeBulan', 'isMonthlyWorkflow'));
    }

    /**
     * Simpan hasil pemeriksaan fisik ke database (Insert baru atau Same-Month Correction)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apar_id' => ['required', 'exists:apars,id'],
            'jadwal_id' => ['nullable', 'exists:jadwals,id'],
            'periode_tahun' => ['nullable', 'integer', 'between:2000,2100'],
            'periode_bulan' => ['nullable', 'integer', 'between:1,12'],
            'pemeriksa_nama' => ['required', 'string', 'max:100'],
            'tanggal_pemeriksaan' => ['nullable', 'date'],
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

        // Cegah pemeriksaan APAR tidak aktif atau stok
        if (! $apar->isEligibleForInspection()) {
            return redirect()->route('apar.show', $apar->id)
                ->with('error', 'Tidak dapat menyimpan pemeriksaan untuk APAR berstatus Tidak Aktif atau berada di lokasi stok.');
        }

        // Tentukan jadwal jika ada
        $jadwal = null;
        if (! empty($validated['jadwal_id'])) {
            $jadwal = Jadwal::find($validated['jadwal_id']);
        }

        // Cegah duplicate pemeriksaan untuk APAR + Jadwal yang sama (Legacy Schedule flow)
        if ($jadwal) {
            $existingOnJadwal = Pemeriksaan::where('apar_id', $apar->id)
                ->where('jadwal_id', $jadwal->id)
                ->first();
            if ($existingOnJadwal && $existingOnJadwal->status === 'selesai') {
                return redirect()->route('pemeriksaan.show', $existingOnJadwal->id)
                    ->with('warning', 'Pemeriksaan fisik untuk APAR ini pada jadwal tersebut telah tersimpan sebelumnya.');
            }
        }

        // Tanggal dan Periode
        $tanggalPemeriksaan = ! empty($validated['tanggal_pemeriksaan'])
            ? Carbon::parse($validated['tanggal_pemeriksaan'])
            : Carbon::now();

        $periodeTahun = $validated['periode_tahun']
            ?? ($jadwal ? (int) Carbon::parse($jadwal->tanggal_mulai)->year : (int) $tanggalPemeriksaan->year);
        $periodeBulan = $validated['periode_bulan']
            ?? ($jadwal ? (int) Carbon::parse($jadwal->tanggal_mulai)->month : (int) $tanggalPemeriksaan->month);

        $isMonthlyWorkflow = ! empty($validated['periode_bulan']) || ! empty($validated['periode_tahun']);
        $canonicalItems = Pemeriksaan::CANONICAL_CHECKLIST_ITEMS;

        // Validasi identitas 5 parameter kanonikal K3RS untuk pemeriksaan bulanan
        if ($isMonthlyWorkflow) {
            $submittedNames = array_map('trim', array_column($validated['items'], 'nama'));

            if (count($submittedNames) !== count($canonicalItems)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['items' => 'Checklist pemeriksaan bulanan harus memuat tepat 5 parameter K3RS.']);
            }

            $diff1 = array_diff($submittedNames, $canonicalItems);
            $diff2 = array_diff($canonicalItems, $submittedNames);
            if (! empty($diff1) || ! empty($diff2) || count($submittedNames) !== count(array_unique($submittedNames))) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['items' => 'Parameter checklist pemeriksaan bulanan tidak valid atau duplikat. Harus terdiri dari: '.implode(', ', $canonicalItems)]);
            }
        }

        // Tentukan Hasil Keseluruhan: jika ada 1 item "tidak_baik", otomatis "perlu_tindakan"
        $hasTidakBaik = false;
        foreach ($validated['items'] as $itemData) {
            if ($itemData['hasil'] === 'tidak_baik') {
                $hasTidakBaik = true;
                break;
            }
        }

        $hasilKeseluruhan = $hasTidakBaik
            ? Pemeriksaan::HASIL_PERLU_TINDAKAN
            : ($validated['kesimpulan_manual'] ?? Pemeriksaan::HASIL_BAIK);

        // Upload foto opsional
        $fotoPath = null;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $request->file('foto')->store('pemeriksaan_foto', 'public');
        }

        // Cek keberadaan record pemeriksaan untuk periode yang sama (baik alur mandiri maupun terjadwal)
        $existing = Pemeriksaan::findByAparPeriod($apar->id, $periodeTahun, $periodeBulan);

        try {
            $pemeriksaan = DB::transaction(function () use ($apar, $jadwal, $validated, $hasilKeseluruhan, $fotoPath, $tanggalPemeriksaan, $periodeTahun, $periodeBulan, $existing, $canonicalItems) {
                if ($existing) {
                    // Alur Koreksi: Perbarui record yang ada tanpa mengubah ID atau membuat duplikat
                    $existing->update([
                        'jadwal_id' => $jadwal?->id ?? $existing->jadwal_id,
                        'user_id' => auth()->id() ?? $existing->user_id,
                        'pemeriksa_nama' => $validated['pemeriksa_nama'],
                        'tanggal_pemeriksaan' => $tanggalPemeriksaan,
                        'hasil_keseluruhan' => $hasilKeseluruhan,
                        'status' => 'selesai',
                        'catatan' => $validated['catatan_pemeriksaan'] ?? $existing->catatan,
                        'foto_path' => $fotoPath ?? $existing->foto_path,
                    ]);

                    // Sinkronisasi item checklist berdasarkan identitas nama stabil, bukan index urutan request
                    $existingDetails = $existing->details()->get()->keyBy('item_nama');
                    foreach ($validated['items'] as $itemData) {
                        $nama = trim($itemData['nama']);
                        $canonicalIndex = array_search($nama, $canonicalItems);
                        $urutan = $canonicalIndex !== false ? $canonicalIndex : 0;

                        if (isset($existingDetails[$nama])) {
                            $existingDetails[$nama]->update([
                                'hasil' => $itemData['hasil'],
                                'catatan' => $itemData['catatan'] ?? null,
                                'urutan' => $urutan,
                            ]);
                        } else {
                            $existing->details()->create([
                                'item_nama' => $nama,
                                'hasil' => $itemData['hasil'],
                                'catatan' => $itemData['catatan'] ?? null,
                                'urutan' => $urutan,
                            ]);
                        }
                    }

                    // Hapus detail ekstra yang usang jika tidak terikat dengan tiket TindakLanjut
                    $submittedNames = array_map('trim', array_column($validated['items'], 'nama'));
                    $obsoleteDetails = $existing->details()->whereNotIn('item_nama', $submittedNames)->get();
                    foreach ($obsoleteDetails as $obsolete) {
                        if (! $obsolete->tindakLanjuts()->exists()) {
                            $obsolete->delete();
                        }
                    }

                    $pem = $existing;
                } else {
                    // Alur Baru: Buat record pemeriksaan bulanan
                    $pem = Pemeriksaan::create([
                        'apar_id' => $apar->id,
                        'jadwal_id' => $jadwal?->id,
                        'periode_tahun' => $periodeTahun,
                        'periode_bulan' => $periodeBulan,
                        'user_id' => auth()->id(),
                        'pemeriksa_nama' => $validated['pemeriksa_nama'],
                        'tanggal_pemeriksaan' => $tanggalPemeriksaan,
                        'hasil_keseluruhan' => $hasilKeseluruhan,
                        'status' => 'selesai',
                        'catatan' => $validated['catatan_pemeriksaan'] ?? null,
                        'foto_path' => $fotoPath,
                    ]);

                    // Simpan detail setiap item checklist
                    foreach ($validated['items'] as $itemData) {
                        $nama = trim($itemData['nama']);
                        $canonicalIndex = array_search($nama, $canonicalItems);
                        $urutan = $canonicalIndex !== false ? $canonicalIndex : 0;

                        $pem->details()->create([
                            'item_nama' => $nama,
                            'hasil' => $itemData['hasil'],
                            'catatan' => $itemData['catatan'] ?? null,
                            'urutan' => $urutan,
                        ]);
                    }
                }

                // Update status operasional & tanggal periksa terakhir APAR secara aman
                $apar->syncStatusAfterInspection($pem);

                // Jika terhubung dengan Jadwal Inspeksi legacy, update status pivot
                if ($jadwal) {
                    $jadwal->apars()->syncWithoutDetaching([
                        $apar->id => [
                            'status_pemeriksaan' => 'sudah_diperiksa',
                            'catatan' => $validated['catatan_pemeriksaan'] ?? null,
                        ],
                    ]);

                    $masihAdaBelumDiperiksa = $jadwal->apars()
                        ->wherePivot('status_pemeriksaan', 'belum_diperiksa')
                        ->exists();

                    if (! $masihAdaBelumDiperiksa) {
                        $jadwal->update(['status' => 'selesai']);
                    }
                }

                return $pem;
            });
        } catch (UniqueConstraintViolationException $e) {
            $existing = Pemeriksaan::findByAparPeriod($apar->id, $periodeTahun, $periodeBulan);
            if ($existing) {
                return redirect()->route('pemeriksaan.show', $existing->id)
                    ->with('info', "Unit APAR {$apar->kode} sudah memiliki catatan pemeriksaan untuk periode {$periodeBulan}/{$periodeTahun}.");
            }

            return redirect()->route('pemeriksaan.index', ['bulan' => $periodeBulan, 'tahun' => $periodeTahun])
                ->with('warning', "Pemeriksaan untuk unit APAR {$apar->kode} pada periode {$periodeBulan}/{$periodeTahun} sudah tersimpan sebelumnya.");
        } catch (DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        $pesan = $existing
            ? "Pemeriksaan bulanan unit APAR {$apar->kode} untuk periode {$periodeBulan}/{$periodeTahun} berhasil diperbarui (koreksi)."
            : "Pemeriksaan fisik unit APAR {$apar->kode} berhasil disimpan dan dicatat dalam kartu kendali.";

        return redirect()->route('pemeriksaan.show', $pemeriksaan->id)->with('success', $pesan);
    }

    /**
     * Tampilkan formulir koreksi pemeriksaan bulanan yang sudah ada
     */
    public function edit(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->load(['apar.lokasi', 'apar.ruangan.lantai.gedung', 'details', 'jadwal']);

        $apar = $pemeriksaan->apar;
        $jadwal = $pemeriksaan->jadwal;
        $periodeTahun = $pemeriksaan->periode_tahun ?? Carbon::now()->year;
        $periodeBulan = $pemeriksaan->periode_bulan ?? Carbon::now()->month;

        // Ambil rincian checklist yang sudah tersimpan
        $savedDetails = $pemeriksaan->details->keyBy('urutan');

        // Pastikan checklist items sesuai
        $checklistItems = $pemeriksaan->isMonthlyInspection()
            ? Pemeriksaan::K3RS_CHECKLIST_ITEMS
            : Pemeriksaan::DEFAULT_CHECKLIST_ITEMS;

        $petugas = $pemeriksaan->pemeriksa_nama;
        $waktuSekarang = $pemeriksaan->tanggal_pemeriksaan ?? Carbon::now();

        return view('pemeriksaan.edit', compact(
            'pemeriksaan',
            'apar',
            'jadwal',
            'checklistItems',
            'savedDetails',
            'petugas',
            'waktuSekarang',
            'periodeTahun',
            'periodeBulan'
        ));
    }

    /**
     * Perbarui data pemeriksaan fisik yang sudah ada (Koreksi)
     */
    public function update(Request $request, Pemeriksaan $pemeriksaan)
    {
        $validated = $request->validate([
            'pemeriksa_nama' => ['required', 'string', 'max:100'],
            'tanggal_pemeriksaan' => ['nullable', 'date'],
            'kesimpulan_manual' => ['nullable', 'string', 'in:baik,perlu_tindakan'],
            'catatan_pemeriksaan' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'max:5120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama' => ['required', 'string', 'max:200'],
            'items.*.hasil' => ['required', 'string', 'in:baik,tidak_baik,tidak_diperiksa'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
        ], [
            'pemeriksa_nama.required' => 'Nama petugas pemeriksa wajib diisi.',
            'items.required' => 'Checklist pemeriksaan fisik wajib diisi.',
            'items.*.hasil.required' => 'Setiap parameter checklist wajib memiliki pilihan hasil evaluasi.',
        ]);

        $apar = $pemeriksaan->apar;

        $canonicalItems = Pemeriksaan::CANONICAL_CHECKLIST_ITEMS;

        // Validasi identitas 5 parameter kanonikal K3RS jika ini pemeriksaan bulanan
        if ($pemeriksaan->isMonthlyInspection()) {
            $submittedNames = array_map('trim', array_column($validated['items'], 'nama'));

            if (count($submittedNames) !== count($canonicalItems)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['items' => 'Checklist pemeriksaan bulanan harus memuat tepat 5 parameter K3RS.']);
            }

            $diff1 = array_diff($submittedNames, $canonicalItems);
            $diff2 = array_diff($canonicalItems, $submittedNames);
            if (! empty($diff1) || ! empty($diff2) || count($submittedNames) !== count(array_unique($submittedNames))) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['items' => 'Parameter checklist pemeriksaan bulanan tidak valid atau duplikat. Harus terdiri dari: '.implode(', ', $canonicalItems)]);
            }
        }

        $hasTidakBaik = false;
        foreach ($validated['items'] as $itemData) {
            if ($itemData['hasil'] === 'tidak_baik') {
                $hasTidakBaik = true;
                break;
            }
        }

        $hasilKeseluruhan = $hasTidakBaik
            ? Pemeriksaan::HASIL_PERLU_TINDAKAN
            : ($validated['kesimpulan_manual'] ?? Pemeriksaan::HASIL_BAIK);

        $fotoPath = $pemeriksaan->foto_path;
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoPath = $request->file('foto')->store('pemeriksaan_foto', 'public');
        }

        $tanggalPemeriksaan = ! empty($validated['tanggal_pemeriksaan'])
            ? Carbon::parse($validated['tanggal_pemeriksaan'])
            : $pemeriksaan->tanggal_pemeriksaan;

        try {
            DB::transaction(function () use ($pemeriksaan, $apar, $validated, $hasilKeseluruhan, $fotoPath, $tanggalPemeriksaan, $canonicalItems) {
                $pemeriksaan->update([
                    'pemeriksa_nama' => $validated['pemeriksa_nama'],
                    'tanggal_pemeriksaan' => $tanggalPemeriksaan,
                    'hasil_keseluruhan' => $hasilKeseluruhan,
                    'catatan' => $validated['catatan_pemeriksaan'] ?? null,
                    'foto_path' => $fotoPath,
                ]);

                // Sinkronisasi item checklist berdasarkan identitas nama stabil, bukan index array
                $existingDetails = $pemeriksaan->details()->get()->keyBy('item_nama');
                foreach ($validated['items'] as $itemData) {
                    $nama = trim($itemData['nama']);
                    $canonicalIndex = array_search($nama, $canonicalItems);
                    $urutan = $canonicalIndex !== false ? $canonicalIndex : 0;

                    if (isset($existingDetails[$nama])) {
                        $existingDetails[$nama]->update([
                            'hasil' => $itemData['hasil'],
                            'catatan' => $itemData['catatan'] ?? null,
                            'urutan' => $urutan,
                        ]);
                    } else {
                        $pemeriksaan->details()->create([
                            'item_nama' => $nama,
                            'hasil' => $itemData['hasil'],
                            'catatan' => $itemData['catatan'] ?? null,
                            'urutan' => $urutan,
                        ]);
                    }
                }

                // Hapus detail ekstra jika tidak terikat dengan tiket TindakLanjut
                $submittedNames = array_map('trim', array_column($validated['items'], 'nama'));
                $obsoleteDetails = $pemeriksaan->details()->whereNotIn('item_nama', $submittedNames)->get();
                foreach ($obsoleteDetails as $obsolete) {
                    if (! $obsolete->tindakLanjuts()->exists()) {
                        $obsolete->delete();
                    }
                }

                // Update status operasional & tanggal periksa terakhir APAR secara aman
                $apar->syncStatusAfterInspection($pemeriksaan);
            });
        } catch (DomainException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('pemeriksaan.show', $pemeriksaan->id)
            ->with('success', "Pemeriksaan unit APAR {$apar->kode} berhasil diperbarui (koreksi).");
    }

    /**
     * Tampilkan detail lembar hasil checklist pemeriksaan fisik
     */
    public function show(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->load([
            'apar.lokasi',
            'apar.ruangan.lantai.gedung',
            'jadwal',
            'user',
            'details.tindakLanjuts',
            'tindakLanjuts',
        ]);

        return view('pemeriksaan.show', compact('pemeriksaan'));
    }
}
