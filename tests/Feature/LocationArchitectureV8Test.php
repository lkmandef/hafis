<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationArchitectureV8Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    /**
     * Verifikasi hierarki lokasi otoritatif Gedung -> Lantai -> Lokasi K3RS -> APAR
     */
    public function test_location_hierarchy_and_accessors_behave_correctly(): void
    {
        $gedung = Gedung::create([
            'nama' => 'Gedung Bedah Terpadu',
            'kode' => 'GDG-GBT',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 2,
            'nama' => 'Lantai 2',
        ]);

        $lokasi = Lokasi::create([
            'kode' => 'LOK-GBT-KOR2',
            'nama' => 'Koridor Depan OK 5',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'tipe_lokasi' => Lokasi::TIPE_CORRIDOR,
            'status' => 'aktif',
        ]);

        $apar = Apar::create([
            'kode' => 'APAR-GBT-001',
            'qr_code' => 'HAFIS-QR-GBT001-TEST',
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => null,
            'posisi_detail' => 'Dinding pilar timur',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        // Verifikasi relasi & accessors
        $this->assertEquals($gedung->id, $apar->gedung->id);
        $this->assertEquals($lantai->id, $apar->lantai->id);
        $this->assertEquals($lokasi->id, $apar->lokasi->id);
        $this->assertNull($apar->ruangan_id);
        $this->assertStringContainsString('Gedung Bedah Terpadu', $apar->lokasi_lengkap);
        $this->assertStringContainsString('Koridor Depan OK 5', $apar->lokasi_lengkap);

        // Verifikasi query scopes
        $this->assertEquals(1, Apar::diGedung($gedung->id)->count());
        $this->assertEquals(1, Apar::diLantai($lantai->id)->count());
    }

    /**
     * Pendaftaran APAR baru di koridor/area terbuka (tanpa ruangan fisik tertutup) berhasil
     */
    public function test_can_create_apar_in_open_corridor_without_ruangan(): void
    {
        $gedung = Gedung::create([
            'nama' => 'Gedung Paviliun Melati',
            'kode' => 'GDG-MLT',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $payload = [
            'kode' => 'APAR-MLT-KOR-01',
            'nomor_register' => 'REG-MLT-01',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => null,
            'posisi_detail' => 'Samping lift pengunjung',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Servvo',
            'status' => 'baik',
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertRedirect();

        $apar = Apar::where('kode', 'APAR-MLT-KOR-01')->first();
        $this->assertNotNull($apar);
        $this->assertNull($apar->ruangan_id);
        $this->assertNotNull($apar->lokasi_id);
        $this->assertEquals($gedung->id, $apar->gedung->id);
    }

    /**
     * APAR di lokasi stok sediaan (Gudang K3RS) tidak memenuhi syarat inspeksi operasional
     */
    public function test_reserve_stock_apar_is_excluded_from_operational_inspection(): void
    {
        $gedung = Gedung::create([
            'nama' => 'Gedung K3RS',
            'kode' => 'GDG-K3RS',
            'status' => 'aktif',
        ]);

        $lokasiStok = Lokasi::create([
            'kode' => 'LOK-GUD-K3RS',
            'nama' => 'Gudang Penyimpanan K3RS',
            'gedung_id' => $gedung->id,
            'lantai_id' => null,
            'tipe_lokasi' => Lokasi::TIPE_WAREHOUSE,
            'status' => Lokasi::STATUS_STOK,
        ]);

        $aparStok = Apar::create([
            'kode' => 'APAR-STOK-001',
            'qr_code' => 'HAFIS-QR-STOK001',
            'lokasi_id' => $lokasiStok->id,
            'ruangan_id' => null,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $this->assertFalse($aparStok->isEligibleForInspection());
        $this->assertEquals(0, Apar::eligibleForInspection()->where('id', $aparStok->id)->count());

        // Domain invariant: pembuatan pemeriksaan fisik pada unit stok harus ditolak
        $this->expectException(DomainException::class);
        Pemeriksaan::create([
            'apar_id' => $aparStok->id,
            'pemeriksa_nama' => 'Petugas K3RS',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'periode_tahun' => 2026,
            'periode_bulan' => 11,
        ]);
    }

    /**
     * Rute denah interaktif telah dihapus secara bersih (404)
     */
    public function test_interactive_denah_routes_are_disabled(): void
    {
        $this->get('/penempatan')->assertStatus(404);
        $this->post('/penempatan/apar/1/titik', ['koordinat_x' => 10, 'koordinat_y' => 10])->assertStatus(404);
        $this->delete('/penempatan/apar/1/titik')->assertStatus(404);
        $this->post('/lokasi/lantai/1/denah', [])->assertStatus(404);
        $this->delete('/lokasi/lantai/1/denah')->assertStatus(404);
    }
}
