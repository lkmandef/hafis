<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TindakLanjutSoftDeletedAparTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Apar $apar;

    protected TindakLanjut $tindakLanjut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $gedung = Gedung::create(['kode' => 'G-UJI', 'nama' => 'Gedung Uji']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'kode' => 'R-101', 'nama' => 'Ruang 101']);

        $this->apar = Apar::create([
            'kode' => 'APAR-TEST-DEL-01',
            'nomor_register' => 'REG-DEL-01',
            'qr_code' => 'QR-DEL-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 Kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $this->apar->id,
            'user_id' => $this->admin->id,
            'pemeriksa_nama' => $this->admin->name,
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
            'periode_tahun' => 2026,
            'periode_bulan' => 11,
        ]);

        $this->tindakLanjut = TindakLanjut::create([
            'apar_id' => $this->apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'user_id' => $this->admin->id,
            'deskripsi_temuan' => 'Temuan simulasi pengujian APAR terhapus',
            'jenis_tindakan' => TindakLanjut::JENIS_PEMBERSIHAN,
            'uraian_tindakan' => 'Pembersihan rutin selang dan nozel',
            'status' => TindakLanjut::STATUS_DALAM_PROSES,
            'prioritas' => TindakLanjut::PRIORITAS_SEDANG,
            'petugas_pelaksana' => 'Teknisi Uji',
            'tanggal_tindakan' => now(),
        ]);
    }

    public function test_halaman_index_tindak_lanjut_tetap_dapat_diakses_saat_apar_soft_deleted(): void
    {
        // Soft delete unit APAR
        $this->apar->delete();
        $this->assertSoftDeleted('apars', ['id' => $this->apar->id]);

        $response = $this->actingAs($this->admin)->get(route('tindak-lanjut.index'));

        $response->assertOk();
        $response->assertSee('APAR-TEST-DEL-01');
        $response->assertSee('Terhapus');
    }

    public function test_halaman_detail_tindak_lanjut_tetap_dapat_diakses_saat_apar_soft_deleted(): void
    {
        // Soft delete unit APAR
        $this->apar->delete();
        $this->assertSoftDeleted('apars', ['id' => $this->apar->id]);

        $response = $this->actingAs($this->admin)->get(route('tindak-lanjut.show', $this->tindakLanjut->id));

        $response->assertOk();
        $response->assertSee('APAR-TEST-DEL-01');
    }

    public function test_halaman_edit_tindak_lanjut_tetap_dapat_diakses_saat_apar_soft_deleted(): void
    {
        // Soft delete unit APAR
        $this->apar->delete();
        $this->assertSoftDeleted('apars', ['id' => $this->apar->id]);

        $response = $this->actingAs($this->admin)->get(route('tindak-lanjut.edit', $this->tindakLanjut->id));

        $response->assertOk();
        $response->assertSee('APAR-TEST-DEL-01');
    }

    public function test_relasi_apar_pada_tindak_lanjut_menggunakan_with_trashed(): void
    {
        $this->apar->delete();

        $tl = TindakLanjut::find($this->tindakLanjut->id);

        $this->assertNotNull($tl->apar, 'Relasi apar() pada TindakLanjut harus memuat instance model meskipun berstatus soft-deleted.');
        $this->assertEquals($this->apar->id, $tl->apar->id);
        $this->assertTrue($tl->apar->trashed());
    }
}
