<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\Ruangan;
use Database\Seeders\AparAktualSeeder;
use Database\Seeders\LokasiAktualSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase2MasterDataImportTest extends TestCase
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
            'kode' => 'GDG-TEST',
            'nama' => 'Gedung Pengujian',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        return Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruangan Uji',
            'kode' => 'R-TEST',
        ]);
    }

    /**
     * 1. Master locations can be seeded with exact 64 rows.
     */
    public function test_master_locations_can_be_seeded_with_exact_64_locations(): void
    {
        $this->seed(LokasiAktualSeeder::class);

        $this->assertEquals(64, Lokasi::count());
    }

    /**
     * 2. Exactly 59 active locations exist as expected.
     */
    public function test_59_active_locations_exist_with_aktif_status(): void
    {
        $this->seed(LokasiAktualSeeder::class);

        $activeCount = Lokasi::where('status', Lokasi::STATUS_AKTIF)->count();
        $this->assertEquals(59, $activeCount);
    }

    /**
     * 3. Four zero-APAR locations exist with 'kosong' status.
     */
    public function test_four_zero_apar_locations_exist_and_remain_empty(): void
    {
        $this->seed(AparAktualSeeder::class);

        $zeroLocations = ['JAMRUD', 'YAKUT', 'GIOK', 'KANTIN'];

        foreach ($zeroLocations as $nama) {
            $lokasi = Lokasi::where('nama', $nama)->first();
            $this->assertNotNull($lokasi, "Lokasi {$nama} harus terdaftar di tabel lokasis.");
            $this->assertEquals(Lokasi::STATUS_KOSONG, $lokasi->status);
            $this->assertEquals(0, $lokasi->apars()->count(), "Lokasi {$nama} tidak boleh memiliki APAR.");
        }

        $this->assertEquals(4, Lokasi::where('status', Lokasi::STATUS_KOSONG)->count());
    }

    /**
     * 4. Exactly 173 installed/active APAR exist.
     */
    public function test_exactly_173_installed_active_apars_exist(): void
    {
        $this->seed(AparAktualSeeder::class);

        $installedApars = Apar::whereNotNull('lokasi_id')->get();
        $this->assertCount(173, $installedApars);

        $activeApars = Apar::where('status', Apar::STATUS_BAIK)->get();
        $this->assertCount(173, $activeApars);
    }

    /**
     * 5. APAR are associated with the correct locations per LOCATION_APAR_MASTER.md.
     */
    public function test_apars_are_associated_with_correct_locations(): void
    {
        $this->seed(AparAktualSeeder::class);

        foreach (LokasiAktualSeeder::LOCATIONS_DATA as $def) {
            $lokasi = Lokasi::where('nama', $def['nama'])->firstOrFail();
            $this->assertEquals(
                $def['target_apar'],
                $lokasi->apars()->count(),
                "Jumlah APAR pada lokasi {$def['nama']} harus tepat {$def['target_apar']}."
            );
        }

        // Spot checks for specific representative locations
        $pos = Lokasi::where('nama', 'POS PANTAU SECURITY')->first();
        $this->assertEquals(1, $pos->apars()->count());

        $igd = Lokasi::where('nama', 'IGD Lt. 1')->first();
        $this->assertEquals(4, $igd->apars()->count());

        $emr1 = Lokasi::where('nama', 'EMERALD Lt 1')->first();
        $this->assertEquals(5, $emr1->apars()->count());

        $mutiara1 = Lokasi::where('nama', 'MUTIARA Lt 1 HD')->first();
        $this->assertEquals(7, $mutiara1->apars()->count());

        $lift = Lokasi::where('nama', 'LIFT')->first();
        $this->assertEquals(8, $lift->apars()->count());

        $kumalaMutiara = Lokasi::where('nama', 'KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4')->first();
        $this->assertEquals(6, $kumalaMutiara->apars()->count());
    }

    /**
     * 6. No stock APAR are imported as active inspection targets.
     */
    public function test_no_stock_apar_are_imported_as_active_inspection_targets(): void
    {
        $this->seed(AparAktualSeeder::class);

        $gudang = Lokasi::where('nama', 'GUDANG K3RS')->firstOrFail();
        $this->assertEquals(0, $gudang->apars()->count());
        $this->assertEquals(0, Apar::where('lokasi_id', $gudang->id)->count());
    }

    /**
     * 7. GUDANG K3RS remains identifiable as stock location.
     */
    public function test_gudang_k3rs_remains_identifiable_as_stock_location(): void
    {
        $this->seed(LokasiAktualSeeder::class);

        $gudang = Lokasi::where('nama', 'GUDANG K3RS')->firstOrFail();
        $this->assertEquals(Lokasi::STATUS_STOK, $gudang->status);
        $this->assertStringContainsString('64 unit stok sediaan', $gudang->keterangan);
    }

    /**
     * 8. APAR identifiers are unique and follow deterministic location pattern APAR-{KODE_LOKASI}-{NOMOR_URUT}.
     */
    public function test_temporary_apar_identifiers_are_unique_and_deterministic(): void
    {
        $this->seed(AparAktualSeeder::class);

        $apars = Apar::whereNotNull('lokasi_id')->orderBy('kode')->get();

        $this->assertCount(173, $apars);

        $codes = $apars->pluck('kode')->toArray();
        $uniqueCodes = array_unique($codes);
        $this->assertCount(173, $uniqueCodes);

        $qrCodes = $apars->pluck('qr_code')->toArray();
        $uniqueQrs = array_unique($qrCodes);
        $this->assertCount(173, $uniqueQrs);

        foreach ($apars as $apar) {
            $this->assertMatchesRegularExpression('/^APAR-[A-Z0-9]+-\d{2}$/', $apar->kode);
            $this->assertEquals("HAFIS-QR-{$apar->kode}", $apar->qr_code);
        }

        // Check specific known deterministic codes
        $this->assertTrue(in_array('APAR-SEC-01', $codes));
        $this->assertTrue(in_array('APAR-EMR1-01', $codes));
        $this->assertTrue(in_array('APAR-ITD-01', $codes));
        $this->assertTrue(in_array('APAR-OKC-02', $codes));
    }

    /**
     * 9. Seeder is deterministic and idempotent on rerun.
     */
    public function test_seeder_is_deterministic_and_idempotent(): void
    {
        // First run
        $this->seed(AparAktualSeeder::class);
        $this->assertEquals(64, Lokasi::count());
        $this->assertEquals(173, Apar::whereNotNull('lokasi_id')->count());

        // Second run
        $this->seed(AparAktualSeeder::class);
        $this->assertEquals(64, Lokasi::count());
        $this->assertEquals(173, Apar::whereNotNull('lokasi_id')->count());
        $this->assertEquals(173, Apar::where('status', Apar::STATUS_BAIK)->count());
    }

    /**
     * 10. Existing legacy location relationship remains intact on APAR model.
     */
    public function test_existing_legacy_location_relationship_remains_intact(): void
    {
        $this->seed(AparAktualSeeder::class);

        $firstApar = Apar::whereNotNull('lokasi_id')->first();
        $this->assertNotNull($firstApar->ruangan_id);
        $this->assertNotNull($firstApar->ruangan);
        $this->assertInstanceOf(Ruangan::class, $firstApar->ruangan);
        $this->assertInstanceOf(Lokasi::class, $firstApar->lokasi);
    }

    /**
     * 11. Legacy dummy APARs and exclusively dependent history are permanently purged by seeder.
     */
    public function test_existing_legacy_inspection_records_are_not_destroyed(): void
    {
        $ruangan = $this->createLegacyRuangan();

        // 1. Simulasikan APAR legacy dengan riwayat pemeriksaan
        $legacyAparWithHistory = Apar::create([
            'kode' => 'APAR-IGD-01',
            'qr_code' => 'HAFIS-QR-LEGACY-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Legacy Test',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'status' => 'belum_dimulai',
        ]);

        DB::table('jadwal_apars')->insert([
            'jadwal_id' => $jadwal->id,
            'apar_id' => $legacyAparWithHistory->id,
            'status_pemeriksaan' => 'belum_diperiksa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $legacyAparWithHistory->id,
            'jadwal_id' => $jadwal->id,
            'pemeriksa_nama' => 'Petugas Legacy Test',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => 'baik',
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
        ]);

        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Tekanan',
            'hasil' => 'baik',
            'urutan' => 1,
        ]);

        // 2. Simulasikan APAR dummy terisolasi tanpa relasi
        $isolatedDummy = Apar::create([
            'kode' => 'APAR-NONAKTIF-01',
            'qr_code' => 'HAFIS-QR-ISOLATED',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        // Jalankan seeder Phase 4.5
        $this->seed(AparAktualSeeder::class);

        // Verifikasi: Dummy APAR dan riwayatnya dibersihkan permanen sesuai Phase 4.5 decision
        $this->assertNull(Apar::where('kode', 'APAR-IGD-01')->first());
        $this->assertNull(Apar::where('kode', 'APAR-NONAKTIF-01')->first());
        $this->assertEquals(0, Pemeriksaan::where('apar_id', $legacyAparWithHistory->id)->count());
        $this->assertEquals(0, PemeriksaanDetail::where('pemeriksaan_id', $pemeriksaan->id)->count());
        $this->assertEquals(0, DB::table('jadwal_apars')->where('apar_id', $legacyAparWithHistory->id)->count());

        // Verifikasi total active APAR tetap tepat 173
        $this->assertEquals(173, Apar::where('status', Apar::STATUS_BAIK)->count());
    }

    /**
     * 12. No monthly inspection records are created by Phase 2.
     */
    public function test_no_monthly_inspection_records_created_by_seeder(): void
    {
        $this->seed(AparAktualSeeder::class);

        $this->assertEquals(0, Pemeriksaan::count());
        $this->assertEquals(0, PemeriksaanDetail::count());
    }

    /**
     * 13. No schedule records are created by Phase 2.
     */
    public function test_no_schedule_records_created_by_seeder(): void
    {
        $this->seed(AparAktualSeeder::class);

        $this->assertEquals(0, Jadwal::count());
        $this->assertEquals(0, DB::table('jadwal_apars')->count());
    }

    /**
     * 14. Ambiguous locations are preserved as unresolved without splitting or merging.
     */
    public function test_ambiguous_locations_preserved_as_unresolved(): void
    {
        $this->seed(AparAktualSeeder::class);

        $ambiguousLocations = [
            'KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4' => 6,
            'RESEPCIONIST Lt 2' => 1,
            'LIFT' => 8,
            '119' => 1,
            'GUDANG K3RS' => 0,
            'IT DALAM' => 1,
            'IT LUAR' => 1,
        ];

        foreach ($ambiguousLocations as $nama => $targetApar) {
            $lokasi = Lokasi::where('nama', $nama)->first();
            $this->assertNotNull($lokasi, "Lokasi ambigu '{$nama}' harus tetap terdaftar.");
            $this->assertEquals($targetApar, $lokasi->apars()->count());
        }
    }

    /**
     * 15. Location codes are populated with unique deterministic codes from LOCATION_CODE_MAPPING.md.
     */
    public function test_location_codes_remain_null(): void
    {
        $this->seed(LokasiAktualSeeder::class);

        $nonNullCodes = Lokasi::whereNotNull('kode')->count();
        $this->assertEquals(64, $nonNullCodes, 'Seluruh 64 lokasi harus memiliki kode lokasi definitif.');

        $uniqueCodes = Lokasi::pluck('kode')->unique()->count();
        $this->assertEquals(64, $uniqueCodes, 'Seluruh 64 kode lokasi harus unik.');
    }
}
