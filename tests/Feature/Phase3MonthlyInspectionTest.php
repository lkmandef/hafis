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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase3MonthlyInspectionTest extends TestCase
{
    use RefreshDatabase;

    private function createOperationalApar(array $attributes = []): Apar
    {
        $lokasi = Lokasi::create([
            'nama' => 'IGD Lt. 1 Test '.uniqid(),
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
        $names = [1 => 'Tekanan', 2 => 'Pin/Segel', 3 => 'Handle', 4 => 'Kondisi Fisik', 5 => 'Nozel'];

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
     * 1. A new monthly inspection can be created without a Jadwal.
     */
    public function test_monthly_inspection_can_be_created_without_jadwal(): void
    {
        $apar = $this->createOperationalApar();

        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pemeriksaans', [
            'apar_id' => $apar->id,
            'jadwal_id' => null,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'hasil_keseluruhan' => 'baik',
        ]);
    }

    /**
     * 2. A valid month and year are persisted correctly.
     */
    public function test_valid_month_and_year_are_persisted_correctly(): void
    {
        $apar = $this->createOperationalApar();

        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 5,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->first();
        $this->assertNotNull($pem);
        $this->assertEquals(2026, $pem->periode_tahun);
        $this->assertEquals(5, $pem->periode_bulan);
        $this->assertTrue($pem->isMonthlyInspection());
        $this->assertEquals('Mei 2026', $pem->periode_label);
    }

    /**
     * 3. Invalid month values are rejected.
     */
    public function test_invalid_month_values_are_rejected(): void
    {
        $apar = $this->createOperationalApar();

        // Month 13 is invalid
        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 13,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $response->assertSessionHasErrors('periode_bulan');
        $this->assertEquals(0, Pemeriksaan::count());

        // Month 0 is invalid
        $response0 = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 0,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $response0->assertSessionHasErrors('periode_bulan');
        $this->assertEquals(0, Pemeriksaan::count());
    }

    /**
     * 4. A duplicate APAR/year/month submission updates or reuses the existing inspection rather than creating another record.
     */
    public function test_duplicate_submission_updates_existing_inspection_without_new_record(): void
    {
        $apar = $this->createOperationalApar();

        // First submission (Hasil: Baik)
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Pertama',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $firstId = Pemeriksaan::where('apar_id', $apar->id)->first()->id;

        // Second submission for the same month/year with updated inspector & finding
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Koreksi',
            'items' => $this->fiveChecklistPayload([1 => 'tidak_baik']), // Tekanan drop
        ]);

        // Assert record count remains exactly 1 (no duplicate record)
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $updatedPem = Pemeriksaan::where('apar_id', $apar->id)->first();
        $this->assertEquals($firstId, $updatedPem->id);
        $this->assertEquals('Petugas Koreksi', $updatedPem->pemeriksa_nama);
        $this->assertEquals(Pemeriksaan::HASIL_PERLU_TINDAKAN, $updatedPem->hasil_keseluruhan);
    }

    /**
     * 5. Correcting a monthly inspection preserves its record ID.
     */
    public function test_correcting_monthly_inspection_preserves_record_id(): void
    {
        $apar = $this->createOperationalApar();

        $initial = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 8,
            'pemeriksa_nama' => 'Petugas Awal',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        foreach ($this->fiveChecklistPayload() as $idx => $item) {
            PemeriksaanDetail::create([
                'pemeriksaan_id' => $initial->id,
                'item_nama' => $item['nama'],
                'hasil' => $item['hasil'],
                'urutan' => $idx,
            ]);
        }

        $originalId = $initial->id;

        // Koreksi via update route
        $response = $this->put(route('pemeriksaan.update', $initial->id), [
            'pemeriksa_nama' => 'Petugas Pengoreksi',
            'items' => $this->fiveChecklistPayload([2 => 'tidak_baik']), // Pin lepas
            'catatan_pemeriksaan' => 'Ditemukan pin segel terlepas saat audit ulang.',
        ]);

        $response->assertRedirect(route('pemeriksaan.show', $originalId));

        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $corrected = Pemeriksaan::find($originalId);
        $this->assertNotNull($corrected);
        $this->assertEquals('Petugas Pengoreksi', $corrected->pemeriksa_nama);
        $this->assertEquals(Pemeriksaan::HASIL_PERLU_TINDAKAN, $corrected->hasil_keseluruhan);
    }

    /**
     * 6. Exactly five checklist items are persisted for a new monthly inspection.
     */
    public function test_exactly_five_checklist_items_are_persisted_for_monthly_inspection(): void
    {
        $apar = $this->createOperationalApar();

        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->firstOrFail();
        $this->assertCount(5, $pem->details);

        $savedNames = $pem->details->pluck('item_nama')->toArray();
        $expectedNames = ['Tekanan', 'Pin/Segel', 'Handle', 'Kondisi Fisik', 'Nozel'];
        $this->assertEquals($expectedNames, $savedNames);
    }

    /**
     * 7. Updating an existing inspection does not duplicate checklist details.
     */
    public function test_updating_existing_inspection_does_not_duplicate_checklist_details(): void
    {
        $apar = $this->createOperationalApar();

        // Insert awal dengan 5 detail
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas A',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->firstOrFail();
        $this->assertEquals(5, PemeriksaanDetail::where('pemeriksaan_id', $pem->id)->count());

        // Koreksi ulang
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas B',
            'items' => $this->fiveChecklistPayload([4 => 'tidak_baik']),
        ]);

        $this->assertEquals(5, PemeriksaanDetail::where('pemeriksaan_id', $pem->id)->count());
        $detail4 = PemeriksaanDetail::where('pemeriksaan_id', $pem->id)->where('item_nama', 'Kondisi Fisik')->first();
        $this->assertEquals('tidak_baik', $detail4->hasil);
    }

    /**
     * 8. Negative checklist responses produce the appropriate non-good result.
     */
    public function test_negative_checklist_response_produces_perlu_tindakan_and_updates_apar_status(): void
    {
        $apar = $this->createOperationalApar(['status' => Apar::STATUS_BAIK]);

        // Simpan checklist dengan 1 temuan tidak baik
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload([1 => 'tidak_baik']), // Tekanan tidak baik
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->firstOrFail();
        $this->assertEquals(Pemeriksaan::HASIL_PERLU_TINDAKAN, $pem->hasil_keseluruhan);

        // Status APAR ter-upgrade ke perhatian
        $apar->refresh();
        $this->assertEquals(Apar::STATUS_PERHATIAN, $apar->status);
    }

    /**
     * 9. Inactive legacy APARs cannot be newly inspected.
     */
    public function test_inactive_legacy_apars_cannot_be_newly_inspected(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-LEGACY', 'nama' => 'Gedung Legacy', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruangan Legacy', 'kode' => 'R-LEG']);

        $inactiveLegacyApar = Apar::create([
            'kode' => 'APAR-LEGACY-INACTIVE',
            'qr_code' => 'HAFIS-QR-LEGACY-INACTIVE',
            'lokasi_id' => null, // Legacy preserved: no lokasi
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        $this->assertFalse($inactiveLegacyApar->isEligibleForInspection());

        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $inactiveLegacyApar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $response->assertRedirect(route('apar.show', $inactiveLegacyApar->id));
        $this->assertEquals(0, Pemeriksaan::where('apar_id', $inactiveLegacyApar->id)->count());
    }

    /**
     * 10. Existing legacy inspections with null period values remain intact.
     */
    public function test_existing_legacy_inspections_with_null_period_remain_intact(): void
    {
        $apar = $this->createOperationalApar();

        $legacyPem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'jadwal_id' => null,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Petugas Historis',
            'tanggal_pemeriksaan' => now()->subMonths(6),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $this->assertNull($legacyPem->periode_tahun);
        $this->assertNull($legacyPem->periode_bulan);
        $this->assertTrue($legacyPem->isLegacyInspection());
        $this->assertNull($legacyPem->periode_label);

        // Buat pemeriksaan bulanan baru pada APAR yang sama untuk periode sekarang
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Baru',
            'items' => $this->fiveChecklistPayload(),
        ]);

        // Legacy pemeriksaan tetap ada dan tidak tertimpa
        $this->assertDatabaseHas('pemeriksaans', ['id' => $legacyPem->id, 'periode_tahun' => null]);
        $this->assertEquals(2, Pemeriksaan::where('apar_id', $apar->id)->count());
    }

    /**
     * 11. Existing legacy inspection details remain intact.
     */
    public function test_existing_legacy_inspection_details_remain_intact(): void
    {
        $apar = $this->createOperationalApar();

        $legacyPem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Petugas Historis',
            'tanggal_pemeriksaan' => now()->subMonths(3),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Simulasikan 10 item checklist legacy
        for ($i = 1; $i <= 10; $i++) {
            PemeriksaanDetail::create([
                'pemeriksaan_id' => $legacyPem->id,
                'item_nama' => "Item Legacy {$i}",
                'hasil' => 'baik',
                'urutan' => $i,
            ]);
        }

        $this->assertEquals(10, $legacyPem->details()->count());

        // Buat pemeriksaan bulanan baru
        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Baru',
            'items' => $this->fiveChecklistPayload(),
        ]);

        // 10 detail legacy tetap utuh
        $this->assertEquals(10, $legacyPem->details()->count());
    }

    /**
     * 12. The old Jadwal relationship continues to work.
     */
    public function test_old_jadwal_relationship_continues_to_work(): void
    {
        $apar = $this->createOperationalApar();

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Rutin Oktober',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(14)->toDateString(),
            'status' => 'berjalan',
        ]);

        DB::table('jadwal_apars')->insert([
            'jadwal_id' => $jadwal->id,
            'apar_id' => $apar->id,
            'status_pemeriksaan' => 'belum_diperiksa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => $this->fiveChecklistPayload(),
        ]);

        $pem = Pemeriksaan::where('apar_id', $apar->id)->where('periode_tahun', 2026)->firstOrFail();
        $this->assertEquals($jadwal->id, $pem->jadwal_id);
        $this->assertInstanceOf(Jadwal::class, $pem->jadwal);
        $this->assertEquals('Jadwal Rutin Oktober', $pem->jadwal->nama);

        // Status di pivot jadwal_apars diperbarui
        $pivot = DB::table('jadwal_apars')->where('jadwal_id', $jadwal->id)->where('apar_id', $apar->id)->first();
        $this->assertEquals('sudah_diperiksa', $pivot->status_pemeriksaan);
    }

    /**
     * 13. Failed saves do not leave partial inspection records or detail records.
     */
    public function test_failed_save_does_not_leave_partial_records(): void
    {
        $apar = $this->createOperationalApar();

        // Kirim request yang gagal validasi (items kosong)
        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'items' => [], // Gagal validasi min:1
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertEquals(0, Pemeriksaan::count());
        $this->assertEquals(0, PemeriksaanDetail::count());
    }

    /**
     * 14. Monthly inspection list status correctly distinguishes inspected and not-inspected APARs.
     */
    public function test_monthly_inspection_list_distinguishes_inspected_and_not_inspected_apars(): void
    {
        $apar1 = $this->createOperationalApar(['kode' => 'TEMP-APAR-001']);
        $apar2 = $this->createOperationalApar(['kode' => 'TEMP-APAR-002']);

        // apar1 diinspeksi pada bulan 10 tahun 2026
        Pemeriksaan::create([
            'apar_id' => $apar1->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas Lapangan',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $this->assertTrue($apar1->isInspectedForPeriod(2026, 10));
        $this->assertFalse($apar2->isInspectedForPeriod(2026, 10));

        // Akses halaman ikhtisar pemeriksaan bulanan
        $response = $this->get(route('pemeriksaan.index', ['tahun' => 2026, 'bulan' => 10]));
        $response->assertStatus(200);
        $response->assertSee('TEMP-APAR-001');
        $response->assertSee('TEMP-APAR-002');
        $response->assertSee('Sudah Diperiksa');
        $response->assertSee('Belum Diperiksa');
    }
}
