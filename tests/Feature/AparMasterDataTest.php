<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AparMasterDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    /**
     * Halaman index master data APAR dapat diakses
     */
    public function test_apar_index_page_can_be_rendered(): void
    {
        $response = $this->get(route('apar.index'));
        $response->assertStatus(200);
        $response->assertSee('Master Data APAR');
    }

    /**
     * Halaman form tambah APAR dapat diakses
     */
    public function test_apar_create_page_can_be_rendered(): void
    {
        $response = $this->get(route('apar.create'));
        $response->assertStatus(200);
        $response->assertSee('Tambah Master Data APAR');
    }

    /**
     * Simpan APAR berhasil jika hierarki lokasi valid
     */
    public function test_can_create_apar_with_valid_cascading_location(): void
    {
        $gedung = Gedung::firstOrCreate(
            ['kode' => 'GDG-TEST'],
            ['nama' => 'Gedung Pengujian', 'status' => 'aktif']
        );

        $lantai = Lantai::firstOrCreate(
            ['gedung_id' => $gedung->id, 'nomor_lantai' => 99],
            ['nama' => 'Lantai 99 Testing']
        );

        $ruangan = Ruangan::firstOrCreate(
            ['lantai_id' => $lantai->id, 'nama' => 'Ruang Uji Coba'],
            ['kode' => 'R-UJI']
        );

        $payload = [
            'kode' => 'APAR-TEST-001',
            'nomor_register' => 'REG-TEST-001',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Dinding timur samping pintu masuk',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Servvo',
            'tipe_model' => 'Portable',
            'status' => 'baik',
            'tanggal_pemasangan' => '2026-01-10',
            'tanggal_kadaluarsa' => '2028-01-10',
            'keterangan' => 'Unit uji coba master data',
        ];

        $response = $this->post(route('apar.store'), $payload);

        $this->assertDatabaseHas('apars', [
            'kode' => 'APAR-TEST-001',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'status' => 'baik',
        ]);

        $apar = Apar::where('kode', 'APAR-TEST-001')->first();
        $this->assertNotNull($apar->qr_code);
        $this->assertStringContainsString('HAFIS-QR-', $apar->qr_code);

        $response->assertRedirect(route('apar.show', $apar->id));
    }

    /**
     * Validasi gagal jika lantai tidak cocok dengan gedung yang dipilih
     */
    public function test_validation_fails_if_lantai_does_not_belong_to_gedung(): void
    {
        $gedung1 = Gedung::create(['kode' => 'GDG-T1', 'nama' => 'Gedung Test 1']);
        $gedung2 = Gedung::create(['kode' => 'GDG-T2', 'nama' => 'Gedung Test 2']);

        $lantaiGedung2 = Lantai::create([
            'gedung_id' => $gedung2->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1 Gedung 2',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantaiGedung2->id,
            'nama' => 'Ruangan Test',
        ]);

        $payload = [
            'kode' => 'APAR-INVALID-LOC',
            'gedung_id' => $gedung1->id, // Gedung 1 dipilih
            'lantai_id' => $lantaiGedung2->id, // Tapi lantai milik Gedung 2
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertSessionHasErrors('lantai_id');
    }

    /**
     * Validasi gagal jika ruangan tidak cocok dengan lantai yang dipilih
     */
    public function test_validation_fails_if_ruangan_does_not_belong_to_lantai(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-T3', 'nama' => 'Gedung Test 3']);

        $lantai1 = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $lantai2 = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 2, 'nama' => 'Lantai 2']);

        $ruanganLantai2 = Ruangan::create(['lantai_id' => $lantai2->id, 'nama' => 'Ruangan Lantai 2']);

        $payload = [
            'kode' => 'APAR-INVALID-ROOM',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai1->id, // Lantai 1 dipilih
            'ruangan_id' => $ruanganLantai2->id, // Tapi ruangan ada di lantai 2
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertSessionHasErrors('ruangan_id');
    }

    /**
     * Validasi kode APAR harus unik
     */
    public function test_kode_apar_must_be_unique(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-T4', 'nama' => 'Gedung Test 4']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruangan Test']);

        Apar::create([
            'kode' => 'APAR-DUP-01',
            'qr_code' => 'QR-DUP-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $payload = [
            'kode' => 'APAR-DUP-01', // Kode duplikat
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '3 kg',
            'status' => 'baik',
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertSessionHasErrors('kode');
    }

    /**
     * Halaman detail menampilkan relasi lokasi dan spesifikasi unit APAR
     */
    public function test_apar_detail_page_shows_correct_information(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-T5', 'nama' => 'Gedung Paviliun']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 3, 'nama' => 'Lantai 3 VIP']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Kamar VIP 301']);

        $apar = Apar::create([
            'kode' => 'APAR-VIP-301',
            'nomor_register' => 'REG-VIP-01',
            'qr_code' => 'HAFIS-QR-VIP301-XYZ',
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Samping lemari obat',
            'jenis_media' => 'Clean Agent',
            'kapasitas' => '3 kg',
            'merek' => 'Yamato',
            'status' => 'baik',
        ]);

        $response = $this->get(route('apar.show', $apar->id));
        $response->assertStatus(200);
        $response->assertSee('APAR-VIP-301');
        $response->assertSee('Gedung Paviliun');
        $response->assertSee('Lantai 3 VIP');
        $response->assertSee('Kamar VIP 301');
        $response->assertSee('Clean Agent');
        $response->assertSee('HAFIS-QR-VIP301-XYZ');
    }

    /**
     * Update data APAR berhasil
     */
    public function test_can_update_apar(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-T6', 'nama' => 'Gedung Test 6']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Administrasi']);

        $apar = Apar::create([
            'kode' => 'APAR-UPDATE-01',
            'qr_code' => 'HAFIS-QR-UPD-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $payload = [
            'kode' => 'APAR-UPDATE-01',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'perhatian', // Update status
            'keterangan' => 'Perlu pengecekan tekanan segel',
        ];

        $response = $this->put(route('apar.update', $apar->id), $payload);
        $response->assertRedirect(route('apar.show', $apar->id));

        $this->assertDatabaseHas('apars', [
            'id' => $apar->id,
            'status' => 'perhatian',
            'keterangan' => 'Perlu pengecekan tekanan segel',
        ]);
    }

    /**
     * Soft delete unit APAR
     */
    public function test_can_soft_delete_apar(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-T7', 'nama' => 'Gedung Test 7']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Genset']);

        $apar = Apar::create([
            'kode' => 'APAR-DEL-01',
            'qr_code' => 'HAFIS-QR-DEL-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => 'rusak',
        ]);

        $response = $this->delete(route('apar.destroy', $apar->id));
        $response->assertRedirect(route('apar.index'));

        $this->assertSoftDeleted('apars', [
            'id' => $apar->id,
            'kode' => 'APAR-DEL-01',
        ]);
    }

    /**
     * Standardisasi toolbar filter Master APAR: form submit, placeholder akurat, dan kontrol status
     */
    public function test_apar_index_filter_toolbar_is_standardized(): void
    {
        $response = $this->get(route('apar.index'));
        $response->assertStatus(200);

        // 1. Placeholder akurat tanpa menyebut nomor register
        $response->assertSee('Cari kode APAR, QR, merek, atau lokasi...');
        $response->assertDontSee('Cari kode, register, merek, lokasi, keterangan...');

        // 2. Kontrol status aset via tab filter horizontal dan hidden status pada form toolbar
        $response->assertSee('name="status"', false);
        $response->assertSee('status=semua');
        $response->assertSee('status=baik');
        $response->assertSee('status=perhatian');
        $response->assertSee('status=rusak');
        $response->assertSee('status=sudah_diganti');
        $response->assertSee('status=tidak_aktif');

        // 3. Kontrol lokasi dan tombol Cari eksplisit
        $response->assertSee('name="lokasi_id"', false);
        $response->assertSee('Semua Lokasi K3RS');
        $response->assertSee('type="submit"', false);
        $response->assertSee('Cari');

        // 4. Aksi Tambah APAR berada pada header
        $response->assertSee('Tambah APAR');
        $response->assertSee(route('apar.create'));
    }

    /**
     * Filter status aset dan persistensi query string
     */
    public function test_apar_index_filters_by_asset_status_correctly(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-FLT', 'nama' => 'Gedung Filter']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Server']);

        $aparBaik = Apar::create([
            'kode' => 'APAR-FLT-BAIK',
            'qr_code' => 'HAFIS-QR-FLT-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $aparRusak = Apar::create([
            'kode' => 'APAR-FLT-RUSAK',
            'qr_code' => 'HAFIS-QR-FLT-02',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => Apar::STATUS_RUSAK,
        ]);

        $aparNonaktif = Apar::create([
            'kode' => 'APAR-FLT-NONAKTIF',
            'qr_code' => 'HAFIS-QR-FLT-03',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Foam (AFFF)',
            'kapasitas' => '6 L',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        // Filter: Kondisi Baik
        $resBaik = $this->get(route('apar.index', ['status' => 'baik']));
        $resBaik->assertStatus(200);
        $resBaik->assertSee($aparBaik->kode);
        $resBaik->assertDontSee($aparRusak->kode);
        $resBaik->assertDontSee($aparNonaktif->kode);

        // Filter: Kondisi Rusak
        $resRusak = $this->get(route('apar.index', ['status' => 'rusak']));
        $resRusak->assertStatus(200);
        $resRusak->assertSee($aparRusak->kode);
        $resRusak->assertDontSee($aparBaik->kode);

        // Filter: Unit Aktif (menampilkan baik & rusak, bukan tidak aktif)
        $resAktif = $this->get(route('apar.index', ['status' => 'aktif']));
        $resAktif->assertStatus(200);
        $resAktif->assertSee($aparBaik->kode);
        $resAktif->assertSee($aparRusak->kode);
        $resAktif->assertDontSee($aparNonaktif->kode);

        // Filter: Semua (menampilkan seluruh status)
        $resSemua = $this->get(route('apar.index', ['status' => 'semua']));
        $resSemua->assertStatus(200);
        $resSemua->assertSee($aparBaik->kode);
        $resSemua->assertSee($aparRusak->kode);
        $resSemua->assertSee($aparNonaktif->kode);

        // Tombol reset tersedia jika status bukan default 'aktif'
        $resRusak->assertSee('Reset');
        $resRusak->assertSee(route('apar.index', ['status' => 'semua']));
    }

    /**
     * Tombol cetak semua QR tampil di halaman index APAR
     */
    public function test_cetak_semua_qr_button_is_visible_on_index_page(): void
    {
        $response = $this->get(route('apar.index'));
        $response->assertStatus(200);
        $response->assertSee(route('apar.cetak_semua_qr'));
        $response->assertSee('Cetak Semua QR (A4)');
    }

    /**
     * Halaman cetak semua QR APAR menampilkan grid 4 stiker per halaman A4
     */
    public function test_can_access_cetak_semua_qr_page(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-BATCH', 'nama' => 'Gedung Batch']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang 101']);

        $apar1 = Apar::create([
            'kode' => 'APAR-BATCH-01',
            'qr_code' => 'HAFIS-QR-BATCH-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $apar2 = Apar::create([
            'kode' => 'APAR-BATCH-02',
            'qr_code' => 'HAFIS-QR-BATCH-02',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2',
            'kapasitas' => '5 kg',
            'status' => 'baik',
        ]);

        $response = $this->get(route('apar.cetak_semua_qr'));
        $response->assertStatus(200);
        $response->assertSee('Cetak Semua Label');
        $response->assertSee('APAR-BATCH-01');
        $response->assertSee('APAR-BATCH-02');
        $response->assertSee('Format Lembar A4 (Grid 2 &times; 2 &bull; 4 Stiker per Halaman)', false);
    }

    /**
     * Filter dropdown lokasi menyaring cetak stiker QR massal
     */
    public function test_cetak_semua_qr_filters_by_location(): void
    {
        $gedung = Gedung::firstOrCreate(['nama' => 'Gedung Alexandri Filter'], ['kode' => 'GDG-ALXF']);
        $lantai = Lantai::firstOrCreate(['gedung_id' => $gedung->id, 'nomor_lantai' => 1], ['nama' => 'Lantai 1']);
        $lokasiA = Lokasi::create([
            'nama' => 'ALEXANDRI Lt 1 Unik',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
        ]);
        $lokasiB = Lokasi::create([
            'nama' => 'Gedung IGD Lt 1 Unik',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
        ]);

        $aparAlexandri = Apar::create([
            'kode' => 'APAR-ALX-01',
            'qr_code' => 'QR-ALX-01',
            'lokasi_id' => $lokasiA->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $aparIgd = Apar::create([
            'kode' => 'APAR-IGD-01',
            'qr_code' => 'QR-IGD-01',
            'lokasi_id' => $lokasiB->id,
            'jenis_media' => 'CO2',
            'kapasitas' => '5 kg',
            'status' => 'baik',
        ]);

        // Cek halaman cetak dengan filter lokasi_id
        $response = $this->get(route('apar.cetak_semua_qr', ['lokasi_id' => $lokasiA->id]));
        $response->assertStatus(200);
        $response->assertSee('APAR-ALX-01');
        $response->assertDontSee('APAR-IGD-01');
        $response->assertSee('ALEXANDRI Lt 1 Unik');
        $response->assertSee('Cetak Hasil Filter (1 APAR)');

        // Cek bahwa tombol di index berubah adaptif saat terfilter
        $indexResponse = $this->get(route('apar.index', ['lokasi_id' => $lokasiA->id]));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Cetak QR Terfilter (1)');
    }

    /**
     * Pencarian kata kunci teks (nama ruangan/gedung) menyaring cetak stiker QR massal
     */
    public function test_cetak_semua_qr_filters_by_search_query_hierarchically(): void
    {
        $gedung = Gedung::firstOrCreate(['nama' => 'Gedung Medis Filter'], ['kode' => 'GDG-MEDF']);
        $lantai = Lantai::firstOrCreate(['gedung_id' => $gedung->id, 'nomor_lantai' => 2], ['nama' => 'Lantai 2']);
        $ruangFarmasi = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Farmasi Sentral']);
        $ruangRadiologi = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Radiologi']);

        $aparFarmasi = Apar::create([
            'kode' => 'APAR-FAR-01',
            'qr_code' => 'QR-FAR-01',
            'ruangan_id' => $ruangFarmasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $aparRadiologi = Apar::create([
            'kode' => 'APAR-RAD-01',
            'qr_code' => 'QR-RAD-01',
            'ruangan_id' => $ruangRadiologi->id,
            'jenis_media' => 'CO2',
            'kapasitas' => '5 kg',
            'status' => 'baik',
        ]);

        // Cari kata "farmasi"
        $response = $this->get(route('apar.cetak_semua_qr', ['q' => 'farmasi']));
        $response->assertStatus(200);
        $response->assertSee('APAR-FAR-01');
        $response->assertDontSee('APAR-RAD-01');
        $response->assertSee('Filter: &quot;farmasi&quot;', false);
        $response->assertSee('Cetak Hasil Filter (1 APAR)');

        // Tombol di index halaman
        $indexResponse = $this->get(route('apar.index', ['q' => 'farmasi']));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Cetak QR Terfilter (1)');
    }

    /**
     * Memastikan halaman cetak QR massal menggunakan ukuran kertas default A4 dan memiliki panduan potong simetris
     */
    public function test_cetak_semua_qr_defaults_to_a4_paper_and_has_cutting_guides(): void
    {
        $gedung = Gedung::firstOrCreate(['nama' => 'Gedung Uji A4'], ['kode' => 'GDG-UA4']);
        $lantai = Lantai::firstOrCreate(['gedung_id' => $gedung->id, 'nomor_lantai' => 1], ['nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Uji A4']);

        Apar::create([
            'kode' => 'APAR-A4-01',
            'qr_code' => 'QR-A4-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $response = $this->get(route('apar.cetak_semua_qr'));
        $response->assertStatus(200);

        // Verifikasi CSS Paged Media standar W3C untuk default ukuran A4 tanpa margin printer
        $response->assertSee('@page', false);
        $response->assertSee('size: A4 portrait;', false);
        $response->assertSee('margin: 0;', false);

        // Verifikasi dimensi lembar fisik A4 (210mm x 297mm)
        $response->assertSee('210mm', false);
        $response->assertSee('297mm', false);

        // Verifikasi pembagian 4 kuadran simetris (105mm x 148.5mm)
        $response->assertSee('grid-template-columns: 105mm 105mm', false);
        $response->assertSee('grid-template-rows: 148.5mm 148.5mm', false);

        // Verifikasi elemen garis panduan potong silang dan penanda gunting
        $response->assertSee('cut-line-v');
        $response->assertSee('cut-line-h');
        $response->assertSee('cut-badge-center');
        $response->assertSee('cut-badge-edge');
        $response->assertSee('POTONG');
    }
}
