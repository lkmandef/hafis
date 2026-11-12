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

class SmartInspectionWorkflowIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    private function createOperationalApar(array $attributes = []): Apar
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-INT-'.uniqid(),
            'nama' => 'Gedung Integrasi '.uniqid(),
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruang Integrasi',
            'kode' => 'R-INT-'.uniqid(),
        ]);

        $lokasi = Lokasi::create([
            'nama' => 'Koridor Integrasi '.uniqid(),
            'kode' => 'LOK-INT-'.uniqid(),
            'status' => Lokasi::STATUS_AKTIF,
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
        ]);

        return Apar::create(array_merge([
            'kode' => 'APAR-INT-'.rand(100, 999),
            'nomor_register' => 'REG-INT-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-INT-'.uniqid(),
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
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
            'tanggal_pemeriksaan' => Carbon::create($tahun, $bulan, 10, 8, 30)->format('Y-m-d H:i:s'),
            'catatan_pemeriksaan' => 'Kondisi tabung prima sesuai standar K3RS.',
            'items' => $items,
        ];
    }

    // ==========================================
    // FASE 3: JADWAL STATUS & DASHBOARD TESTS
    // ==========================================

    /**
     * Test: Jadwal yang telah memeriksa seluruh target berubah status menjadi 'selesai'.
     */
    public function test_jadwal_sinkronkan_status_becomes_selesai_when_all_targets_inspected(): void
    {
        $apar1 = $this->createOperationalApar();
        $apar2 = $this->createOperationalApar();

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Target Lengkap',
            'tanggal_mulai' => Carbon::now()->subDays(5),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'tipe_target' => 'custom',
            'status' => 'berjalan',
        ]);
        $jadwal->apars()->attach([
            $apar1->id => ['status_pemeriksaan' => 'sudah_diperiksa'],
            $apar2->id => ['status_pemeriksaan' => 'sudah_diperiksa'],
        ]);

        $jadwal->sinkronkanStatus();
        $this->assertSame('selesai', $jadwal->fresh()->status);
    }

    /**
     * Test: Jadwal yang kedaluwarsa tetapi target belum selesai TIDAK otomatis menjadi 'selesai'.
     */
    public function test_jadwal_sinkronkan_status_does_not_falsely_complete_expired_incomplete_schedule(): void
    {
        $apar = $this->createOperationalApar();

        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Belum Selesai Tapi Lewat Waktu',
            'tanggal_mulai' => Carbon::now()->subDays(20),
            'tanggal_selesai' => Carbon::now()->subDays(5),
            'tipe_target' => 'custom',
            'status' => 'berjalan',
        ]);
        $jadwal->apars()->attach([
            $apar->id => ['status_pemeriksaan' => 'belum_diperiksa'],
        ]);

        $jadwal->sinkronkanStatus();
        // Sesuai konvensi status, jadwal yang belum tuntas tidak boleh ditandai 'selesai'
        $this->assertSame('berjalan', $jadwal->fresh()->status);
    }

    // ==========================================
    // FASE 4: SCANNER & CONTROLLER INTEGRATION
    // ==========================================

    /**
     * Test: Scan APAR yang belum diperiksa M-1 dan M mengarahkan ke form dengan pesan multi-period.
     */
    public function test_scan_uninspected_apar_redirects_to_create_with_multi_period_flash(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::now();

        $response = $this->post(route('scan.cari'), [
            'kode' => $apar->qr_code,
        ]);

        $response->assertRedirect(route('pemeriksaan.create', [
            'apar_id' => $apar->id,
            'periode_bulan' => (int) $now->month,
            'periode_tahun' => (int) $now->year,
        ]));
        $response->assertSessionHas('success');
    }

    /**
     * Test: Simpan pemeriksaan multi-period menghasilkan Master (M) dan Linked Record (M-1).
     */
    public function test_store_multi_period_creates_master_and_linked_record_with_zero_detail_duplication(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::now();
        $prev = $now->copy()->subMonth();

        $payload = $this->validFiveItemPayload($apar, (int) $now->month, (int) $now->year);
        $payload['resolve_backlog'] = true;

        $response = $this->post(route('pemeriksaan.store'), $payload);
        $response->assertSessionHasNoErrors();

        // 1. Master Record untuk periode berjalan (M)
        $master = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', (int) $now->year)
            ->where('periode_bulan', (int) $now->month)
            ->first();

        $this->assertNotNull($master);
        $this->assertNull($master->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TEPAT_WAKTU, $master->tipe_penyelesaian);
        $this->assertCount(5, $master->details);

        // 2. Linked Record untuk periode sebelumnya (M-1)
        $linked = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', (int) $prev->year)
            ->where('periode_bulan', (int) $prev->month)
            ->first();

        $this->assertNotNull($linked);
        $this->assertSame($master->id, $linked->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $linked->tipe_penyelesaian);
        // Nol duplikasi checklist pada baris linked record
        $this->assertSame(0, $linked->details()->count());

        $response->assertRedirect(route('pemeriksaan.show', $master->id));
        $response->assertSessionHas('success');
    }

    /**
     * Test: Simpan pemeriksaan reguler (tanpa resolve_backlog) hanya membuat 1 record.
     */
    public function test_store_regular_without_resolve_backlog_only_creates_single_record(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::now();
        $prev = $now->copy()->subMonth();

        $payload = $this->validFiveItemPayload($apar, (int) $now->month, (int) $now->year);
        $payload['resolve_backlog'] = false;

        $response = $this->post(route('pemeriksaan.store'), $payload);
        $response->assertSessionHasNoErrors();

        $this->assertSame(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $this->assertNull(Pemeriksaan::findByAparPeriod($apar->id, (int) $prev->year, (int) $prev->month));
    }

    /**
     * Test: APAR tidak aktif tetap dicegah dari scan dan pemeriksaan.
     */
    public function test_inactive_apar_cannot_be_scanned_or_stored(): void
    {
        $apar = $this->createOperationalApar(['status' => Apar::STATUS_TIDAK_AKTIF]);
        $now = Carbon::now();

        // 1. Scan dicegah
        $responseScan = $this->post(route('scan.cari'), ['kode' => $apar->kode]);
        $responseScan->assertRedirect(route('apar.show', ['id' => $apar->id]));
        $responseScan->assertSessionHas('warning');

        // 2. Akses create dicegah
        $responseCreate = $this->get(route('pemeriksaan.create', ['apar_id' => $apar->id]));
        $responseCreate->assertRedirect(route('apar.show', $apar->id));
        $responseCreate->assertSessionHas('warning');

        // 3. Post store dicegah
        $payload = $this->validFiveItemPayload($apar, (int) $now->month, (int) $now->year);
        $responseStore = $this->post(route('pemeriksaan.store'), $payload);
        $responseStore->assertRedirect(route('apar.show', $apar->id));
        $responseStore->assertSessionHas('error');

        $this->assertSame(0, Pemeriksaan::where('apar_id', $apar->id)->count());
    }
}
