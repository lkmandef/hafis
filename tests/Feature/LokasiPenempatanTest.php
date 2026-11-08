<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokasiPenempatanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

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
     * Rute denah interaktif telah dihapus (Phase 2C.1)
     */
    public function test_penempatan_denah_routes_are_disabled(): void
    {
        $response = $this->get('/penempatan');
        $response->assertStatus(404);
    }
}
