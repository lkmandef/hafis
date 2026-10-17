<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemeriksaanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    private function createHierarchy(): array
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-RS-1',
            'nama' => 'Gedung Teratai',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-101',
            'nama' => 'Ruang Resepsionis',
            'kategori' => 'umum',
        ]);

        $apar = Apar::create([
            'kode' => 'APAR-WF-001',
            'nomor_register' => 'REG-WF-001',
            'qr_code' => 'HAFIS-QR-WF-001',
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Samping meja resepsionis',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Servvo',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2025-01-01',
            'tanggal_kadaluarsa' => '2028-01-01',
        ]);

        return [$gedung, $lantai, $ruangan, $apar];
    }

    /**
     * Halaman scan QR dapat dirender
     */
    public function test_scan_page_can_be_rendered(): void
    {
        $response = $this->get(route('scan.index'));
        $response->assertStatus(200);
        $response->assertSee('Scan APAR');
        $response->assertSee('Scan QR Code APAR');
    }

    /**
     * Scan pencarian kode valid mengarahkan ke detail APAR
     */
    public function test_scan_cari_with_valid_qr_redirects_to_apar_show(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => $apar->qr_code,
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('success');
    }

    /**
     * Scan pencarian dengan kode manual APAR juga bekerja
     */
    public function test_scan_cari_with_manual_code_redirects_to_apar_show(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => $apar->kode,
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('success');
    }

    /**
     * Scan kode tidak ditemukan menampilkan flash error
     */
    public function test_scan_cari_with_invalid_code_shows_error(): void
    {
        $response = $this->post(route('scan.cari.post'), [
            'kode' => 'APAR-NON-EXISTENT',
        ]);

        $response->assertRedirect(route('scan.index'));
        $response->assertSessionHas('error');
    }

    /**
     * APAR yang sudah diganti (is_replaced) memberikan warning saat discan
     */
    public function test_scan_cari_with_replaced_apar_shows_warning(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $apar->alasan_nonaktif = 'sudah_diganti';
        $apar->status = Apar::STATUS_TIDAK_AKTIF;
        $apar->tanggal_nonaktif = Carbon::today();
        $apar->save();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => $apar->kode,
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('warning');
    }

    /**
     * Halaman mulai pemeriksaan dapat diakses untuk APAR aktif
     */
    public function test_pemeriksaan_mulai_page_can_be_rendered(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $response = $this->get(route('pemeriksaan.mulai', ['apar_id' => $apar->id]));
        $response->assertStatus(200);
        $response->assertSee($apar->kode);
        $response->assertSee('Mulai Pemeriksaan APAR');
    }

    /**
     * Halaman formulir checklist dapat diakses
     */
    public function test_pemeriksaan_create_checklist_page_can_be_rendered(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $response = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id]));
        $response->assertStatus(200);
        $response->assertSee($apar->kode);
        $response->assertSee('Checklist Pemeriksaan Fisik APAR');
        $response->assertSee('Indikator tekanan / manometer');
    }

    /**
     * Simpan pemeriksaan dengan semua item 'baik' menghasilkan status 'baik'
     */
    public function test_pemeriksaan_store_with_all_baik_saves_hasil_keseluruhan_baik(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $items = [];
        foreach (Pemeriksaan::DEFAULT_CHECKLIST_ITEMS as $itemNama) {
            $items[] = [
                'nama' => $itemNama,
                'hasil' => 'baik',
                'catatan' => null,
            ];
        }

        $payload = [
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Petugas K3RS Test',
            'catatan_pemeriksaan' => 'Kondisi tabung prima dan lengkap.',
            'items' => $items,
        ];

        $this->actingAsPetugas(['name' => 'Petugas K3RS Test']);
        $response = $this->post(route('pemeriksaan.store'), $payload);

        $this->assertDatabaseHas('pemeriksaans', [
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Petugas K3RS Test',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->latest()->first();
        $this->assertCount(count(Pemeriksaan::DEFAULT_CHECKLIST_ITEMS), $pem->details);

        $apar->refresh();
        $this->assertEquals(Carbon::today()->toDateString(), $apar->tanggal_pemeriksaan_terakhir->toDateString());

        $response->assertRedirect(route('pemeriksaan.show', $pem->id));
    }

    /**
     * Jika minimal 1 checklist 'tidak_baik', hasil keseluruhan otomatis 'perlu_tindakan'
     */
    public function test_pemeriksaan_store_with_one_tidak_baik_automatically_sets_perlu_tindakan(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $items = [];
        $first = true;
        foreach (Pemeriksaan::DEFAULT_CHECKLIST_ITEMS as $itemNama) {
            $items[] = [
                'nama' => $itemNama,
                'hasil' => $first ? 'tidak_baik' : 'baik',
                'catatan' => $first ? 'Tekanan di bawah batas hijau' : null,
            ];
            $first = false;
        }

        $payload = [
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Petugas K3RS Test',
            'catatan_pemeriksaan' => 'Perlu pengecekan tekanan tabung segera.',
            'items' => $items,
        ];

        $response = $this->post(route('pemeriksaan.store'), $payload);

        $this->assertDatabaseHas('pemeriksaans', [
            'apar_id' => $apar->id,
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
        ]);

        $apar->refresh();
        // APAR status harus otomatis berubah menjadi perhatian
        $this->assertEquals(Apar::STATUS_PERHATIAN, $apar->status);
    }

    /**
     * APAR tidak aktif tidak boleh diperiksa
     */
    public function test_pemeriksaan_cannot_be_performed_on_inactive_apar(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $apar->status = Apar::STATUS_TIDAK_AKTIF;
        $apar->save();

        $response = $this->get(route('pemeriksaan.mulai', ['apar_id' => $apar->id]));
        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('warning');

        $responseCreate = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id]));
        $responseCreate->assertRedirect(route('apar.show', $apar->id));
        $responseCreate->assertSessionHas('warning');
    }

    /**
     * Halaman detail hasil pemeriksaan dapat dirender dengan detail checklist
     */
    public function test_pemeriksaan_show_page_can_be_rendered(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $pem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Budi Santoso',
            'tanggal_pemeriksaan' => Carbon::now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
            'catatan' => 'Semua komponen lengkap.',
        ]);

        $pem->details()->create([
            'item_nama' => 'Tekanan Jarum Manometer',
            'hasil' => 'baik',
            'urutan' => 0,
        ]);

        $response = $this->get(route('pemeriksaan.show', $pem->id));
        $response->assertStatus(200);
        $response->assertSee($apar->kode);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Tekanan Jarum Manometer');
    }

    private function defaultChecklistPayload(Apar $apar, ?Jadwal $jadwal = null): array
    {
        $items = [];
        foreach (Pemeriksaan::DEFAULT_CHECKLIST_ITEMS as $itemNama) {
            $items[] = [
                'nama' => $itemNama,
                'hasil' => 'baik',
                'catatan' => null,
            ];
        }

        return [
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal?->id,
            'pemeriksa_nama' => 'Petugas K3RS Test',
            'catatan_pemeriksaan' => 'Kondisi tabung prima dan lengkap.',
            'items' => $items,
        ];
    }

    /**
     * TEST 1 — Pemeriksaan pertama: APAR dapat diperiksa pada Jadwal A
     */
    public function test_apar_dapat_diperiksa_pertama_kali_pada_jadwal_a(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $jadwalA = Jadwal::create([
            'nama' => 'Jadwal Inspeksi Rutin A',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwalA->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);

        // Akses halaman mulai & create checklist
        $resMulai = $this->get(route('pemeriksaan.mulai', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalA->id]));
        $resMulai->assertStatus(200);

        $resCreate = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalA->id]));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Checklist Pemeriksaan Fisik APAR');

        // Simpan pemeriksaan
        $resStore = $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar, $jadwalA));
        $resStore->assertRedirect();

        $this->assertDatabaseHas('pemeriksaans', [
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwalA->id,
            'status' => 'selesai',
        ]);
    }

    /**
     * TEST 2 — Duplicate pada jadwal sama dicegah dan diarahkan ke pemeriksaan lama
     */
    public function test_duplicate_pada_jadwal_sama_dicegah_dan_diarahkan_ke_pemeriksaan_lama(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $jadwalA = Jadwal::create([
            'nama' => 'Jadwal Inspeksi Rutin A',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwalA->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);

        // Selesaikan pemeriksaan pertama pada Jadwal A
        $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar, $jadwalA));
        $firstPem = Pemeriksaan::where('apar_id', $apar->id)->where('jadwal_id', $jadwalA->id)->first();
        $this->assertNotNull($firstPem);

        // Coba akses mulai lagi pada jadwal yang sama
        $resMulaiLagi = $this->get(route('pemeriksaan.mulai', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalA->id]));
        $resMulaiLagi->assertRedirect(route('pemeriksaan.show', $firstPem->id));
        $resMulaiLagi->assertSessionHas('info');

        // Coba akses form create lagi pada jadwal yang sama
        $resCreateLagi = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalA->id]));
        $resCreateLagi->assertRedirect(route('pemeriksaan.show', $firstPem->id));

        // Coba submit store lagi pada jadwal yang sama
        $resStoreLagi = $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar, $jadwalA));
        $resStoreLagi->assertRedirect(route('pemeriksaan.show', $firstPem->id));

        // Pastikan tidak ada duplikasi record pemeriksaan di database
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->where('jadwal_id', $jadwalA->id)->count());
    }

    /**
     * TEST 3 & 4 — Pemeriksaan ulang pada jadwal baru diperbolehkan dan histori lama tetap ada
     */
    public function test_apar_yang_sudah_selesai_di_jadwal_a_dapat_diperiksa_di_jadwal_b_dan_histori_lengkap(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $jadwalA = Jadwal::create([
            'nama' => 'Jadwal Inspeksi A',
            'tanggal_mulai' => Carbon::now()->subMonth()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->subMonth()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwalA->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);
        $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar, $jadwalA));

        // Buat Jadwal B
        $jadwalB = Jadwal::create([
            'nama' => 'Jadwal Inspeksi B Baru',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwalB->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);

        // Checklist untuk Jadwal B harus bisa dibuka dan diisi
        $resCreateB = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalB->id]));
        $resCreateB->assertStatus(200);
        $resCreateB->assertSee('Checklist Pemeriksaan Fisik APAR');

        // Simpan checklist pada Jadwal B
        $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar, $jadwalB));

        // Verifikasi TEST 4: Terdapat 2 record pemeriksaan untuk APAR yang sama
        $this->assertEquals(2, Pemeriksaan::where('apar_id', $apar->id)->count());
        $this->assertDatabaseHas('pemeriksaans', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalA->id]);
        $this->assertDatabaseHas('pemeriksaans', ['apar_id' => $apar->id, 'jadwal_id' => $jadwalB->id]);
    }

    /**
     * TEST 5 & 6 — Status jadwal otomatis menjadi 'selesai' jika semua APAR sudah diperiksa, dan tetap 'berjalan' jika belum
     */
    public function test_jadwal_otomatis_selesai_ketika_semua_apar_diperiksa(): void
    {
        [$gedung, $lantai, $ruangan, $apar1] = $this->createHierarchy();
        $apar2 = Apar::create([
            'kode' => 'APAR-WF-002',
            'qr_code' => 'HAFIS-QR-WF-002',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2',
            'kapasitas' => '5 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Multi APAR',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwal->apars()->attach([
            $apar1->id => ['status_pemeriksaan' => 'belum_diperiksa'],
            $apar2->id => ['status_pemeriksaan' => 'belum_diperiksa'],
        ]);

        // Periksa APAR 1
        $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar1, $jadwal));
        $jadwal->refresh();
        // TEST 6: Masih ada 1 APAR belum diperiksa -> status tetap 'berjalan'
        $this->assertEquals('berjalan', $jadwal->status);

        // Periksa APAR 2
        $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar2, $jadwal));
        $jadwal->refresh();
        // TEST 5: Semua APAR sudah diperiksa -> status otomatis 'selesai'
        $this->assertEquals('selesai', $jadwal->status);
    }

    /**
     * TEST 7 — Jadwal lama tidak mengunci jadwal baru pada pemilihan active_jadwal
     */
    public function test_active_jadwal_memprioritaskan_jadwal_yang_belum_diperiksa(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $jadwalLama = Jadwal::create([
            'nama' => 'Jadwal Oktober Lama',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwalLama->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);
        $this->post(route('pemeriksaan.store'), $this->defaultChecklistPayload($apar, $jadwalLama));

        // Buat jadwal baru yang juga berstatus berjalan
        $jadwalBaru = Jadwal::create([
            'nama' => 'Jadwal Oktober Baru',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);
        $jadwalBaru->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);

        // Accessor active_jadwal harus mengembalikan $jadwalBaru (karena APAR belum diperiksa di sana)
        $this->assertEquals($jadwalBaru->id, $apar->active_jadwal->id);

        // Scan APAR tanpa parameter jadwal_id harus membuka Jadwal Baru, bukan Jadwal Lama yang terkunci
        $resScan = $this->post(route('scan.cari.post'), ['kode' => $apar->kode]);
        $resScan->assertRedirect(route('apar.show', ['id' => $apar->id]));

        $resMulai = $this->get(route('pemeriksaan.mulai', ['apar_id' => $apar->id]));
        $resMulai->assertStatus(200); // Tidak redirect ke hasil lama
        $resMulai->assertSee($jadwalBaru->nama);
    }

    /**
     * TEST 8 & 9 — Scan flow mempertahankan konteks jadwal_id dan manual lookup tetap berfungsi
     */
    public function test_scan_mempertahankan_jadwal_id_dan_manual_lookup_berfungsi(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Khusus',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);

        // TEST 8: Scan dengan jadwal_id mempertahankan jadwal_id saat redirect ke apar.show
        $resScanWithJadwal = $this->post(route('scan.cari.post'), [
            'kode' => $apar->qr_code,
            'jadwal_id' => $jadwal->id,
        ]);
        $resScanWithJadwal->assertRedirect(route('apar.show', ['id' => $apar->id, 'jadwal_id' => $jadwal->id]));

        // TEST 9: Manual lookup kode APAR tetap berfungsi dengan baik
        $resManual = $this->post(route('scan.cari.post'), [
            'kode' => $apar->kode,
        ]);
        $resManual->assertRedirect(route('apar.show', ['id' => $apar->id]));
        $resManual->assertSessionHas('success');
    }
}
