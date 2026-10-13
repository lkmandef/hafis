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
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class Phase49DualInspectionWorkflowFixTest extends TestCase
{
    use RefreshDatabase;

    private function createOperationalApar(array $attributes = []): Apar
    {
        $lokasi = Lokasi::create([
            'nama' => 'Lokasi Uji '.uniqid(),
            'kode' => 'LOK-'.uniqid(),
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $gedung = Gedung::create([
            'kode' => 'GDG-'.uniqid(),
            'nama' => 'Gedung Uji',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruang Uji',
            'kode' => 'R-'.uniqid(),
        ]);

        return Apar::create(array_merge([
            'kode' => 'APAR-P49-'.rand(100, 999),
            'nomor_register' => 'REG-P49-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-P49-'.uniqid(),
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Dekat pintu masuk',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => null,
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2025-01-01',
            'tanggal_kadaluarsa' => '2028-01-01',
        ], $attributes));
    }

    private function validFiveItemPayload(Apar $apar, int $bulan, int $tahun): array
    {
        $items = [];
        foreach (Pemeriksaan::K3RS_CHECKLIST_ITEMS as $itemNama) {
            $items[] = [
                'nama' => $itemNama,
                'hasil' => 'baik',
                'catatan' => null,
            ];
        }

        return [
            'apar_id' => $apar->id,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'pemeriksa_nama' => 'Petugas K3RS Ansari Saleh',
            'tanggal_pemeriksaan' => Carbon::create($tahun, $bulan, 10)->format('Y-m-d'),
            'catatan_pemeriksaan' => 'Kondisi tabung sesuai standar K3RS.',
            'items' => $items,
        ];
    }

    /**
     * 1. GET standalone create route without explicit period parameters loads successfully.
     */
    public function test_get_standalone_create_route_without_explicit_period_parameters_succeeds(): void
    {
        $apar = $this->createOperationalApar();

        $response = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id]));

        $response->assertStatus(200);
        $response->assertViewIs('pemeriksaan.create');
        $response->assertViewHas('isMonthlyWorkflow', true);
        $response->assertViewHas('periodeBulan', (int) Carbon::now()->month);
        $response->assertViewHas('periodeTahun', (int) Carbon::now()->year);
    }

    /**
     * 2. Confirm the rendered form contains exactly the five canonical items and the intended period.
     */
    public function test_rendered_form_contains_exactly_five_canonical_items_and_intended_period(): void
    {
        $apar = $this->createOperationalApar();
        $currentMonth = (int) Carbon::now()->month;
        $currentYear = (int) Carbon::now()->year;

        $response = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id]));

        $response->assertStatus(200);

        // Check exactly 5 canonical items are present
        foreach (Pemeriksaan::K3RS_CHECKLIST_ITEMS as $canonicalItem) {
            $response->assertSee($canonicalItem);
        }

        // Check legacy-only items are NOT in the rendered monthly form
        $response->assertDontSee('Kerapian/Kebersihan Luar');
        $response->assertDontSee('Pemasangan Rambu/Tanda APAR');
        $response->assertDontSee('Aksesibilitas/Keterjangkauan');
        $response->assertDontSee('Kondisi Breket/Gantungan');

        // Check intended period hidden fields
        $response->assertSee('name="periode_bulan" value="'.$currentMonth.'"', false);
        $response->assertSee('name="periode_tahun" value="'.$currentYear.'"', false);
    }

    /**
     * 3. Submit a valid payload corresponding to the rendered form and confirm successful persistence.
     */
    public function test_submit_valid_payload_corresponding_to_rendered_form_persists_successfully(): void
    {
        $apar = $this->createOperationalApar();
        $currentMonth = (int) Carbon::now()->month;
        $currentYear = (int) Carbon::now()->year;

        $payload = $this->validFiveItemPayload($apar, $currentMonth, $currentYear);

        $response = $this->post(route('pemeriksaan.store'), $payload);

        $response->assertSessionHasNoErrors();
        $pemeriksaan = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', $currentYear)
            ->where('periode_bulan', $currentMonth)
            ->first();

        $this->assertNotNull($pemeriksaan);
        $response->assertRedirect(route('pemeriksaan.show', $pemeriksaan->id));

        $this->assertEquals($currentYear, $pemeriksaan->periode_tahun);
        $this->assertEquals($currentMonth, $pemeriksaan->periode_bulan);
        $this->assertEquals(Pemeriksaan::HASIL_BAIK, $pemeriksaan->hasil_keseluruhan);
        $this->assertCount(5, $pemeriksaan->details);

        $persistedItems = $pemeriksaan->details->pluck('item_nama')->toArray();
        $this->assertEquals(array_values(Pemeriksaan::K3RS_CHECKLIST_ITEMS), $persistedItems);
    }

    /**
     * 4. Verify invalid or incomplete monthly checklist submissions remain rejected.
     */
    public function test_invalid_or_incomplete_monthly_checklist_submissions_remain_rejected(): void
    {
        $apar = $this->createOperationalApar();
        $currentMonth = (int) Carbon::now()->month;
        $currentYear = (int) Carbon::now()->year;

        // Incomplete: only 4 items instead of 5
        $payloadIncomplete = $this->validFiveItemPayload($apar, $currentMonth, $currentYear);
        array_pop($payloadIncomplete['items']);

        $response = $this->post(route('pemeriksaan.store'), $payloadIncomplete);
        $response->assertSessionHasErrors(['items']);
        $this->assertEquals(0, Pemeriksaan::where('apar_id', $apar->id)->count());

        // Invalid item name (non-canonical item)
        $payloadInvalidItem = $this->validFiveItemPayload($apar, $currentMonth, $currentYear);
        $payloadInvalidItem['items'][0]['nama'] = 'Kerapian/Kebersihan Luar';

        $responseInvalid = $this->post(route('pemeriksaan.store'), $payloadInvalidItem);
        $responseInvalid->assertSessionHasErrors(['items']);
        $this->assertEquals(0, Pemeriksaan::where('apar_id', $apar->id)->count());
    }

    /**
     * 5. Verify standalone scanner lookup defaults to the current period and routes to the correct form or existing result.
     */
    public function test_standalone_scanner_lookup_defaults_to_current_period_and_routes_correctly(): void
    {
        $apar = $this->createOperationalApar();
        $currentMonth = (int) Carbon::now()->month;
        $currentYear = (int) Carbon::now()->year;

        // Case A: Uninspected APAR -> routes to pemeriksaan.create with current period
        $resUninspected = $this->post(route('scan.cari'), [
            'kode' => $apar->kode,
        ]);

        $resUninspected->assertRedirect(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => $currentMonth,
            'periode_tahun' => $currentYear,
        ]));

        // Create inspection for current period
        $payload = $this->validFiveItemPayload($apar, $currentMonth, $currentYear);
        $this->post(route('pemeriksaan.store'), $payload);

        $existing = Pemeriksaan::findByAparPeriod($apar->id, $currentYear, $currentMonth);
        $this->assertNotNull($existing);

        // Case B: Already inspected APAR -> routes to existing pemeriksaan.show
        $resAlready = $this->post(route('scan.cari'), [
            'kode' => $apar->qr_code,
        ]);

        $resAlready->assertRedirect(route('pemeriksaan.show', $existing->id));
        $resAlready->assertSessionHas('info');
    }

    /**
     * 6. Verify a previously inspected APAR does not receive a duplicate header for the same month.
     */
    public function test_previously_inspected_apar_does_not_receive_duplicate_header_for_same_month(): void
    {
        $apar = $this->createOperationalApar();
        $currentMonth = (int) Carbon::now()->month;
        $currentYear = (int) Carbon::now()->year;

        // First inspection succeeds
        $payload1 = $this->validFiveItemPayload($apar, $currentMonth, $currentYear);
        $res1 = $this->post(route('pemeriksaan.store'), $payload1);
        $res1->assertSessionHasNoErrors();
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->where('periode_tahun', $currentYear)->where('periode_bulan', $currentMonth)->count());

        $existing = Pemeriksaan::findByAparPeriod($apar->id, $currentYear, $currentMonth);
        $this->assertNotNull($existing);

        // Second submission for the exact same period executes correction flow on existing header without creating duplicate
        $payload2 = $this->validFiveItemPayload($apar, $currentMonth, $currentYear);
        $payload2['catatan_pemeriksaan'] = 'Pemeriksaan koreksi periode sama.';
        $res2 = $this->post(route('pemeriksaan.store'), $payload2);

        $res2->assertSessionHasNoErrors();
        $res2->assertRedirect(route('pemeriksaan.show', $existing->id));
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->where('periode_tahun', $currentYear)->where('periode_bulan', $currentMonth)->count());
        $this->assertEquals('Pemeriksaan koreksi periode sama.', $existing->fresh()->catatan);
    }

    /**
     * 7. Verify explicitly selected periods remain preserved.
     */
    public function test_explicitly_selected_periods_remain_preserved(): void
    {
        $apar = $this->createOperationalApar();
        $selectedBulan = 8;
        $selectedTahun = 2026;

        // Scan with explicit period parameters (e.g. from Hasil Inspeksi page)
        $resScan = $this->post(route('scan.cari'), [
            'kode' => $apar->kode,
            'bulan' => $selectedBulan,
            'tahun' => $selectedTahun,
        ]);

        $resScan->assertRedirect(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => $selectedBulan,
            'periode_tahun' => $selectedTahun,
        ]));

        // GET create with explicit query parameters
        $resGet = $this->get(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => $selectedBulan,
            'periode_tahun' => $selectedTahun,
        ]));

        $resGet->assertStatus(200);
        $resGet->assertSee('name="periode_bulan" value="'.$selectedBulan.'"', false);
        $resGet->assertSee('name="periode_tahun" value="'.$selectedTahun.'"', false);

        // Store with explicit period parameters
        $payload = $this->validFiveItemPayload($apar, $selectedBulan, $selectedTahun);
        $resStore = $this->post(route('pemeriksaan.store'), $payload);
        $resStore->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pemeriksaans', [
            'apar_id' => $apar->id,
            'periode_bulan' => $selectedBulan,
            'periode_tahun' => $selectedTahun,
        ]);
    }

    /**
     * 8. Verify legacy schedule-based tests and workflows remain compatible.
     */
    public function test_legacy_schedule_based_workflows_remain_compatible(): void
    {
        // Create legacy hierarchy without lokasi_id
        $gedung = Gedung::create(['kode' => 'GDG-LEGACY-'.uniqid(), 'nama' => 'Gedung Legacy', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Legacy', 'kode' => 'R-LEGACY-'.uniqid()]);

        $apar = Apar::create([
            'kode' => 'APAR-LEGACY-'.rand(100, 999),
            'nomor_register' => 'REG-LEGACY-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-LEGACY-'.uniqid(),
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Lorong',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2025-01-01',
            'tanggal_kadaluarsa' => '2028-01-01',
        ]);

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Rutin Legacy',
            'tanggal_mulai' => Carbon::now()->startOfMonth(),
            'tanggal_selesai' => Carbon::now()->endOfMonth(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin Legacy',
        ]);
        $jadwal->apars()->attach($apar->id, ['status_pemeriksaan' => 'belum_diperiksa']);

        // GET create with jadwal_id and without period parameters loads legacy 10-item checklist
        $resCreate = $this->get(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal->id,
        ]));

        $resCreate->assertStatus(200);
        $resCreate->assertViewHas('isMonthlyWorkflow', false);
        $resCreate->assertViewHas('checklistItems', Pemeriksaan::DEFAULT_CHECKLIST_ITEMS);

        // Rendered form must NOT contain hidden period fields
        $resCreate->assertDontSee('name="periode_bulan"', false);
        $resCreate->assertDontSee('name="periode_tahun"', false);

        // Submit 10 legacy items with jadwal_id
        $items = [];
        foreach (Pemeriksaan::DEFAULT_CHECKLIST_ITEMS as $itemNama) {
            $items[] = [
                'nama' => $itemNama,
                'hasil' => 'baik',
                'catatan' => null,
            ];
        }

        $resStore = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal->id,
            'pemeriksa_nama' => 'Petugas Legacy',
            'catatan_pemeriksaan' => 'Pemeriksaan jadwal legacy berhasil.',
            'items' => $items,
        ]);

        $resStore->assertSessionHasNoErrors();
        $this->assertEquals(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $saved = Pemeriksaan::where('apar_id', $apar->id)->first();
        $this->assertEquals($jadwal->id, $saved->jadwal_id);
        $this->assertEquals((int) Carbon::now()->month, $saved->periode_bulan);
        $this->assertEquals((int) Carbon::now()->year, $saved->periode_tahun);
        $this->assertCount(10, $saved->details);
    }

    /**
     * 9. Verify a different month can still have its own inspection.
     */
    public function test_different_month_can_still_have_its_own_inspection(): void
    {
        $apar = $this->createOperationalApar();

        // Month 7
        $payloadMonth7 = $this->validFiveItemPayload($apar, 7, 2026);
        $res7 = $this->post(route('pemeriksaan.store'), $payloadMonth7);
        $res7->assertSessionHasNoErrors();

        // Month 8
        $payloadMonth8 = $this->validFiveItemPayload($apar, 8, 2026);
        $res8 = $this->post(route('pemeriksaan.store'), $payloadMonth8);
        $res8->assertSessionHasNoErrors();

        // Month 9
        $payloadMonth9 = $this->validFiveItemPayload($apar, 9, 2026);
        $res9 = $this->post(route('pemeriksaan.store'), $payloadMonth9);
        $res9->assertSessionHasNoErrors();

        $this->assertEquals(3, Pemeriksaan::where('apar_id', $apar->id)->count());
        $this->assertNotNull(Pemeriksaan::findByAparPeriod($apar->id, 2026, 7));
        $this->assertNotNull(Pemeriksaan::findByAparPeriod($apar->id, 2026, 8));
        $this->assertNotNull(Pemeriksaan::findByAparPeriod($apar->id, 2026, 9));
    }

    /**
     * 10. Verify existing database uniqueness and follow-up protections remain intact.
     */
    public function test_database_uniqueness_and_follow_up_protections_remain_intact(): void
    {
        $apar = $this->createOperationalApar();
        $tahun = 2026;
        $bulan = 8;

        // Direct DB insertion first record
        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => $tahun,
            'periode_bulan' => $bulan,
            'pemeriksa_nama' => 'Tester 1',
            'tanggal_pemeriksaan' => Carbon::create($tahun, $bulan, 5),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // 1. Eloquent model domain guard throws DomainException
        try {
            Pemeriksaan::create([
                'apar_id' => $apar->id,
                'periode_tahun' => $tahun,
                'periode_bulan' => $bulan,
                'pemeriksa_nama' => 'Tester 2',
                'tanggal_pemeriksaan' => Carbon::create($tahun, $bulan, 6),
                'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
                'status' => 'selesai',
            ]);
            $this->fail('Expected DomainException was not thrown by model guard');
        } catch (\DomainException $e) {
            $this->assertStringContainsString('sudah memiliki hasil pemeriksaan', $e->getMessage());
        }

        // 2. Database level UNIQUE constraint throws QueryException on raw insert
        $this->expectException(QueryException::class);
        DB::table('pemeriksaans')->insert([
            'apar_id' => $apar->id,
            'periode_tahun' => $tahun,
            'periode_bulan' => $bulan,
            'pemeriksa_nama' => 'Tester 3',
            'tanggal_pemeriksaan' => '2026-08-07',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Follow-up check: verify inspection with non-conforming items triggers 'perlu_tindakan'.
     */
    public function test_inspection_with_damaged_item_triggers_perlu_tindakan(): void
    {
        $apar = $this->createOperationalApar();
        $tahun = 2026;
        $bulan = 9;

        $payload = $this->validFiveItemPayload($apar, $bulan, $tahun);
        // Set Pin/Segel to 'tidak_baik'
        foreach ($payload['items'] as $idx => $item) {
            if ($item['nama'] === 'Pin/Segel') {
                $payload['items'][$idx]['hasil'] = 'tidak_baik';
                $payload['items'][$idx]['catatan'] = 'Segel putus';
            }
        }

        $response = $this->post(route('pemeriksaan.store'), $payload);
        $response->assertSessionHasNoErrors();

        $saved = Pemeriksaan::findByAparPeriod($apar->id, $tahun, $bulan);
        $this->assertNotNull($saved);
        $this->assertEquals(Pemeriksaan::HASIL_PERLU_TINDAKAN, $saved->hasil_keseluruhan);

        // APAR status updated to perhatian
        $apar->refresh();
        $this->assertEquals(Apar::STATUS_PERHATIAN, $apar->status);
    }
}
