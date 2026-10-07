<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanQrScannerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat unit APAR IGD-01 standar dengan token test tervalidasi
     */
    private function createTestApar(): Apar
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-RS-1',
            'nama' => 'Gedung Utama',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-IGD-01',
            'nama' => 'Zona Triase IGD',
            'kategori' => 'medis',
        ]);

        return Apar::create([
            'kode' => 'APAR-IGD-01',
            'nomor_register' => 'REG-IGD-001',
            'qr_code' => 'HAFIS-QR-APARIGD01-TEST',
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Pintu Masuk IGD',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '3 kg',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2025-01-01',
            'tanggal_kadaluarsa' => '2028-01-01',
        ]);
    }

    /**
     * Halaman scan QR menampilkan viewfinder, kontrol kamera, dan input manual
     */
    public function test_scan_page_contains_camera_viewfinder_and_fallback_elements(): void
    {
        $response = $this->get(route('scan.index'));

        $response->assertStatus(200);
        $response->assertSee('Scan QR Code APAR');
        $response->assertSee('id="camera-video"', false);
        $response->assertSee('id="btn-start-camera"', false);
        $response->assertSee('id="btn-stop-camera"', false);
        $response->assertSee('id="active-scanning-badge"', false);
        $response->assertSee('id="kode-manual-input"', false);
        $response->assertSee('Cari APAR');
        $response->assertSee('window.jsQR', false);
        $response->assertSee('BarcodeDetector', false);
    }

    /**
     * QR test tervalidasi HAFIS-QR-APARIGD01-TEST berhasil di-lookup dan membuka detail APAR IGD-01
     */
    public function test_scan_cari_with_hafis_qr_aparigd01_test_redirects_to_apar_igd_01(): void
    {
        $apar = $this->createTestApar();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => 'HAFIS-QR-APARIGD01-TEST',
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('success');

        $followed = $this->followRedirects($response);
        $followed->assertSee('APAR-IGD-01');
        $followed->assertSee('Zona Triase IGD');
    }

    /**
     * Manual input fallback dengan kode tabung fisik APAR-IGD-01 membuka detail APAR yang sama
     */
    public function test_manual_input_with_apar_kode_redirects_to_apar_show(): void
    {
        $apar = $this->createTestApar();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => 'APAR-IGD-01',
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('success');

        $followed = $this->followRedirects($response);
        $followed->assertSee('APAR-IGD-01');
    }

    /**
     * Pencarian QR atau kode tidak peka huruf besar/kecil (case-insensitive)
     */
    public function test_scan_cari_case_insensitivity(): void
    {
        $apar = $this->createTestApar();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => 'hafis-qr-aparigd01-test',
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('success');
    }

    /**
     * Payload URL lengkap dari pemindai eksternal tetap terurai ke kode APAR yang benar
     */
    public function test_scan_cari_with_url_payload(): void
    {
        $apar = $this->createTestApar();

        $response = $this->post(route('scan.cari.post'), [
            'kode' => 'http://hafis.local/scan/cari?kode=HAFIS-QR-APARIGD01-TEST',
        ]);

        $response->assertRedirect(route('apar.show', $apar->id));
        $response->assertSessionHas('success');
    }
}
