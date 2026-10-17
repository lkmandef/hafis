<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Database\Seeders\AparAktualSeeder;
use Database\Seeders\AparAtributKoreksiSeeder;
use Database\Seeders\LokasiAktualSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase47AparAttributesCorrectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    private function createLegacyRuangan(): Ruangan
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-LEGACY-TEST',
            'nama' => 'Gedung Legacy Test',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        return Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruang IGD',
            'kode' => 'R-IGD',
        ]);
    }

    /**
     * 1. Total installed APAR count is exactly 173 across 59 active locations.
     */
    public function test_total_installed_apar_remains_exact_173_units(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $this->assertEquals(173, Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)->count());
        $this->assertEquals(173, Apar::count());
    }

    /**
     * 2. Counts per location match Table 12 in the reference document.
     */
    public function test_counts_per_location_match_source_document(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        foreach (LokasiAktualSeeder::LOCATIONS_DATA as $loc) {
            $lokasi = Lokasi::where('nama', $loc['nama'])->firstOrFail();
            $expected = $loc['target_apar'];
            $actual = Apar::where('lokasi_id', $lokasi->id)->count();

            $this->assertEquals(
                $expected,
                $actual,
                "Mismatch in count for location {$loc['nama']}"
            );
        }
    }

    /**
     * 3. Total media categories: exactly 115 Powder and 58 CO2.
     */
    public function test_media_distribution_matches_table_12_exact_115_powder_and_58_co2(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $powderCount = Apar::where('jenis_media', 'Dry Chemical Powder')->count();
        $co2Count = Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->count();

        $this->assertEquals(115, $powderCount, 'Powder units must be exactly 115');
        $this->assertEquals(58, $co2Count, 'CO2 units must be exactly 58');
        $this->assertEquals(173, $powderCount + $co2Count);
    }

    /**
     * 4. Total capacity categories: 3x 25kg, 108x 6kg, 4x 3kg (Powder), 17x 7kg, 7x 5kg, 34x 3kg (CO2).
     * Total capacities overall: 3x 25kg, 108x 6kg, 38x 3kg, 17x 7kg, 7x 5kg.
     */
    public function test_capacity_distribution_matches_table_12_exact_breakdown(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $this->assertEquals(3, Apar::where('kapasitas', '25 kg')->count());
        $this->assertEquals(108, Apar::where('kapasitas', '6 kg')->count());
        $this->assertEquals(38, Apar::where('kapasitas', '3 kg')->count());
        $this->assertEquals(17, Apar::where('kapasitas', '7 kg')->count());
        $this->assertEquals(7, Apar::where('kapasitas', '5 kg')->count());

        // Breakdown per media & capacity
        $this->assertEquals(3, Apar::where('jenis_media', 'Dry Chemical Powder')->where('kapasitas', '25 kg')->count());
        $this->assertEquals(108, Apar::where('jenis_media', 'Dry Chemical Powder')->where('kapasitas', '6 kg')->count());
        $this->assertEquals(4, Apar::where('jenis_media', 'Dry Chemical Powder')->where('kapasitas', '3 kg')->count());

        $this->assertEquals(17, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->where('kapasitas', '7 kg')->count());
        $this->assertEquals(7, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->where('kapasitas', '5 kg')->count());
        $this->assertEquals(34, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->where('kapasitas', '3 kg')->count());
    }

    /**
     * 5. Brand is not assumed as Servvo; unrecorded brands are stored as NULL and rendered as 'Belum terdata'.
     */
    public function test_brand_is_null_and_displays_as_belum_terdata(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        // In database, brand is NULL (no Servvo assumption)
        $this->assertEquals(0, Apar::whereNotNull('merek')->count());
        $this->assertEquals(173, Apar::whereNull('merek')->count());

        // In model accessor / label
        $sampleApar = Apar::first();
        $this->assertEquals('Belum terdata', $sampleApar->merek_label);
    }

    /**
     * 6. Codes and QR codes remain unique and properly formatted.
     */
    public function test_codes_and_qr_codes_are_unique_and_formatted(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $this->assertEquals(173, Apar::distinct('kode')->count('kode'));
        $this->assertEquals(173, Apar::distinct('qr_code')->count('qr_code'));

        $invalidCodes = Apar::where('kode', 'not like', 'APAR-%')->count();
        $this->assertEquals(0, $invalidCodes);
    }

    /**
     * 7. Location displays show actual master location name rather than fallback Ruang IGD.
     */
    public function test_location_displays_use_actual_master_lokasi(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $sampleApar = Apar::whereHas('lokasi', fn ($q) => $q->where('nama', 'LAUNDRY LT 1'))->firstOrFail();

        $this->assertEquals('LAUNDRY LT 1', $sampleApar->lokasi_nama);
        $this->assertEquals('LAUNDRY LT 1', $sampleApar->lokasi_lengkap);

        // UI index page displays location name correctly on page 1
        $response = $this->get(route('apar.index'));
        $response->assertOk();
        $firstItem = $response->viewData('apars')->first();
        $this->assertNotNull($firstItem->lokasi);
        $response->assertSee(e($firstItem->lokasi->nama));
        $response->assertSee('Belum terdata');

        // UI index search by location finds the location
        $searchResponse = $this->get(route('apar.index', ['q' => 'LAUNDRY']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('LAUNDRY LT 1');

        // UI show page displays location name correctly
        $showResponse = $this->get(route('apar.show', $sampleApar->id));
        $showResponse->assertOk();
        $showResponse->assertSee('LAUNDRY LT 1');
    }

    /**
     * 8. Gudang K3RS 64 units remains stock metadata, not installed APARs.
     */
    public function test_gudang_k3rs_remains_stock_metadata_only(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $gudang = Lokasi::where('nama', 'GUDANG K3RS')->firstOrFail();
        $this->assertEquals(Lokasi::STATUS_STOK, $gudang->status);
        $this->assertStringContainsString('64 unit stok sediaan', $gudang->keterangan);
        $this->assertEquals(0, Apar::where('lokasi_id', $gudang->id)->count());
    }

    /**
     * 9. No dummy legacy APARs are resurrected upon re-seeding.
     */
    public function test_no_dummy_apars_are_created_or_resurrected(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $dummyCodes = [
            'APAR-TEST-E',
            'APAR-NONAKTIF-01',
            'APAR-IRJ-001',
            'Emerald-001',
        ];

        $this->assertEquals(0, Apar::whereIn('kode', $dummyCodes)->count());
    }

    /**
     * 10. Operational data (inspection dates, coordinates, statuses) are preserved when re-seeding.
     */
    public function test_operational_data_is_preserved_on_re_seeding(): void
    {
        $this->createLegacyRuangan();
        $this->seed(AparAktualSeeder::class);

        $target = Apar::first();
        $target->update([
            'status' => Apar::STATUS_PERHATIAN,
            'koordinat_x' => 45.5,
            'koordinat_y' => 60.2,
            'posisi_detail' => 'Dinding pilar utara samping hydrant',
            'tanggal_pemeriksaan_terakhir' => '2026-10-12',
        ]);

        // Re-run seeder
        $this->seed(AparAktualSeeder::class);

        $reloaded = $target->fresh();
        $this->assertEquals(Apar::STATUS_PERHATIAN, $reloaded->status);
        $this->assertEquals(45.5, (float) $reloaded->koordinat_x);
        $this->assertEquals(60.2, (float) $reloaded->koordinat_y);
        $this->assertEquals('Dinding pilar utara samping hydrant', $reloaded->posisi_detail);
        $this->assertEquals('2026-10-12', $reloaded->tanggal_pemeriksaan_terakhir?->toDateString());
    }

    /**
     * 11. Standalone AparAtributKoreksiSeeder works idempotently on existing database records.
     */
    public function test_standalone_koreksi_seeder_updates_existing_records_idempotently(): void
    {
        $ruangan = $this->createLegacyRuangan();
        $this->seed(LokasiAktualSeeder::class);

        // Simulasikan database dalam kondisi sebelum Phase 4.7 (semua Powder 6kg Servvo)
        $lokasiList = Lokasi::where('status', Lokasi::STATUS_AKTIF)->get();
        foreach ($lokasiList as $loc) {
            $locDef = collect(LokasiAktualSeeder::LOCATIONS_DATA)->firstWhere('nama', $loc->nama);
            for ($i = 1; $i <= $locDef['target_apar']; $i++) {
                Apar::create([
                    'kode' => sprintf('APAR-%s-%02d', $loc->kode, $i),
                    'qr_code' => sprintf('HAFIS-QR-APAR-%s-%02d', $loc->kode, $i),
                    'lokasi_id' => $loc->id,
                    'ruangan_id' => $ruangan->id,
                    'jenis_media' => 'Dry Chemical Powder',
                    'kapasitas' => '6 kg',
                    'merek' => 'Servvo',
                    'status' => Apar::STATUS_BAIK,
                ]);
            }
        }

        $this->assertEquals(173, Apar::where('merek', 'Servvo')->count());

        // Jalankan koreksi seeder
        $this->seed(AparAtributKoreksiSeeder::class);

        // Verifikasi hasil koreksi
        $this->assertEquals(115, Apar::where('jenis_media', 'Dry Chemical Powder')->count());
        $this->assertEquals(58, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->count());
        $this->assertEquals(0, Apar::whereNotNull('merek')->count());

        // Jalankan lagi (idempotency check)
        $this->seed(AparAtributKoreksiSeeder::class);
        $this->assertEquals(115, Apar::where('jenis_media', 'Dry Chemical Powder')->count());
        $this->assertEquals(58, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->count());
    }
}
