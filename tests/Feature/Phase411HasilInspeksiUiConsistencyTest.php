<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase411HasilInspeksiUiConsistencyTest extends TestCase
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
     * 1. Empat kartu statistik besar lama tidak lagi ditampilkan.
     */
    public function test_four_large_statistic_cards_are_not_displayed(): void
    {
        $this->createOperationalApar(['kode' => 'APAR-CARD-01']);

        $response = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $response->assertOk();

        // Kartu statistik besar lama tidak ada
        $response->assertDontSee('Target Periode');
        $response->assertDontSee('Total Terpasang');
        $response->assertDontSee('Bulan kalender operasional');
        $response->assertDontSee('tabung tersisa');
        $response->assertDontSee('59 lokasi titik operasional');
    }

    /**
     * 2. Empat tab tampil dengan label dan jumlah dinamis.
     */
    public function test_four_tabs_display_with_dynamic_counts(): void
    {
        $apar1 = $this->createOperationalApar(['kode' => 'APAR-01']);
        $apar2 = $this->createOperationalApar(['kode' => 'APAR-02']);
        $apar3 = $this->createOperationalApar(['kode' => 'APAR-03']);

        // Apar 1: Baik
        Pemeriksaan::create([
            'apar_id' => $apar1->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-05 09:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas A',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Apar 2: Perlu Tindakan
        Pemeriksaan::create([
            'apar_id' => $apar2->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-06 10:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas B',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        // Apar 3: Belum Diperiksa

        $response = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $response->assertOk();

        // Empat tab ada di halaman
        $response->assertSee('Semua');
        $response->assertSee('Sudah Diperiksa');
        $response->assertSee('Belum Diperiksa');
        $response->assertSee('Perlu Tindakan');

        // ViewData counts
        $this->assertEquals(3, $response->viewData('totalWajib'));
        $this->assertEquals(2, $response->viewData('totalSudah'));
        $this->assertEquals(1, $response->viewData('totalBelum'));
        $this->assertEquals(1, $response->viewData('totalPerluTindakan'));
    }

    /**
     * 3. Setiap tab menyaring data tabel secara benar.
     */
    public function test_each_tab_filters_table_data_correctly(): void
    {
        $aparBaik = $this->createOperationalApar(['kode' => 'APAR-BAIK-01']);
        $aparTindakan = $this->createOperationalApar(['kode' => 'APAR-TINDAKAN-01']);
        $aparBelum = $this->createOperationalApar(['kode' => 'APAR-BELUM-01']);

        Pemeriksaan::create([
            'apar_id' => $aparBaik->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-05 09:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas A',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        Pemeriksaan::create([
            'apar_id' => $aparTindakan->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-06 10:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas B',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        // Tab: Semua
        $resSemua = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $resSemua->assertSee('APAR-BAIK-01');
        $resSemua->assertSee('APAR-TINDAKAN-01');
        $resSemua->assertSee('APAR-BELUM-01');

        // Tab: Sudah Diperiksa
        $resSudah = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'sudah']));
        $resSudah->assertSee('APAR-BAIK-01');
        $resSudah->assertSee('APAR-TINDAKAN-01');
        $resSudah->assertDontSee('APAR-BELUM-01');

        // Tab: Belum Diperiksa
        $resBelum = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'belum']));
        $resBelum->assertSee('APAR-BELUM-01');
        $resBelum->assertDontSee('APAR-BAIK-01');
        $resBelum->assertDontSee('APAR-TINDAKAN-01');

        // Tab: Perlu Tindakan
        $resTindakan = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'perlu_tindakan']));
        $resTindakan->assertSee('APAR-TINDAKAN-01');
        $resTindakan->assertDontSee('APAR-BAIK-01');
        $resTindakan->assertDontSee('APAR-BELUM-01');
    }

    /**
     * 4. Pencarian dan filter lokasi tetap berfungsi bersama tab.
     */
    public function test_search_and_location_filter_work_together_with_tabs(): void
    {
        $lokasiA = Lokasi::create(['nama' => 'Lantai 1 Sayap Barat', 'status' => Lokasi::STATUS_AKTIF]);
        $lokasiB = Lokasi::create(['nama' => 'Lantai 2 Sayap Timur', 'status' => Lokasi::STATUS_AKTIF]);

        $apar1 = $this->createOperationalApar(['kode' => 'APAR-BARAT-01', 'lokasi_id' => $lokasiA->id]);
        $apar2 = $this->createOperationalApar(['kode' => 'APAR-TIMUR-01', 'lokasi_id' => $lokasiB->id]);

        Pemeriksaan::create([
            'apar_id' => $apar1->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-05 09:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas A',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Filter lokasi A + tab sudah
        $resLokasi = $this->get(route('pemeriksaan.index', [
            'bulan' => 10,
            'tahun' => 2026,
            'lokasi_id' => $lokasiA->id,
            'status_periksa' => 'sudah',
        ]));
        $resLokasi->assertSee('APAR-BARAT-01');
        $resLokasi->assertDontSee('APAR-TIMUR-01');

        // Filter search q + tab belum
        $resSearch = $this->get(route('pemeriksaan.index', [
            'bulan' => 10,
            'tahun' => 2026,
            'q' => 'TIMUR',
            'status_periksa' => 'belum',
        ]));
        $resSearch->assertSee('APAR-TIMUR-01');
        $resSearch->assertDontSee('APAR-BARAT-01');
    }

    /**
     * 5. Pergantian bulan/tahun menghitung ulang jumlah dan data dengan benar.
     */
    public function test_period_switch_recalculates_counts_and_data_correctly(): void
    {
        $apar = $this->createOperationalApar(['kode' => 'APAR-PERIODE-01']);

        // Diperiksa pada Oktober 2026
        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-05 09:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas A',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Oktober 2026: Sudah = 1, Belum = 0
        $resOkt = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $this->assertEquals(1, $resOkt->viewData('totalSudah'));
        $this->assertEquals(0, $resOkt->viewData('totalBelum'));

        // November 2026: Belum ada inspeksi -> Sudah = 0, Belum = 1
        $resNov = $this->get(route('pemeriksaan.index', ['bulan' => 11, 'tahun' => 2026]));
        $this->assertEquals(0, $resNov->viewData('totalSudah'));
        $this->assertEquals(1, $resNov->viewData('totalBelum'));
    }

    /**
     * 6. APAR stok dan tidak aktif dikecualikan dari target dan tab.
     */
    public function test_stock_and_inactive_apars_are_excluded(): void
    {
        $aparAktif = $this->createOperationalApar(['kode' => 'APAR-AKTIF-01']);

        $aparNonAktif = $this->createOperationalApar([
            'kode' => 'APAR-INAKTIF-01',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        $lokasiStok = Lokasi::create(['nama' => 'GUDANG K3RS', 'status' => Lokasi::STATUS_STOK]);
        $aparStok = $this->createOperationalApar([
            'kode' => 'APAR-STOK-01',
            'lokasi_id' => $lokasiStok->id,
        ]);

        $response = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $this->assertEquals(1, $response->viewData('totalWajib'));

        $response->assertSee('APAR-AKTIF-01');
        $response->assertDontSee('APAR-INAKTIF-01');
        $response->assertDontSee('APAR-STOK-01');
    }

    /**
     * 7. Tombol Scan APAR di header kanan tetap ada dan mempertahankan periode.
     */
    public function test_scan_apar_header_button_is_present_with_period_params(): void
    {
        $response = $this->get(route('pemeriksaan.index', ['bulan' => 8, 'tahun' => 2026]));
        $response->assertOk();

        $response->assertSee('Scan APAR');
        $response->assertSee(route('scan.index', ['bulan' => 8, 'tahun' => 2026]));
    }

    /**
     * 8. Keadaan kosong ditangani dengan jelas ketika pencarian tidak ada hasil.
     */
    public function test_empty_state_displayed_on_zero_results(): void
    {
        $this->createOperationalApar(['kode' => 'APAR-ADA-01']);

        $response = $this->get(route('pemeriksaan.index', [
            'bulan' => 10,
            'tahun' => 2026,
            'q' => 'TIDAK_DITEMUKAN_SAMA_SEKALI',
        ]));
        $response->assertOk();

        $response->assertSee('Tidak ada data unit APAR');
    }

    /**
     * 9. Breadcrumb redundant tidak lagi ditampilkan di atas judul halaman index Hasil Inspeksi.
     */
    public function test_breadcrumb_is_not_displayed_on_index(): void
    {
        $this->createOperationalApar(['kode' => 'APAR-BC-01']);

        $response = $this->get(route('pemeriksaan.index'));
        $response->assertOk();

        $response->assertDontSee('aria-label="Breadcrumb"', false);
    }
}
