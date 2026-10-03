<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokasiPenempatanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman index struktur lokasi dapat diakses
     */
    public function test_lokasi_index_page_can_be_rendered(): void
    {
        $response = $this->get(route('lokasi.index'));
        $response->assertStatus(200);
        $response->assertSee('Struktur Lokasi Rumah Sakit');
    }

    /**
     * Dapat menambahkan gedung baru
     */
    public function test_can_create_gedung(): void
    {
        $payload = [
            'nama' => 'Gedung Bedah Sentral',
            'kode' => 'GBS',
            'status' => 'aktif',
            'keterangan' => 'Instalasi Bedah Terpadu',
        ];

        $response = $this->post(route('lokasi.gedung.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('gedungs', [
            'nama' => 'Gedung Bedah Sentral',
            'kode' => 'GBS',
        ]);
    }

    /**
     * Dapat menambahkan lantai dalam gedung
     */
    public function test_can_create_lantai_under_gedung(): void
    {
        $gedung = Gedung::create([
            'nama' => 'Gedung Bedah',
            'kode' => 'GBD',
            'status' => 'aktif',
        ]);

        $payload = [
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 2,
            'nama' => 'Lantai 2 - Kamar Operasi',
        ];

        $response = $this->post(route('lokasi.lantai.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('lantais', [
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 2,
        ]);
    }

    /**
     * Dapat menambahkan ruangan dalam lantai
     */
    public function test_can_create_ruangan_under_lantai(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Utama', 'kode' => 'GDU']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);

        $payload = [
            'lantai_id' => $lantai->id,
            'kode' => 'OK-01',
            'nama' => 'Kamar Operasi 1',
            'kategori' => 'pelayanan',
        ];

        $response = $this->post(route('lokasi.ruangan.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('ruangans', [
            'lantai_id' => $lantai->id,
            'kode' => 'OK-01',
            'nama' => 'Kamar Operasi 1',
        ]);
    }

    /**
     * Halaman penempatan APAR pada denah lantai dapat diakses
     */
    public function test_penempatan_denah_page_can_be_rendered(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Rawat Inap', 'kode' => 'GRI']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'kode' => 'R-1', 'nama' => 'Ruang 1']);

        $apar = Apar::create([
            'kode' => 'APAR-DENAH-01',
            'qr_code' => 'QR-DENAH-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $response = $this->get(route('penempatan.index', [
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
        ]));

        $response->assertStatus(200);
        $response->assertSeeText('Titik Penempatan APAR');
        $response->assertSee('APAR-DENAH-01');
    }

    /**
     * Dapat menyimpan titik koordinat APAR pada denah
     */
    public function test_can_save_apar_coordinate_marker_on_floorplan(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Denah', 'kode' => 'GDD']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Perawatan']);

        $apar = Apar::create([
            'kode' => 'APAR-MARKER-01',
            'qr_code' => 'QR-MARKER-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Clean Agent',
            'kapasitas' => '3 kg',
            'status' => 'baik',
        ]);

        $response = $this->post(route('penempatan.titik.simpan', $apar->id), [
            'koordinat_x' => 45.5,
            'koordinat_y' => 60.2,
        ]);

        $response->assertRedirect();
        $apar->refresh();
        $this->assertEquals(45.5, $apar->koordinat_x);
        $this->assertEquals(60.2, $apar->koordinat_y);
        $this->assertTrue($apar->has_titik);
    }

    /**
     * Dapat menghapus titik koordinat APAR
     */
    public function test_can_delete_apar_coordinate_marker(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Marker', 'kode' => 'GDM']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Server']);

        $apar = Apar::create([
            'kode' => 'APAR-DEL-MARKER',
            'qr_code' => 'QR-DEL-MARKER',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => 'baik',
            'koordinat_x' => 30.0,
            'koordinat_y' => 50.0,
        ]);

        $this->assertTrue($apar->has_titik);

        $response = $this->delete(route('penempatan.titik.hapus', $apar->id));
        $response->assertRedirect();

        $apar->refresh();
        $this->assertNull($apar->koordinat_x);
        $this->assertNull($apar->koordinat_y);
        $this->assertFalse($apar->has_titik);
    }
}
