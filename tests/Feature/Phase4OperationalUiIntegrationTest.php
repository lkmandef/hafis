<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4OperationalUiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    private function createOperationalApar(array $attributes = []): Apar
    {
        $lokasi = Lokasi::create([
            'nama' => 'Lokasi Uji '.uniqid(),
            'kode' => null,
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $gedung = Gedung::create(['kode' => 'GDG-'.uniqid(), 'nama' => 'Gedung Uji '.uniqid(), 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Uji', 'kode' => 'R-'.uniqid()]);

        return Apar::create(array_merge([
            'kode' => 'TEMP-APAR-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-TEST-'.uniqid(),
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Yamato',
            'status' => Apar::STATUS_BAIK,
        ], $attributes));
    }

    /**
     * Skenario 1: Menu Hasil Inspeksi yang terlihat pada navigasi mengarah ke ikhtisar bulanan
     */
    public function test_visible_hasil_inspeksi_menu_points_to_monthly_overview(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // Sidebar harus memuat link Hasil Inspeksi yang menuju ke pemeriksaan.index
        $response->assertSee(route('pemeriksaan.index'));
        $response->assertSee('Hasil Inspeksi');

        // Buka rute pemeriksaan.index secara langsung
        $indexResponse = $this->get(route('pemeriksaan.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertViewIs('pemeriksaan.index');
        $indexResponse->assertViewHas(['totalWajib', 'totalSudah', 'totalBelum', 'persentaseKepatuhan']);
    }

    /**
     * Skenario 2: Progres bulanan hanya menghitung APAR aktif terpasang (mengecualikan unit tidak aktif dan stok sediaan)
     */
    public function test_monthly_progress_excludes_inactive_apar_and_stock(): void
    {
        // 2 APAR aktif terpasang
        $apar1 = $this->createOperationalApar(['kode' => 'APAR-AKTIF-01']);
        $apar2 = $this->createOperationalApar(['kode' => 'APAR-AKTIF-02']);

        // 1 APAR tidak aktif (legacy inactive)
        $aparTidakAktif = $this->createOperationalApar([
            'kode' => 'APAR-NONAKTIF-01',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        // 1 Lokasi stok sediaan GUDANG K3RS
        $lokasiStok = Lokasi::create([
            'nama' => 'GUDANG K3RS',
            'status' => Lokasi::STATUS_STOK,
        ]);
        $aparStok = $this->createOperationalApar([
            'kode' => 'APAR-STOK-01',
            'lokasi_id' => $lokasiStok->id,
            'status' => Apar::STATUS_BAIK,
        ]);

        $response = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $response->assertStatus(200);

        // Hanya 2 APAR aktif terpasang yang menjadi target wajib periksa
        $this->assertEquals(2, $response->viewData('totalWajib'));

        $aparsCollection = $response->viewData('apars');
        $this->assertCount(2, $aparsCollection);
        $this->assertTrue($aparsCollection->contains('id', $apar1->id));
        $this->assertTrue($aparsCollection->contains('id', $apar2->id));
        $this->assertFalse($aparsCollection->contains('id', $aparTidakAktif->id));
        $this->assertFalse($aparsCollection->contains('id', $aparStok->id));
    }

    /**
     * Skenario 3: Navigasi scanner mempertahankan parameter periode inspeksi terpilih
     */
    public function test_scanner_navigation_preserves_selected_period(): void
    {
        $response = $this->get(route('scan.index', ['bulan' => 5, 'tahun' => 2026]));
        $response->assertStatus(200);
        $response->assertViewHas('bulan', 5);
        $response->assertViewHas('tahun', 2026);

        // Pastikan form manual dan auto-scan memiliki input tersembunyi untuk bulan dan tahun
        $response->assertSee('name="bulan" value="5"', false);
        $response->assertSee('name="tahun" value="2026"', false);

        // Jika user submit tanpa kode, redirect tetap membawa parameter bulan dan tahun
        $emptySubmit = $this->post(route('scan.cari'), [
            'kode' => '',
            'bulan' => 5,
            'tahun' => 2026,
        ]);
        $emptySubmit->assertRedirect(route('scan.index', ['bulan' => 5, 'tahun' => 2026]));
        $emptySubmit->assertSessionHas('error');
    }

    /**
     * Skenario 4: Scanning APAR yang sudah diperiksa pada periode terpilih langsung membuka hasil/detail pemeriksaannya
     */
    public function test_scanning_already_inspected_apar_opens_existing_monthly_record(): void
    {
        $apar = $this->createOperationalApar();
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 8,
            'pemeriksa_nama' => 'Petugas K3RS',
            'tanggal_pemeriksaan' => Carbon::create(2026, 8, 15),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $response = $this->post(route('scan.cari'), [
            'kode' => $apar->kode,
            'bulan' => 8,
            'tahun' => 2026,
        ]);

        // Harus dialihkan ke halaman show pemeriksaan yang sudah ada, bukan membuat duplicate
        $response->assertRedirect(route('pemeriksaan.show', $pemeriksaan->id));
        $response->assertSessionHas('info');

        // Pastikan record tetap 1
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->where('periode_tahun', 2026)->where('periode_bulan', 8)->count());
    }

    /**
     * Skenario 5: Scanning APAR memenuhi syarat yang belum diperiksa langsung mengarahkan ke formulir checklist baru
     */
    public function test_scanning_eligible_apar_without_monthly_record_offers_correct_inspection_flow(): void
    {
        $apar = $this->createOperationalApar();

        $response = $this->post(route('scan.cari'), [
            'kode' => $apar->qr_code,
            'bulan' => 9,
            'tahun' => 2026,
        ]);

        // Mengarahkan langsung ke halaman create checklist fisik dengan periode terpreservasi
        $response->assertRedirect(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => 9,
            'periode_tahun' => 2026,
        ]));
        $response->assertSessionHas('success');
    }

    /**
     * Skenario 6: Metrik bulanan di dashboard konsisten dengan record pemeriksaan dan aturan kelaikan
     */
    public function test_dashboard_monthly_counts_match_underlying_monthly_records(): void
    {
        $now = Carbon::now();
        $tahun = (int) $now->year;
        $bulan = (int) $now->month;

        // Buat 3 APAR aktif operasional
        $aparBaik = $this->createOperationalApar(['kode' => 'APAR-METRIK-01']);
        $aparTindakan = $this->createOperationalApar(['kode' => 'APAR-METRIK-02']);
        $aparBelum = $this->createOperationalApar(['kode' => 'APAR-METRIK-03']);

        // 1 APAR tidak aktif (tidak boleh masuk target)
        $this->createOperationalApar([
            'kode' => 'APAR-METRIK-04',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        // Pemeriksaan 1: Baik
        Pemeriksaan::create([
            'apar_id' => $aparBaik->id,
            'periode_tahun' => $tahun,
            'periode_bulan' => $bulan,
            'pemeriksa_nama' => 'Pemeriksa 1',
            'tanggal_pemeriksaan' => $now,
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Pemeriksaan 2: Perlu Tindakan
        Pemeriksaan::create([
            'apar_id' => $aparTindakan->id,
            'periode_tahun' => $tahun,
            'periode_bulan' => $bulan,
            'pemeriksa_nama' => 'Pemeriksa 2',
            'tanggal_pemeriksaan' => $now,
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        $inspeksiData = $response->viewData('inspeksiBulanan');
        $this->assertNotNull($inspeksiData);
        $this->assertEquals(3, $inspeksiData['total_target']);
        $this->assertEquals(2, $inspeksiData['sudah_diperiksa']);
        $this->assertEquals(1, $inspeksiData['belum_diperiksa']);
        $this->assertEquals(1, $inspeksiData['baik']);
        $this->assertEquals(1, $inspeksiData['perlu_tindakan']);
        $this->assertEquals(67, $inspeksiData['progress_pct']); // round(2/3 * 100) = 67%
    }

    /**
     * Skenario 7: Riwayat pemeriksaan legacy dengan nilai periode null tetap dapat diakses
     */
    public function test_legacy_historical_inspections_remain_accessible(): void
    {
        $apar = $this->createOperationalApar();
        $legacyPemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'jadwal_id' => null,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Petugas Legacy',
            'tanggal_pemeriksaan' => Carbon::create(2025, 3, 10),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $response = $this->get(route('pemeriksaan.show', $legacyPemeriksaan->id));
        $response->assertStatus(200);
        $response->assertSee('Petugas Legacy');
        $response->assertSee($apar->kode);
    }

    /**
     * Skenario 8: Rute tindak lanjut, penggantian APAR, dan laporan tetap berfungsi
     */
    public function test_existing_follow_up_replacement_and_report_routes_remain_functional(): void
    {
        $apar = $this->createOperationalApar();

        // 1. Rute Tindak Lanjut
        $this->get(route('tindak-lanjut.index'))->assertStatus(200);

        // 2. Rute Penggantian APAR
        $this->get(route('penggantian.index'))->assertStatus(200);
        $this->get(route('apar.ganti', $apar->id))->assertStatus(200);

        // 3. Rute Laporan
        $this->get(route('laporan.index'))->assertStatus(200);
        $this->get(route('laporan.pemeriksaan'))->assertStatus(200);
        $this->get(route('laporan.temuan'))->assertStatus(200);

        // 4. Rute Jadwal Legacy
        $this->get(route('jadwal.index'))->assertStatus(200);
    }

    /**
     * Skenario 9: Scanner mendeteksi unit tidak memenuhi syarat (ineligible) dan menjaga fungsionalitas input manual
     */
    public function test_scanner_rejects_ineligible_apar_and_maintains_fallbacks(): void
    {
        // 1. APAR Tidak Aktif
        $aparInactive = $this->createOperationalApar([
            'kode' => 'APAR-INACTIVE-X',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        $inactiveResponse = $this->post(route('scan.cari'), [
            'kode' => $aparInactive->kode,
            'bulan' => 10,
            'tahun' => 2026,
        ]);
        $inactiveResponse->assertRedirect(route('apar.show', ['id' => $aparInactive->id]));
        $inactiveResponse->assertSessionHas('warning');

        // 2. APAR di Gudang Stok Sediaan
        $lokasiStok = Lokasi::create(['nama' => 'GUDANG K3RS', 'status' => Lokasi::STATUS_STOK]);
        $aparStok = $this->createOperationalApar([
            'kode' => 'APAR-STOK-X',
            'lokasi_id' => $lokasiStok->id,
        ]);

        $stockResponse = $this->post(route('scan.cari'), [
            'kode' => $aparStok->kode,
            'bulan' => 10,
            'tahun' => 2026,
        ]);
        $stockResponse->assertRedirect(route('apar.show', ['id' => $aparStok->id]));
        $stockResponse->assertSessionHas('warning');

        // 3. Fallback input case-insensitive
        $aparNormal = $this->createOperationalApar(['kode' => 'TEMP-APAR-555']);
        $caseInsensitiveResponse = $this->post(route('scan.cari'), [
            'kode' => 'temp-apar-555',
            'bulan' => 10,
            'tahun' => 2026,
        ]);
        $caseInsensitiveResponse->assertRedirect(route('pemeriksaan.create', [
            'apar_id' => $aparNormal->id,
            'periode_bulan' => 10,
            'periode_tahun' => 2026,
        ]));
    }
}
