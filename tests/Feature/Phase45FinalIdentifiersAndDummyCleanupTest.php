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

class Phase45FinalIdentifiersAndDummyCleanupTest extends TestCase
{
    use RefreshDatabase;

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
     * 1 & 2. The five expected dummy APARs and exclusively dependent history are deleted; unrelated records remain.
     */
    public function test_five_legacy_dummy_apars_and_exclusive_history_are_deleted_while_unrelated_remain(): void
    {
        $ruangan = $this->createLegacyRuangan();

        // Buat 5 dummy APARs dengan riwayat
        $dummy1 = Apar::create([
            'kode' => 'APAR-IGD-01',
            'qr_code' => 'HAFIS-QR-APARIGD01-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);
        $dummy2 = Apar::create([
            'kode' => 'APAR-IGD-02',
            'qr_code' => 'HAFIS-QR-APARIGD02-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);
        $dummy3 = Apar::create([
            'kode' => 'APAR-TEST-E',
            'qr_code' => 'HAFIS-TEST-E-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);
        $dummy4 = Apar::create([
            'kode' => 'APAR-IRJ-001',
            'qr_code' => 'HAFIS-QR-APARIRJ001-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);
        $dummy5 = Apar::create([
            'kode' => 'Emerald-001',
            'qr_code' => 'HAFIS-QR-EMERALD001-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Dummy',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'status' => 'selesai',
        ]);

        foreach ([$dummy1, $dummy2, $dummy3, $dummy4, $dummy5] as $dummy) {
            DB::table('jadwal_apars')->insert([
                'jadwal_id' => $jadwal->id,
                'apar_id' => $dummy->id,
                'status_pemeriksaan' => 'sudah_diperiksa',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $pId = DB::table('pemeriksaans')->insertGetId([
                'apar_id' => $dummy->id,
                'jadwal_id' => $jadwal->id,
                'pemeriksa_nama' => 'Tester',
                'tanggal_pemeriksaan' => now(),
                'hasil_keseluruhan' => 'baik',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('pemeriksaan_details')->insert([
                'pemeriksaan_id' => $pId,
                'item_nama' => 'Tekanan',
                'hasil' => 'baik',
                'urutan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Jalankan seeder Phase 4.5
        $this->seed(AparAktualSeeder::class);

        // Verifikasi kelima dummy telah terhapus permanen
        $this->assertNull(Apar::where('kode', 'APAR-IGD-01')->first());
        $this->assertNull(Apar::where('kode', 'APAR-IGD-02')->first());
        $this->assertNull(Apar::where('kode', 'APAR-TEST-E')->first());
        $this->assertNull(Apar::where('kode', 'APAR-IRJ-001')->first());
        $this->assertNull(Apar::where('kode', 'Emerald-001')->first());

        // Riwayat dependen dummy terhapus
        $this->assertEquals(0, Pemeriksaan::whereIn('apar_id', [$dummy1->id, $dummy2->id, $dummy3->id, $dummy4->id, $dummy5->id])->count());
        $this->assertEquals(0, DB::table('jadwal_apars')->whereIn('apar_id', [$dummy1->id, $dummy2->id, $dummy3->id, $dummy4->id, $dummy5->id])->count());

        // Jadwal utama yang tidak eksklusif tetap ada
        $this->assertNotNull(Jadwal::find($jadwal->id));

        // Tepat 173 APAR aktual aktif terpasang
        $this->assertEquals(173, Apar::where('status', Apar::STATUS_BAIK)->count());
    }

    /**
     * 3, 4, 5, 6. Total APAR is exactly 173, counts match per-location master, codes are unique and follow pattern.
     */
    public function test_actual_apars_match_counts_and_follow_unique_location_code_pattern(): void
    {
        $this->seed(AparAktualSeeder::class);

        $apars = Apar::whereNotNull('lokasi_id')->get();
        $this->assertCount(173, $apars);

        // Unique codes
        $codes = $apars->pluck('kode')->toArray();
        $this->assertCount(173, array_unique($codes));

        // Each code follows APAR-{KODE_LOKASI}-{NOMOR_URUT}
        foreach ($apars as $apar) {
            $lokasi = $apar->lokasi;
            $this->assertNotNull($lokasi);
            $this->assertNotEmpty($lokasi->kode);
            $this->assertMatchesRegularExpression('/^APAR-'.preg_quote($lokasi->kode, '/').'-\d{2}$/', $apar->kode);
        }

        // Verify per-location distribution matches LokasiAktualSeeder::LOCATIONS_DATA
        foreach (LokasiAktualSeeder::LOCATIONS_DATA as $def) {
            $expected = $def['target_apar'];
            $actual = Apar::whereHas('lokasi', fn ($q) => $q->where('nama', $def['nama']))->count();
            $this->assertEquals($expected, $actual, "Count mismatch for location: {$def['nama']}");
        }
    }

    /**
     * 7. QR identifiers are unique and resolve to the correct APAR via scanner.
     */
    public function test_qr_identifiers_are_unique_and_resolve_via_scanner(): void
    {
        $this->seed(AparAktualSeeder::class);

        $apars = Apar::whereNotNull('lokasi_id')->get();
        $qrs = $apars->pluck('qr_code')->toArray();
        $this->assertCount(173, array_unique($qrs));

        $sample = $apars->first();
        $this->assertEquals("HAFIS-QR-{$sample->kode}", $sample->qr_code);

        // Scan by exact QR payload
        $response = $this->get(route('scan.cari', ['kode' => $sample->qr_code]));
        $response->assertRedirect(route('apar.show', $sample->id));

        // Scan by human-readable kode
        $responseKode = $this->get(route('scan.cari', ['kode' => $sample->kode]));
        $responseKode->assertRedirect(route('apar.show', $sample->id));
    }

    /**
     * 8, 9, 10. Generated location codes are unique, zero-APAR locations and stock location remain in master data.
     */
    public function test_location_codes_unique_and_special_locations_preserved(): void
    {
        $this->seed(AparAktualSeeder::class);

        $this->assertEquals(64, Lokasi::count());

        $locationCodes = Lokasi::whereNotNull('kode')->pluck('kode')->toArray();
        $this->assertCount(64, $locationCodes);
        $this->assertCount(64, array_unique($locationCodes));

        // Zero-APAR locations
        foreach (['JAMRUD', 'YAKUT', 'GIOK', 'KANTIN'] as $emptyLoc) {
            $loc = Lokasi::where('nama', $emptyLoc)->firstOrFail();
            $this->assertEquals(Lokasi::STATUS_KOSONG, $loc->status);
            $this->assertEquals(0, Apar::where('lokasi_id', $loc->id)->count());
        }

        // Stock location
        $stockLoc = Lokasi::where('nama', 'GUDANG K3RS')->firstOrFail();
        $this->assertEquals(Lokasi::STATUS_STOK, $stockLoc->status);
        $this->assertEquals('GK3', $stockLoc->kode);
        $this->assertEquals(0, Apar::where('lokasi_id', $stockLoc->id)->count());
    }

    /**
     * 11 & 12. Monthly inspection, scanner, and existing inspection relationships remain functional.
     */
    public function test_monthly_inspection_and_scanner_remain_functional_with_new_codes(): void
    {
        $this->seed(AparAktualSeeder::class);

        $apar = Apar::where('kode', 'APAR-EMR1-01')->firstOrFail();

        // 1. Scanner with period redirects to create form for uninspected unit
        $scanResponse = $this->get(route('scan.cari', [
            'kode' => $apar->qr_code,
            'bulan' => 10,
            'tahun' => 2026,
        ]));
        $scanResponse->assertRedirect(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => 10,
            'periode_tahun' => 2026,
        ]));

        // 2. Perform monthly inspection
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Petugas K3RS',
            'tanggal_pemeriksaan' => '2026-10-12',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
        ]);

        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Tekanan',
            'hasil' => 'baik',
            'urutan' => 1,
        ]);

        // 3. Scanner on already-inspected unit redirects to show result
        $scanAlready = $this->get(route('scan.cari', [
            'kode' => $apar->kode,
            'bulan' => 10,
            'tahun' => 2026,
        ]));
        $scanAlready->assertRedirect(route('pemeriksaan.show', $pemeriksaan->id));

        // 4. Monthly overview page displays the new APAR code
        $overview = $this->get(route('pemeriksaan.index', ['bulan' => 10, 'tahun' => 2026]));
        $overview->assertOk();
        $overview->assertSee('APAR-EMR1-01');
    }

    /**
     * 13. Running the seeder twice is idempotent and does not overwrite operational fields.
     */
    public function test_seeder_rerun_is_safe_and_does_not_reset_operational_fields(): void
    {
        $this->seed(AparAktualSeeder::class);

        $apar = Apar::where('kode', 'APAR-IGD1-01')->firstOrFail();
        $apar->update([
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_pemeriksaan_terakhir' => '2026-10-12',
        ]);

        // Rerun seeder
        $this->seed(AparAktualSeeder::class);

        $reloaded = Apar::where('kode', 'APAR-IGD1-01')->firstOrFail();
        $this->assertEquals(Apar::STATUS_PERHATIAN, $reloaded->status, 'Operational status must NOT be reset by seeder');
        $this->assertEquals('2026-10-12', $reloaded->tanggal_pemeriksaan_terakhir?->toDateString(), 'Inspection date must NOT be reset by seeder');
        $this->assertEquals(173, Apar::count(), 'Total count must remain exactly 173');
    }

    /**
     * 14. No orphaned foreign keys remain in the database.
     */
    public function test_no_orphaned_foreign_keys_remain(): void
    {
        $this->seed(AparAktualSeeder::class);

        $orphanedApars = Apar::whereNotNull('lokasi_id')->whereDoesntHave('lokasi')->count();
        $this->assertEquals(0, $orphanedApars);

        $orphanedDetails = PemeriksaanDetail::whereDoesntHave('pemeriksaan')->count();
        $this->assertEquals(0, $orphanedDetails);

        $orphanedPemeriksaans = Pemeriksaan::whereDoesntHave('apar')->count();
        $this->assertEquals(0, $orphanedPemeriksaans);

        $orphanedJadwalApars = DB::table('jadwal_apars')
            ->whereNotIn('apar_id', Apar::pluck('id')->toArray())
            ->count();
        $this->assertEquals(0, $orphanedJadwalApars);
    }
}
