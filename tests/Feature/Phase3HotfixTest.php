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
use App\Models\TindakLanjut;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase3HotfixTest extends TestCase
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
            'merek' => 'Servvo',
            'status' => Apar::STATUS_BAIK,
        ], $attributes));
    }

    private function fiveChecklistPayload(array $results = []): array
    {
        $items = [];
        $names = [
            1 => 'Tekanan',
            2 => 'Pin/Segel',
            3 => 'Handle',
            4 => 'Kondisi Fisik',
            5 => 'Nozel',
        ];

        foreach ($names as $idx => $name) {
            $items[] = [
                'nama' => $name,
                'hasil' => $results[$idx] ?? 'baik',
                'catatan' => null,
            ];
        }

        return $items;
    }

    /**
     * Fix 1: Database-level partial unique index exists and prevents duplicate monthly inspections.
     */
    public function test_partial_unique_index_prevents_duplicate_monthly_records(): void
    {
        $apar = $this->createOperationalApar();

        // Direct database insert of the first record
        DB::table('pemeriksaans')->insert([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas 1',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => 'baik',
            'status' => 'selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempting another direct DB insert with the same apar_id, periode_tahun, and periode_bulan must fail at the DB level
        $this->expectException(QueryException::class);

        DB::table('pemeriksaans')->insert([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas 2',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => 'baik',
            'status' => 'selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Fix 1: Legacy inspections with null periods are permitted to coexist without conflict.
     */
    public function test_partial_unique_index_permits_multiple_legacy_records_with_null_period(): void
    {
        $apar = $this->createOperationalApar();

        // Two legacy records with null periods for the same APAR must succeed
        $id1 = DB::table('pemeriksaans')->insertGetId([
            'apar_id' => $apar->id,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Petugas Legacy 1',
            'tanggal_pemeriksaan' => now()->subMonths(6),
            'hasil_keseluruhan' => 'baik',
            'status' => 'selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $id2 = DB::table('pemeriksaans')->insertGetId([
            'apar_id' => $apar->id,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Petugas Legacy 2',
            'tanggal_pemeriksaan' => now()->subMonths(3),
            'hasil_keseluruhan' => 'baik',
            'status' => 'selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertNotEquals($id1, $id2);
        $this->assertEquals(2, Pemeriksaan::where('apar_id', $apar->id)->whereNull('periode_tahun')->count());
    }

    /**
     * Fix 2: Checklist validation rejects unknown, duplicate, or missing items.
     */
    public function test_monthly_checklist_rejects_missing_or_unknown_items(): void
    {
        $apar = $this->createOperationalApar();

        // Missing Nozel (only 4 items)
        $invalidPayload = [
            ['nama' => 'Tekanan', 'hasil' => 'baik'],
            ['nama' => 'Pin/Segel', 'hasil' => 'baik'],
            ['nama' => 'Handle', 'hasil' => 'baik'],
            ['nama' => 'Kondisi Fisik', 'hasil' => 'baik'],
        ];

        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $invalidPayload,
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertEquals(0, Pemeriksaan::count());

        // Unknown item name
        $unknownPayload = [
            ['nama' => 'Tekanan', 'hasil' => 'baik'],
            ['nama' => 'Pin/Segel', 'hasil' => 'baik'],
            ['nama' => 'Handle', 'hasil' => 'baik'],
            ['nama' => 'Kondisi Fisik', 'hasil' => 'baik'],
            ['nama' => 'Parameter Fiktif', 'hasil' => 'baik'],
        ];

        $response2 = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $unknownPayload,
        ]);

        $response2->assertSessionHasErrors('items');
        $this->assertEquals(0, Pemeriksaan::count());
    }

    /**
     * Fix 2: Reordered checklist items preserve stable identities, IDs, and foreign keys.
     */
    public function test_reordered_items_preserve_stable_identities_and_foreign_keys(): void
    {
        $apar = $this->createOperationalApar();

        // 1. Initial submission in canonical order (Tekanan = idx 1)
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Pertama',
            'items' => $this->fiveChecklistPayload([1 => 'tidak_baik']), // Tekanan drop
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->firstOrFail();
        $detailTekanan = $pem->details()->where('item_nama', 'Tekanan')->firstOrFail();
        $tekananId = $detailTekanan->id;

        // 2. Attach a TindakLanjut ticket to this specific PemeriksaanDetail
        $tindakLanjut = TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pem->id,
            'pemeriksaan_detail_id' => $tekananId,
            'jenis_tindakan' => 'pengisian_ulang',
            'uraian_tindakan' => 'Isi ulang tekanan N2',
            'status' => TindakLanjut::STATUS_DALAM_PROSES,
            'prioritas' => 'tinggi',
            'petugas_pelaksana' => 'Teknisi Vendor',
            'tanggal_tindakan' => now()->toDateString(),
        ]);

        $this->assertEquals($tekananId, $tindakLanjut->pemeriksaan_detail_id);

        // 3. Correction submission with items REORDERED: Nozel first, Tekanan last!
        $reorderedPayload = [
            ['nama' => 'Nozel', 'hasil' => 'baik', 'catatan' => null],
            ['nama' => 'Kondisi Fisik', 'hasil' => 'baik', 'catatan' => null],
            ['nama' => 'Handle', 'hasil' => 'baik', 'catatan' => null],
            ['nama' => 'Pin/Segel', 'hasil' => 'baik', 'catatan' => null],
            ['nama' => 'Tekanan', 'hasil' => 'tidak_baik', 'catatan' => 'Masih drop'],
        ];

        $this->put(route('pemeriksaan.update', $pem->id), [
            'pemeriksa_nama' => 'Petugas Koreksi',
            'items' => $reorderedPayload,
        ]);

        $pem->refresh();
        $this->assertCount(5, $pem->details);

        // Verify that Tekanan still has the EXACT SAME ID and did not get overwritten by Nozel!
        $updatedDetailTekanan = $pem->details()->where('item_nama', 'Tekanan')->firstOrFail();
        $this->assertEquals($tekananId, $updatedDetailTekanan->id);
        $this->assertEquals('Masih drop', $updatedDetailTekanan->catatan);

        // Verify TindakLanjut still points to the same ID, and that row is still Tekanan
        $tindakLanjut->refresh();
        $this->assertEquals($tekananId, $tindakLanjut->pemeriksaan_detail_id);
        $this->assertEquals('Tekanan', $tindakLanjut->pemeriksaanDetail->item_nama);
    }

    /**
     * Fix 3: A bad monthly result corrected to good restores APAR status to baik.
     */
    public function test_bad_monthly_result_corrected_to_good_restores_apar_status(): void
    {
        $apar = $this->createOperationalApar(['status' => Apar::STATUS_BAIK]);

        // Submit bad inspection -> updates APAR to perhatian
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas A',
            'items' => $this->fiveChecklistPayload([1 => 'tidak_baik']),
        ]);

        $apar->refresh();
        $this->assertEquals(Apar::STATUS_PERHATIAN, $apar->status);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->firstOrFail();

        // Correct inspection to good -> restores APAR to baik
        $this->put(route('pemeriksaan.update', $pem->id), [
            'pemeriksa_nama' => 'Petugas A',
            'items' => $this->fiveChecklistPayload(), // All good
        ]);

        $apar->refresh();
        $this->assertEquals(Apar::STATUS_BAIK, $apar->status);
    }

    /**
     * Fix 3: An APAR with an open follow-up ticket does NOT restore to baik on good inspection.
     */
    public function test_apar_with_open_follow_up_ticket_does_not_restore_to_baik(): void
    {
        $apar = $this->createOperationalApar(['status' => Apar::STATUS_PERHATIAN]);

        // Create an open follow-up ticket on this APAR
        TindakLanjut::create([
            'apar_id' => $apar->id,
            'jenis_tindakan' => 'perbaikan',
            'uraian_tindakan' => 'Penggantian pin dan segel rusak',
            'status' => TindakLanjut::STATUS_DALAM_PROSES,
            'prioritas' => 'tinggi',
            'petugas_pelaksana' => 'Teknisi K3RS',
            'tanggal_tindakan' => now()->toDateString(),
        ]);

        // Submit a good inspection
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas B',
            'items' => $this->fiveChecklistPayload(), // All good
        ]);

        // APAR status must remain perhatian because of open follow-up ticket
        $apar->refresh();
        $this->assertEquals(Apar::STATUS_PERHATIAN, $apar->status);
    }

    /**
     * Fix 3: Historical inspection correction does not regress last inspection date.
     */
    public function test_historical_inspection_correction_does_not_regress_last_inspection_date(): void
    {
        $apar = $this->createOperationalApar([
            'tanggal_pemeriksaan_terakhir' => '2026-10-10',
        ]);

        // Create an older inspection for August 2026
        $pem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 8,
            'pemeriksa_nama' => 'Petugas Historis',
            'tanggal_pemeriksaan' => '2026-08-15 10:00:00',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        foreach ($this->fiveChecklistPayload() as $item) {
            $pem->details()->create([
                'item_nama' => $item['nama'],
                'hasil' => $item['hasil'],
                'urutan' => 0,
            ]);
        }

        // Correct the August inspection
        $this->put(route('pemeriksaan.update', $pem->id), [
            'pemeriksa_nama' => 'Petugas Pengoreksi',
            'tanggal_pemeriksaan' => '2026-08-15 11:00:00',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $apar->refresh();
        // Last inspection date must remain 2026-10-10 and NOT regress to 2026-08-15!
        $this->assertEquals('2026-10-10', $apar->tanggal_pemeriksaan_terakhir->toDateString());
    }

    /**
     * Fix 3: An APAR with status rusak is not cleared or restored to baik by monthly inspection.
     */
    public function test_apar_with_status_rusak_is_not_restored_by_good_inspection(): void
    {
        $apar = $this->createOperationalApar(['status' => Apar::STATUS_RUSAK]);

        // Submit good monthly inspection
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas C',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $apar->refresh();
        $this->assertEquals(Apar::STATUS_RUSAK, $apar->status);
    }

    /**
     * Fix 3: Failed transaction rolls back and does not partially update details or APAR.
     */
    public function test_failed_save_does_not_partially_update_details_or_apar(): void
    {
        $apar = $this->createOperationalApar(['status' => Apar::STATUS_BAIK]);

        // Send invalid payload with validation errors
        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => '', // Fails required validation
            'items' => $this->fiveChecklistPayload(),
        ]);

        $response->assertSessionHasErrors('pemeriksa_nama');
        $this->assertEquals(0, Pemeriksaan::count());
        $this->assertEquals(0, PemeriksaanDetail::count());

        $apar->refresh();
        $this->assertEquals(Apar::STATUS_BAIK, $apar->status);
        $this->assertNull($apar->tanggal_pemeriksaan_terakhir);
    }

    /**
     * Fix 4: Year validation rejects years outside 2000..2100 consistently.
     */
    public function test_year_validation_rejects_out_of_bounds_years(): void
    {
        $apar = $this->createOperationalApar();

        // Year 1999 rejected
        $response1 = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 1999,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);
        $response1->assertSessionHasErrors('periode_tahun');

        // Year 2101 rejected
        $response2 = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2101,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);
        $response2->assertSessionHasErrors('periode_tahun');

        $this->assertEquals(0, Pemeriksaan::count());
    }

    /**
     * Fix 4: Scheduled inspection duplicate is handled gracefully without DomainException.
     */
    public function test_scheduled_inspection_duplicate_updates_in_place_without_crashing(): void
    {
        $apar = $this->createOperationalApar();

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Rutin Oktober',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-31',
            'status' => 'berjalan',
        ]);

        DB::table('jadwal_apars')->insert([
            'jadwal_id' => $jadwal->id,
            'apar_id' => $apar->id,
            'status_pemeriksaan' => 'belum_diperiksa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // First inspection done independently
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Awal',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $initialPem = Pemeriksaan::where('apar_id', $apar->id)->first();
        $initialId = $initialPem->id;

        // Second submission for same APAR and same month with jadwal_id attached
        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Jadwal',
            'items' => $this->fiveChecklistPayload(),
        ]);

        // Must redirect gracefully, NOT crash with unhandled 500
        $response->assertRedirect(route('pemeriksaan.show', $initialId));

        // Exactly 1 inspection record preserved with original ID
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $updatedPem = Pemeriksaan::find($initialId);
        $this->assertEquals($jadwal->id, $updatedPem->jadwal_id);

        // Schedule pivot updated to sudah_diperiksa
        $pivot = DB::table('jadwal_apars')
            ->where('jadwal_id', $jadwal->id)
            ->where('apar_id', $apar->id)
            ->first();
        $this->assertEquals('sudah_diperiksa', $pivot->status_pemeriksaan);
    }
}
