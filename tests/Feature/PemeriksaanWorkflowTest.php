<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemeriksaanWorkflowTest extends TestCase
{
    use RefreshDatabase;

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
}
