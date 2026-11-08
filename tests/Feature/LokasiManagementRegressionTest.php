<?php

namespace Tests\Feature;

use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokasiManagementRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    /**
     * Test halaman lokasi memuat data hierarki, allGedungs, allLantais, dan allRuangans dari controller.
     */
    public function test_lokasi_index_loads_with_all_entities_and_relations(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Bedah Pusat', 'kode' => 'GBP', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1 Bedah']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang OK 1', 'kode' => 'OK-01', 'kategori' => 'Operasi']);

        $response = $this->get(route('lokasi.index'));
        $response->assertOk();

        // Verifikasi seluruh variabel data tersedia dari controller tanpa query inline di Blade
        $response->assertViewHas('gedungs');
        $response->assertViewHas('allGedungs');
        $response->assertViewHas('allLantais');
        $response->assertViewHas('allRuangans');

        $allRuangans = $response->viewData('allRuangans');
        $this->assertCount(1, $allRuangans);
        $this->assertEquals('Ruang OK 1', $allRuangans->first()->nama);
        $this->assertTrue($allRuangans->first()->relationLoaded('lantai'));
        $this->assertTrue($allRuangans->first()->lantai->relationLoaded('gedung'));

        $response->assertSee('Gedung Bedah Pusat');
        $response->assertSee('Lantai 1 Bedah');
        $response->assertSee('Ruang OK 1');
    }

    /**
     * Test parameter query ?tab= dihormati saat render server-side.
     */
    public function test_lokasi_index_supports_tab_query_parameters(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Tab Test', 'kode' => 'GTT', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1 Test']);
        Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Tab Test']);

        // 1. Tab Default: Hirarki
        $resDefault = $this->get(route('lokasi.index'));
        $resDefault->assertOk();
        $this->assertEquals('hirarki', $resDefault->viewData('tab'));

        // 2. Tab Gedung
        $resGedung = $this->get(route('lokasi.index', ['tab' => 'gedung']));
        $resGedung->assertOk();
        $this->assertEquals('gedung', $resGedung->viewData('tab'));
        $resGedung->assertSee('id="content-tab-gedung" class="bg-white border', false);

        // 3. Tab Lantai
        $resLantai = $this->get(route('lokasi.index', ['tab' => 'lantai']));
        $resLantai->assertOk();
        $this->assertEquals('lantai', $resLantai->viewData('tab'));
        $resLantai->assertSee('id="content-tab-lantai" class="bg-white border', false);

        // 4. Tab Ruangan
        $resRuangan = $this->get(route('lokasi.index', ['tab' => 'ruangan']));
        $resRuangan->assertOk();
        $this->assertEquals('ruangan', $resRuangan->viewData('tab'));
        $resRuangan->assertSee('id="content-tab-ruangan" class="bg-white border', false);
    }

    /**
     * Test CRUD operasi dan validasi Gedung.
     */
    public function test_gedung_crud_operations_and_validations(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Melati', 'kode' => 'MLT', 'status' => 'aktif']);

        // Update Gedung
        $resUpdate = $this->put(route('lokasi.gedung.update', $gedung->id), [
            'nama' => 'Gedung Melati Terpadu',
            'kode' => 'MLT-01',
            'status' => 'aktif',
            'keterangan' => 'Instalasi Rawat Inap',
        ]);
        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('gedungs', [
            'id' => $gedung->id,
            'nama' => 'Gedung Melati Terpadu',
            'kode' => 'MLT-01',
        ]);

        // Validasi Duplikasi Nama Gedung
        $resDup = $this->post(route('lokasi.gedung.store'), [
            'nama' => 'Gedung Melati Terpadu',
            'kode' => 'GDG-NEW',
        ]);
        $resDup->assertSessionHasErrors('nama');

        // Delete Gedung
        $resDelete = $this->delete(route('lokasi.gedung.destroy', $gedung->id));
        $resDelete->assertRedirect(route('lokasi.index'));
        $this->assertDatabaseMissing('gedungs', ['id' => $gedung->id]);
    }

    /**
     * Test CRUD operasi dan validasi Lantai.
     */
    public function test_lantai_crud_operations_and_validations(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Dahlia', 'kode' => 'DHL', 'status' => 'aktif']);
        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1 Dahlia',
        ]);

        // Update Lantai
        $resUpdate = $this->put(route('lokasi.lantai.update', $lantai->id), [
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1 - Poliklinik',
            'keterangan' => 'Poli Terpadu',
        ]);
        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('lantais', [
            'id' => $lantai->id,
            'nama' => 'Lantai 1 - Poliklinik',
        ]);

        // Validasi Duplikasi Nomor Lantai dalam Gedung yang Sama
        $resDup = $this->post(route('lokasi.lantai.store'), [
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai Duplikat',
        ]);
        $resDup->assertSessionHasErrors('nomor_lantai');

        // Delete Lantai
        $resDelete = $this->delete(route('lokasi.lantai.destroy', $lantai->id));
        $resDelete->assertRedirect();
        $this->assertDatabaseMissing('lantais', ['id' => $lantai->id]);
    }

    /**
     * Test CRUD operasi dan validasi Ruangan.
     */
    public function test_ruangan_crud_operations_and_validations(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Mawar', 'kode' => 'MWR', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Rawat 101', 'kode' => 'RR-101']);

        // Update Ruangan
        $resUpdate = $this->put(route('lokasi.ruangan.update', $ruangan->id), [
            'nama' => 'Ruang Rawat VIP 101',
            'kode' => 'VIP-101',
            'kategori' => 'Rawat Inap',
            'keterangan' => 'Renovasi selesai',
        ]);
        $resUpdate->assertRedirect();
        $this->assertDatabaseHas('ruangans', [
            'id' => $ruangan->id,
            'nama' => 'Ruang Rawat VIP 101',
            'kode' => 'VIP-101',
        ]);

        // Validasi Duplikasi Nama Ruangan pada Lantai yang Sama
        $resDup = $this->post(route('lokasi.ruangan.store'), [
            'lantai_id' => $lantai->id,
            'nama' => 'Ruang Rawat VIP 101',
        ]);
        $resDup->assertSessionHasErrors('nama');

        // Delete Ruangan
        $resDelete = $this->delete(route('lokasi.ruangan.destroy', $ruangan->id));
        $resDelete->assertRedirect();
        $this->assertDatabaseMissing('ruangans', ['id' => $ruangan->id]);
    }

    /**
     * Test halaman detail gedung (lokasi.gedung.show).
     */
    public function test_show_gedung_detail_page_loads_with_relations(): void
    {
        $gedung = Gedung::create(['nama' => 'Gedung Flamboyan', 'kode' => 'FLB', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai Dasar']);
        Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Farmasi']);

        $response = $this->get(route('lokasi.gedung.show', $gedung->id));
        $response->assertOk();
        $response->assertViewIs('lokasi.show');
        $response->assertSee('Gedung Flamboyan');
        $response->assertSee('Lantai Dasar');
        $response->assertSee('Ruang Farmasi');
    }

    /**
     * Test tampilan empty state ketika tidak ada gedung atau hasil pencarian nihil.
     */
    public function test_lokasi_index_renders_empty_state_correctly(): void
    {
        $response = $this->get(route('lokasi.index', ['q' => 'GEDUNG_TIDAK_ADA']));
        $response->assertOk();
        $response->assertSeeText('Tidak ada data gedung yang cocok dengan pencarian "GEDUNG_TIDAK_ADA"');
    }
}
