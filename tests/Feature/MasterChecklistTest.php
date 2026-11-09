<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\MasterChecklist;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Database\Seeders\MasterChecklistSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterChecklistTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleApar(): Apar
    {
        $gedung = Gedung::create(['kode' => 'GDG-TEST', 'nama' => 'Gedung Uji', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Uji', 'kode' => 'R-01']);
        $lokasi = Lokasi::create(['nama' => 'Lokasi Uji', 'status' => Lokasi::STATUS_AKTIF]);

        return Apar::create([
            'kode' => 'APAR-TEST-01',
            'qr_code' => 'HAFIS-QR-TEST-01',
            'ruangan_id' => $ruangan->id,
            'lokasi_id' => $lokasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);
    }

    /**
     * Admin dapat melihat daftar master checklist pemeriksaan
     */
    public function test_admin_can_view_master_checklist_index(): void
    {
        $this->seed(MasterChecklistSeeder::class);
        $this->actingAsAdmin();

        $response = $this->get(route('pengaturan.checklist.index'));
        $response->assertStatus(200);
        $response->assertSee('Tekanan');
        $response->assertSee('Pin/Segel');
        $response->assertSee('Handle');
    }

    /**
     * Petugas biasa dilarang mengakses halaman pengaturan master checklist
     */
    public function test_petugas_cannot_access_master_checklist(): void
    {
        $this->actingAsPetugas();

        $this->get(route('pengaturan.checklist.index'))->assertStatus(403);
        $this->post(route('pengaturan.checklist.store'), ['nama' => 'Test Item'])->assertStatus(403);
    }

    /**
     * Admin dapat menambahkan kriteria pemeriksaan baru
     */
    public function test_admin_can_create_new_checklist_item(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('pengaturan.checklist.store'), [
            'nama' => 'Kebersihan Kotak APAR',
            'deskripsi_bantuan' => 'Kotak kaca dan box APAR bersih bebas debu dan sarang laba-laba',
            'urutan' => 6,
        ]);

        $response->assertRedirect(route('pengaturan.checklist.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('master_checklists', [
            'nama' => 'Kebersihan Kotak APAR',
            'urutan' => 6,
            'is_active' => 1,
            'is_default' => 0,
        ]);
    }

    /**
     * Validasi nama kriteria wajib dan unik
     */
    public function test_create_checklist_item_validation(): void
    {
        $this->actingAsAdmin();

        MasterChecklist::create([
            'nama' => 'Tekanan',
            'urutan' => 1,
            'is_active' => true,
        ]);

        // Coba submit nama kosong
        $resEmpty = $this->post(route('pengaturan.checklist.store'), ['nama' => '']);
        $resEmpty->assertSessionHasErrors('nama');

        // Coba submit nama duplikat
        $resDuplikat = $this->post(route('pengaturan.checklist.store'), ['nama' => 'Tekanan']);
        $resDuplikat->assertSessionHasErrors('nama');
    }

    /**
     * Admin dapat memperbarui parameter checklist
     */
    public function test_admin_can_update_checklist_item(): void
    {
        $this->actingAsAdmin();

        $item = MasterChecklist::create([
            'nama' => 'Nozel Asli',
            'deskripsi_bantuan' => 'Deskripsi lama',
            'urutan' => 5,
            'is_active' => true,
        ]);

        $response = $this->put(route('pengaturan.checklist.update', $item->id), [
            'nama' => 'Nozel & Selang Baru',
            'deskripsi_bantuan' => 'Selang lentur dan tidak tersumbat kotoran',
            'urutan' => 5,
        ]);

        $response->assertRedirect(route('pengaturan.checklist.index'));
        $this->assertDatabaseHas('master_checklists', [
            'id' => $item->id,
            'nama' => 'Nozel & Selang Baru',
            'deskripsi_bantuan' => 'Selang lentur dan tidak tersumbat kotoran',
        ]);
    }

    /**
     * Admin dapat mengaktifkan / menonaktifkan status parameter
     */
    public function test_admin_can_toggle_checklist_status(): void
    {
        $this->actingAsAdmin();

        $item1 = MasterChecklist::create(['nama' => 'Item 1', 'urutan' => 1, 'is_active' => true]);
        $item2 = MasterChecklist::create(['nama' => 'Item 2', 'urutan' => 2, 'is_active' => true]);

        // Nonaktifkan item 2
        $response = $this->post(route('pengaturan.checklist.toggle-status', $item2->id));
        $response->assertSessionHas('success');
        $this->assertFalse($item2->fresh()->is_active);

        // Aktifkan kembali
        $this->post(route('pengaturan.checklist.toggle-status', $item2->id));
        $this->assertTrue($item2->fresh()->is_active);
    }

    /**
     * Sistem mencegah penonaktifan seluruh parameter (harus ada minimal 1 aktif)
     */
    public function test_cannot_deactivate_all_checklist_items(): void
    {
        $this->actingAsAdmin();

        $item = MasterChecklist::create(['nama' => 'Satu-satunya Item', 'urutan' => 1, 'is_active' => true]);

        $response = $this->post(route('pengaturan.checklist.toggle-status', $item->id));
        $response->assertSessionHas('error');
        $this->assertTrue($item->fresh()->is_active);
    }

    /**
     * Parameter default standar K3RS dicegah untuk dihapus permanen
     */
    public function test_cannot_delete_default_k3rs_items(): void
    {
        $this->actingAsAdmin();

        $defaultItem = MasterChecklist::create([
            'nama' => 'Tekanan',
            'urutan' => 1,
            'is_active' => true,
            'is_default' => true,
        ]);

        $response = $this->delete(route('pengaturan.checklist.destroy', $defaultItem->id));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('master_checklists', ['id' => $defaultItem->id]);
    }

    /**
     * Formulir checklist pemeriksaan dan validasi berjalan dinamis berdasarkan master checklist aktif
     */
    public function test_dynamic_checklist_renders_in_pemeriksaan_form_and_validates(): void
    {
        $apar = $this->createSampleApar();
        $this->actingAsPetugas();

        // Buat 3 parameter khusus di database
        MasterChecklist::create(['nama' => 'Kriteria Alpha', 'urutan' => 1, 'is_active' => true]);
        MasterChecklist::create(['nama' => 'Kriteria Beta', 'urutan' => 2, 'is_active' => true]);
        MasterChecklist::create(['nama' => 'Kriteria Gamma', 'urutan' => 3, 'is_active' => true]);
        // Dan 1 parameter nonaktif
        MasterChecklist::create(['nama' => 'Kriteria Nonaktif', 'urutan' => 4, 'is_active' => false]);

        // 1. Periksa formulir create
        $responseForm = $this->get(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
        ]));
        $responseForm->assertStatus(200);
        $responseForm->assertSee('Kriteria Alpha');
        $responseForm->assertSee('Kriteria Beta');
        $responseForm->assertSee('Kriteria Gamma');
        $responseForm->assertDontSee('Kriteria Nonaktif');

        // 2. Submit pemeriksaan dengan 3 parameter aktif tersebut
        $payload = [
            'apar_id' => $apar->id,
            'periode_bulan' => 11,
            'periode_tahun' => 2026,
            'pemeriksa_nama' => 'Petugas Dinamis',
            'items' => [
                ['nama' => 'Kriteria Alpha', 'hasil' => 'baik', 'catatan' => null],
                ['nama' => 'Kriteria Beta', 'hasil' => 'baik', 'catatan' => null],
                ['nama' => 'Kriteria Gamma', 'hasil' => 'tidak_baik', 'catatan' => 'Ditemukan retak'],
            ],
        ];

        $responseStore = $this->post(route('pemeriksaan.store'), $payload);
        $responseStore->assertSessionHasNoErrors();

        // Pastikan tersimpan dengan benar
        $pem = Pemeriksaan::where('apar_id', $apar->id)->first();
        $this->assertNotNull($pem);
        $this->assertEquals(Pemeriksaan::HASIL_PERLU_TINDAKAN, $pem->hasil_keseluruhan);
        $this->assertCount(3, $pem->details);
        $this->assertDatabaseHas('pemeriksaan_details', [
            'pemeriksaan_id' => $pem->id,
            'item_nama' => 'Kriteria Gamma',
            'hasil' => 'tidak_baik',
            'catatan' => 'Ditemukan retak',
        ]);
    }
}
