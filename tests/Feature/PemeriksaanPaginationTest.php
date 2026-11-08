<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PemeriksaanPaginationTest extends TestCase
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
            'nama' => 'Lokasi '.uniqid(),
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $gedung = Gedung::create(['kode' => 'GDG-'.uniqid(), 'nama' => 'Gedung '.uniqid(), 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang '.uniqid(), 'kode' => 'R-'.uniqid()]);

        return Apar::create(array_merge([
            'kode' => 'APAR-'.uniqid(),
            'qr_code' => 'HAFIS-QR-'.uniqid(),
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ], $attributes));
    }

    /**
     * Test server-side pagination delivers 15 items per page and contains LengthAwarePaginator.
     */
    public function test_pemeriksaan_index_paginates_15_items_per_page(): void
    {
        // Buat 25 unit APAR
        for ($i = 1; $i <= 25; $i++) {
            $this->createOperationalApar([
                'kode' => sprintf('APAR-PAGE-%02d', $i),
            ]);
        }

        $response = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $response->assertOk();

        $apars = $response->viewData('apars');
        $this->assertInstanceOf(LengthAwarePaginator::class, $apars);
        $this->assertEquals(25, $apars->total());
        $this->assertEquals(15, $apars->perPage());
        $this->assertEquals(1, $apars->currentPage());
        $this->assertTrue($apars->hasPages());

        // Halaman 1 memuat APAR-PAGE-01 sampai 15, tetapi tidak memuat APAR-PAGE-16
        $response->assertSee('APAR-PAGE-01');
        $response->assertSee('APAR-PAGE-15');
        $response->assertDontSee('APAR-PAGE-16');
    }

    /**
     * Test navigasi pagination ke halaman kedua.
     */
    public function test_pemeriksaan_index_navigates_to_page_two(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            $this->createOperationalApar([
                'kode' => sprintf('APAR-NAV-%02d', $i),
            ]);
        }

        $response = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'page' => 2]));
        $response->assertOk();

        $apars = $response->viewData('apars');
        $this->assertEquals(2, $apars->currentPage());

        // Halaman 2 memuat APAR-NAV-16 sampai 25, dan tidak memuat APAR-NAV-01
        $response->assertSee('APAR-NAV-16');
        $response->assertSee('APAR-NAV-25');
        $response->assertDontSee('APAR-NAV-01');
    }

    /**
     * Test pagination mempertahankan query string filter (withQueryString).
     */
    public function test_pemeriksaan_index_pagination_preserves_query_string(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->createOperationalApar([
                'kode' => sprintf('APAR-QUERY-%02d', $i),
            ]);
        }

        $response = $this->get(route('pemeriksaan.index', [
            'bulan' => 8,
            'tahun' => 2025,
            'status_periksa' => 'belum',
            'page' => 1,
        ]));
        $response->assertOk();

        // Tautan pagination harus menyertakan parameter query filter
        $response->assertSee('bulan=8');
        $response->assertSee('tahun=2025');
        $response->assertSee('status_periksa=belum');
    }

    /**
     * Test filter status pemeriksaan (sudah, belum, perlu_tindakan, baik) terintegrasi dengan pagination.
     */
    public function test_pemeriksaan_index_status_filter_works_with_pagination(): void
    {
        $aparSudahBaik = $this->createOperationalApar(['kode' => 'APAR-STAT-BAIK']);
        $aparSudahTindakan = $this->createOperationalApar(['kode' => 'APAR-STAT-TINDAKAN']);
        $aparBelum = $this->createOperationalApar(['kode' => 'APAR-STAT-BELUM']);

        Pemeriksaan::create([
            'apar_id' => $aparSudahBaik->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-02 08:00:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas K3RS',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        Pemeriksaan::create([
            'apar_id' => $aparSudahTindakan->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => '2026-10-02 08:30:00',
            'petugas_id' => 1,
            'pemeriksa_nama' => 'Petugas K3RS',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        // 1. Filter status_periksa = 'sudah'
        $resSudah = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'sudah']));
        $resSudah->assertOk();
        $this->assertEquals(2, $resSudah->viewData('apars')->total());
        $resSudah->assertSee('APAR-STAT-BAIK');
        $resSudah->assertSee('APAR-STAT-TINDAKAN');
        $resSudah->assertDontSee('APAR-STAT-BELUM');

        // 2. Filter status_periksa = 'belum'
        $resBelum = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'belum']));
        $resBelum->assertOk();
        $this->assertEquals(1, $resBelum->viewData('apars')->total());
        $resBelum->assertSee('APAR-STAT-BELUM');
        $resBelum->assertDontSee('APAR-STAT-BAIK');

        // 3. Filter status_periksa = 'perlu_tindakan'
        $resTindakan = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'perlu_tindakan']));
        $resTindakan->assertOk();
        $this->assertEquals(1, $resTindakan->viewData('apars')->total());
        $resTindakan->assertSee('APAR-STAT-TINDAKAN');
        $resTindakan->assertDontSee('APAR-STAT-BAIK');

        // 4. Filter status_periksa = 'baik'
        $resBaik = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026, 'status_periksa' => 'baik']));
        $resBaik->assertOk();
        $this->assertEquals(1, $resBaik->viewData('apars')->total());
        $resBaik->assertSee('APAR-STAT-BAIK');
        $resBaik->assertDontSee('APAR-STAT-TINDAKAN');
    }

    /**
     * Test pencarian dan filter lokasi bekerja bersama pagination.
     */
    public function test_pemeriksaan_index_search_and_location_filter_with_pagination(): void
    {
        $lokasiA = Lokasi::create(['nama' => 'Zona Barat K3RS', 'status' => Lokasi::STATUS_AKTIF]);
        $lokasiB = Lokasi::create(['nama' => 'Zona Timur K3RS', 'status' => Lokasi::STATUS_AKTIF]);

        $this->createOperationalApar(['kode' => 'APAR-ZONABARAT-01', 'lokasi_id' => $lokasiA->id]);
        $this->createOperationalApar(['kode' => 'APAR-ZONATIMUR-01', 'lokasi_id' => $lokasiB->id]);

        // Filter lokasi_id
        $resLokasi = $this->get(route('pemeriksaan.index', [
            'bulan' => 10,
            'tahun' => 2026,
            'lokasi_id' => $lokasiA->id,
        ]));
        $resLokasi->assertOk();
        $this->assertEquals(1, $resLokasi->viewData('apars')->total());
        $resLokasi->assertSee('APAR-ZONABARAT-01');
        $resLokasi->assertDontSee('APAR-ZONATIMUR-01');

        // Filter pencarian teks
        $resSearch = $this->get(route('pemeriksaan.index', [
            'bulan' => 10,
            'tahun' => 2026,
            'q' => 'ZONATIMUR',
        ]));
        $resSearch->assertOk();
        $this->assertEquals(1, $resSearch->viewData('apars')->total());
        $resSearch->assertSee('APAR-ZONATIMUR-01');
        $resSearch->assertDontSee('APAR-ZONABARAT-01');
    }
}
