<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AparMasterDataTest extends TestCase
{
    use RefreshDatabase;

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
            'nama' => 'Lantai 1 Gedung 2'
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantaiGedung2->id,
            'nama' => 'Ruangan Test'
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
}
