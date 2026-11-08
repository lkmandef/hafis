<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterLocationIntegrityReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected Gedung $gedungIgd;

    protected Lantai $lantai1;

    protected Lantai $lantai2;

    protected Ruangan $ruangIgd;

    protected Ruangan $ruangStr;

    protected Ruangan $ruangTrs;

    protected Ruangan $ruang119;

    protected Lokasi $lokasiIgd;

    protected Lokasi $lokasiStr;

    protected Lokasi $lokasi119;

    protected Lokasi $lokasiTrs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();

        // Siapkan struktur Gedung IGD sesuai spesifikasi otoritatif
        $this->gedungIgd = Gedung::firstOrCreate(
            ['kode' => 'GDG-IGD'],
            ['nama' => 'Gedung IGD', 'status' => 'aktif', 'keterangan' => 'Instalasi Gawat Darurat & Layanan Kritis']
        );

        $this->lantai1 = Lantai::firstOrCreate(
            ['gedung_id' => $this->gedungIgd->id, 'nomor_lantai' => 1],
            ['nama' => 'Lantai 1', 'keterangan' => 'Lantai operasional IGD dan Stroke Center']
        );

        $this->lantai2 = Lantai::firstOrCreate(
            ['gedung_id' => $this->gedungIgd->id, 'nomor_lantai' => 2],
            ['nama' => 'Lantai 2 IGD', 'keterangan' => 'Area rawat inap IGD']
        );

        // 4 Master Ruangan Fisik di Lantai 1
        $this->ruangIgd = Ruangan::firstOrCreate(
            ['kode' => 'IGD-01'],
            ['lantai_id' => $this->lantai1->id, 'nama' => 'Ruang IGD', 'kategori' => 'Pelayanan Gawat Darurat']
        );

        $this->ruangStr = Ruangan::firstOrCreate(
            ['kode' => 'IGD-02'],
            ['lantai_id' => $this->lantai1->id, 'nama' => 'Stroke Center', 'kategori' => 'Pelayanan Medis Khusus']
        );

        $this->ruangTrs = Ruangan::firstOrCreate(
            ['kode' => 'IGD-TRS'],
            ['lantai_id' => $this->lantai1->id, 'nama' => 'Ruang Triase', 'kategori' => 'Pelayanan Gawat Darurat']
        );

        $this->ruang119 = Ruangan::firstOrCreate(
            ['kode' => 'IGD-119'],
            ['lantai_id' => $this->lantai1->id, 'nama' => 'Ruang 119', 'kategori' => 'Pelayanan Gawat Darurat']
        );

        // 4 Titik Lokasi Operasional K3RS di Lantai 1
        $this->lokasiIgd = Lokasi::firstOrCreate(
            ['kode' => 'IGD1'],
            [
                'gedung_id' => $this->gedungIgd->id,
                'lantai_id' => $this->lantai1->id,
                'ruangan_id' => $this->ruangIgd->id,
                'nama' => 'IGD Lt. 1',
                'kategori' => 'Pelayanan Gawat Darurat',
                'tipe_lokasi' => 'ROOM',
                'status' => 'aktif',
            ]
        );

        $this->lokasiStr = Lokasi::firstOrCreate(
            ['kode' => 'STR'],
            [
                'gedung_id' => $this->gedungIgd->id,
                'lantai_id' => $this->lantai1->id,
                'ruangan_id' => $this->ruangStr->id,
                'nama' => 'STROKE CENTER',
                'kategori' => 'Pelayanan Medis Khusus',
                'tipe_lokasi' => 'ROOM',
                'status' => 'aktif',
            ]
        );

        $this->lokasi119 = Lokasi::firstOrCreate(
            ['kode' => '119'],
            [
                'gedung_id' => $this->gedungIgd->id,
                'lantai_id' => $this->lantai1->id,
                'ruangan_id' => $this->ruang119->id,
                'nama' => '119',
                'kategori' => 'Pelayanan Gawat Darurat',
                'tipe_lokasi' => 'ROOM',
                'status' => 'aktif',
            ]
        );

        $this->lokasiTrs = Lokasi::firstOrCreate(
            ['kode' => 'TRS'],
            [
                'gedung_id' => $this->gedungIgd->id,
                'lantai_id' => $this->lantai1->id,
                'ruangan_id' => $this->ruangTrs->id,
                'nama' => 'Ruang Triase',
                'kategori' => 'Pelayanan Gawat Darurat',
                'tipe_lokasi' => 'ROOM',
                'status' => 'kosong',
            ]
        );

        // APAR di IGD Lt. 1 (3 Powder 6kg, 1 CO2 3kg)
        for ($i = 1; $i <= 3; $i++) {
            Apar::firstOrCreate(
                ['kode' => "APAR-IGD-P0{$i}"],
                [
                    'qr_code' => "QR-IGD-P0{$i}",
                    'lokasi_id' => $this->lokasiIgd->id,
                    'ruangan_id' => $this->ruangIgd->id,
                    'posisi_detail' => "Pilar {$i}",
                    'jenis_media' => 'Dry Chemical Powder',
                    'kapasitas' => '6 kg',
                    'status' => 'aktif',
                ]
            );
        }
        Apar::firstOrCreate(
            ['kode' => 'APAR-IGD-C01'],
            [
                'qr_code' => 'QR-IGD-C01',
                'lokasi_id' => $this->lokasiIgd->id,
                'ruangan_id' => $this->ruangIgd->id,
                'posisi_detail' => 'Pintu Keluar',
                'jenis_media' => 'CO2 (Carbon Dioxide)',
                'kapasitas' => '3 kg',
                'status' => 'aktif',
            ]
        );

        // APAR di Stroke Center (1 Powder 6kg, 1 CO2 3kg)
        Apar::firstOrCreate(
            ['kode' => 'APAR-STR-P01'],
            [
                'qr_code' => 'QR-STR-P01',
                'lokasi_id' => $this->lokasiStr->id,
                'ruangan_id' => $this->ruangStr->id,
                'posisi_detail' => 'Dinding Utama',
                'jenis_media' => 'Dry Chemical Powder',
                'kapasitas' => '6 kg',
                'status' => 'aktif',
            ]
        );
        Apar::firstOrCreate(
            ['kode' => 'APAR-STR-C01'],
            [
                'qr_code' => 'QR-STR-C01',
                'lokasi_id' => $this->lokasiStr->id,
                'ruangan_id' => $this->ruangStr->id,
                'posisi_detail' => 'Dinding Belakang',
                'jenis_media' => 'CO2 (Carbon Dioxide)',
                'kapasitas' => '3 kg',
                'status' => 'aktif',
            ]
        );

        // APAR di 119 (1 Powder 6kg)
        Apar::firstOrCreate(
            ['kode' => 'APAR-119-P01'],
            [
                'qr_code' => 'QR-119-P01',
                'lokasi_id' => $this->lokasi119->id,
                'ruangan_id' => $this->ruang119->id,
                'posisi_detail' => 'Pos Operator',
                'jenis_media' => 'Dry Chemical Powder',
                'kapasitas' => '6 kg',
                'status' => 'aktif',
            ]
        );
    }

    /**
     * Test 1: Gedung IGD Lantai 1 memiliki sinkronisasi 1:1 antara Master Ruangan Fisik dan Titik Lokasi
     */
    public function test_gedung_igd_master_ruangan_and_titik_lokasi_are_fully_synchronized(): void
    {
        // Pastikan Ruang 119 terdaftar di ruangans dan lokasis
        $this->assertNotNull($this->ruang119);
        $this->assertEquals($this->lantai1->id, $this->ruang119->lantai_id);
        $this->assertEquals($this->ruang119->id, $this->lokasi119->ruangan_id);

        // Pastikan Ruang Triase terdaftar di ruangans dan lokasis
        $this->assertNotNull($this->ruangTrs);
        $this->assertEquals($this->ruangTrs->id, $this->lokasiTrs->ruangan_id);
        $this->assertEquals('kosong', $this->lokasiTrs->status);
        $this->assertEquals(0, $this->lokasiTrs->apars()->count());

        // Pastikan render halaman index memuat keempat entitas baik di titik lokasi maupun master ruangan
        $response = $this->get(route('lokasi.index'));
        $response->assertStatus(200);
        $response->assertSee('119');
        $response->assertSee('IGD Lt. 1');
        $response->assertSee('STROKE CENTER');
        $response->assertSee('Ruang Triase');
        $response->assertSee('IGD-01');
        $response->assertSee('IGD-02');
        $response->assertSee('IGD-TRS');
        $response->assertSee('IGD-119');
    }

    /**
     * Test 2: Lantai kosong (seperti Lantai 2 IGD) menampilkan counter 0 APAR dan banner informatif non-penempatan
     */
    public function test_empty_floor_displays_zero_apar_counter_and_informative_banner(): void
    {
        $this->assertEquals(0, $this->lantai2->apars_count);

        $response = $this->get(route('lokasi.index'));
        $response->assertStatus(200);
        $response->assertSee('Area Non-Penempatan APAR (0 Unit)');
        $response->assertSee('hanya ruangan yang memiliki APAR');
    }

    /**
     * Test 3: Kartu lokasi menampilkan breakdown rincian jenis media dan kapasitas APAR
     */
    public function test_card_displays_apar_media_breakdown_detail(): void
    {
        $this->lokasiIgd->load('apars');
        $rincian = $this->lokasiIgd->rincian_media;
        $this->assertStringContainsString('3 Powder 6kg', $rincian);
        $this->assertStringContainsString('1 CO2 3kg', $rincian);

        $response = $this->get(route('lokasi.index'));
        $response->assertStatus(200);
        $response->assertSee($rincian);
    }

    /**
     * Test 4: Ruangan pelayanan medis dilabeli kategori fungsional medis, bukan Zona Layanan / Utilitas
     */
    public function test_medical_service_areas_have_functional_medical_categories(): void
    {
        $this->assertEquals('Pelayanan Gawat Darurat', $this->lokasiIgd->kategori);
        $this->assertEquals('Pelayanan Medis Khusus', $this->lokasiStr->kategori);

        $response = $this->get(route('lokasi.index'));
        $response->assertStatus(200);
        $response->assertSee('Pelayanan Gawat Darurat');
        $response->assertSee('Pelayanan Medis Khusus');
    }

    /**
     * Test 5: Tab Master Ruangan menampilkan seluruh ruangan yang sudah tersinkronisasi
     */
    public function test_tab_ruangan_shows_all_synchronized_master_rooms(): void
    {
        $response = $this->get(route('lokasi.index', ['tab' => 'ruangan']));
        $response->assertStatus(200);
        $response->assertSee('Master Ruangan Fisik Terdaftar');
        $response->assertSee('IGD-01');
        $response->assertSee('IGD-119');
        $response->assertSee('IGD-TRS');
    }
}
