<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use App\Services\SmartInspectionService;
use Carbon\Carbon;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartInspectionServiceTest extends TestCase
{
    use RefreshDatabase;

    private SmartInspectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SmartInspectionService::class);
    }

    private function createOperationalApar(): Apar
    {
        $gedung = Gedung::create(['kode' => 'GDG-SIS', 'nama' => 'Gedung SIS', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'kode' => 'R-SIS', 'nama' => 'Ruang SIS', 'kategori' => 'pelayanan']);
        $lokasi = Lokasi::create([
            'nama' => 'Koridor Utama SIS',
            'kode' => 'LOK-SIS',
            'kategori' => 'koridor',
            'status' => Lokasi::STATUS_AKTIF,
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
        ]);

        return Apar::create([
            'kode' => 'APAR-SIS-01',
            'nomor_register' => 'REG-SIS-001',
            'qr_code' => 'HAFIS-QR-SIS01',
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);
    }

    private function generateValidChecklistPayload(): array
    {
        return [
            'pemeriksa_nama' => 'Petugas Lapangan K3RS',
            'tanggal_pemeriksaan' => '2026-11-10 10:00:00',
            'kesimpulan_manual' => 'baik',
            'catatan_pemeriksaan' => 'Kondisi fisik prima',
            'items' => [
                ['nama' => 'Tekanan', 'hasil' => 'baik', 'catatan' => 'Zona hijau'],
                ['nama' => 'Pin/Segel', 'hasil' => 'baik', 'catatan' => 'Utuh'],
                ['nama' => 'Handle', 'hasil' => 'baik', 'catatan' => 'Kokoh'],
                ['nama' => 'Kondisi Fisik', 'hasil' => 'baik', 'catatan' => 'Bebas karat'],
                ['nama' => 'Nozel', 'hasil' => 'baik', 'catatan' => 'Lentur tanpa sumbatan'],
            ],
        ];
    }

    public function test_evaluate_context_detects_multi_period_when_both_months_uninspected(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::create(2026, 11, 10, 10, 0, 0);

        $context = $this->service->evaluateAparContext($apar, $now);

        $this->assertSame('multi_period', $context['mode']);
        $this->assertSame(2026, $context['current_period']['tahun']);
        $this->assertSame(11, $context['current_period']['bulan']);
        $this->assertFalse($context['current_period']['exists']);

        $this->assertSame(2026, $context['backlog_period']['tahun']);
        $this->assertSame(10, $context['backlog_period']['bulan']);
        $this->assertFalse($context['backlog_period']['exists']);
    }

    public function test_evaluate_context_detects_regular_when_previous_month_already_inspected(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::create(2026, 11, 10, 10, 0, 0);

        // Buat pemeriksaan untuk Oktober 2026
        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => Carbon::create(2026, 10, 15),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'pemeriksa_nama' => 'Petugas Okt',
        ]);

        $context = $this->service->evaluateAparContext($apar, $now);

        $this->assertSame('regular', $context['mode']);
        $this->assertTrue($context['backlog_period']['exists']);
        $this->assertFalse($context['current_period']['exists']);
    }

    public function test_evaluate_context_detects_backlog_only_when_current_month_inspected_first(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::create(2026, 11, 10, 10, 0, 0);

        // November sudah diperiksa tanggal 2 Nov, tapi Oktober tertinggal
        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 11,
            'tanggal_pemeriksaan' => Carbon::create(2026, 11, 2),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'pemeriksa_nama' => 'Petugas Nov',
        ]);

        $context = $this->service->evaluateAparContext($apar, $now);

        $this->assertSame('backlog_only', $context['mode']);
        $this->assertTrue($context['current_period']['exists']);
        $this->assertFalse($context['backlog_period']['exists']);
    }

    public function test_evaluate_context_detects_completed_when_both_months_inspected(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::create(2026, 11, 10, 10, 0, 0);

        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => Carbon::create(2026, 10, 15),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'pemeriksa_nama' => 'Petugas Okt',
        ]);

        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 11,
            'tanggal_pemeriksaan' => Carbon::create(2026, 11, 5),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'pemeriksa_nama' => 'Petugas Nov',
        ]);

        $context = $this->service->evaluateAparContext($apar, $now);

        $this->assertSame('completed', $context['mode']);
    }

    public function test_evaluate_context_detects_ineligible_for_inactive_apar(): void
    {
        $apar = $this->createOperationalApar();
        $apar->update(['status' => Apar::STATUS_TIDAK_AKTIF]);

        $context = $this->service->evaluateAparContext($apar);

        $this->assertSame('ineligible', $context['mode']);
        $this->assertFalse($context['is_eligible']);
    }

    public function test_execute_multi_period_creates_master_and_linked_record_with_zero_detail_duplication(): void
    {
        $user = $this->actingAsPetugas(['name' => 'Inspektur Faisal']);
        $apar = $this->createOperationalApar();
        $payload = $this->generateValidChecklistPayload();
        $payload['resolve_backlog'] = true;

        $context = $this->service->evaluateAparContext($apar, Carbon::parse('2026-11-10 10:00:00'));
        $master = $this->service->executeInspection($apar, $payload, $user, $context);

        // 1. Verifikasi Master Record (November 2026)
        $this->assertInstanceOf(Pemeriksaan::class, $master);
        $this->assertSame(2026, $master->periode_tahun);
        $this->assertSame(11, $master->periode_bulan);
        $this->assertSame(Pemeriksaan::TIPE_TEPAT_WAKTU, $master->tipe_penyelesaian);
        $this->assertNull($master->pemeriksaan_induk_id);
        $this->assertCount(5, $master->details);

        // 2. Verifikasi Linked Record (Oktober 2026)
        $linked = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', 2026)
            ->where('periode_bulan', 10)
            ->first();

        $this->assertNotNull($linked);
        $this->assertSame($master->id, $linked->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $linked->tipe_penyelesaian);
        $this->assertSame('2026-11-10 10:00:00', $linked->tanggal_pemeriksaan->format('Y-m-d H:i:s'));
        // Nol duplikasi checklist pada baris linked
        $this->assertSame(0, $linked->details()->count());

        // 3. Verifikasi Relasi Bidirectional
        $this->assertSame($master->id, $linked->pemeriksaanInduk->id);
        $this->assertTrue($master->resolusiAnak->contains('id', $linked->id));

        // 4. Verifikasi Scopes
        $this->assertSame(1, Pemeriksaan::aktivitasFisik()->where('apar_id', $apar->id)->count());
        $this->assertSame(1, Pemeriksaan::terlambat()->where('apar_id', $apar->id)->count());
        $this->assertSame(1, Pemeriksaan::tepatWaktu()->where('apar_id', $apar->id)->count());

        // 5. Verifikasi Tanggal Pemeriksaan Terakhir APAR Terupdate
        $apar->refresh();
        $this->assertSame('2026-11-10', $apar->tanggal_pemeriksaan_terakhir->format('Y-m-d'));
    }

    public function test_execute_backlog_only_reuses_existing_physical_inspection_without_duplication(): void
    {
        $user = $this->actingAsPetugas(['name' => 'Petugas Lapangan']);
        $apar = $this->createOperationalApar();

        // 1. November sudah diperiksa fisik secara normal terlebih dahulu
        $existingPhysical = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 11,
            'tanggal_pemeriksaan' => Carbon::parse('2026-11-05 09:30:00'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'status' => 'selesai',
            'pemeriksa_nama' => 'Petugas Lapangan',
        ]);
        $existingPhysical->details()->create([
            'item_nama' => 'Tekanan',
            'hasil' => 'baik',
            'urutan' => 0,
        ]);

        // 2. Evaluasi mendeteksi backlog_only (Oktober tertinggal)
        $context = $this->service->evaluateAparContext($apar, Carbon::parse('2026-11-10 10:00:00'));
        $this->assertSame('backlog_only', $context['mode']);

        // 3. Eksekusi resolusi backlog_only
        $backlogRecord = $this->service->executeInspection($apar, [
            'resolve_backlog' => true,
        ], $user, $context);

        // 4. Verifikasi Linked Record untuk Oktober 2026
        $this->assertInstanceOf(Pemeriksaan::class, $backlogRecord);
        $this->assertSame(2026, $backlogRecord->periode_tahun);
        $this->assertSame(10, $backlogRecord->periode_bulan);
        $this->assertSame($existingPhysical->id, $backlogRecord->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $backlogRecord->tipe_penyelesaian);
        $this->assertSame('2026-11-05 09:30:00', $backlogRecord->tanggal_pemeriksaan->format('Y-m-d H:i:s'));

        // 5. Verifikasi ZERO checklist details duplication pada linked record
        $this->assertSame(0, $backlogRecord->details()->count());

        // 6. Verifikasi relasi
        $this->assertSame($existingPhysical->id, $backlogRecord->pemeriksaanInduk->id);
    }

    public function test_execute_inspection_throws_exception_on_ineligible_apar(): void
    {
        $user = $this->actingAsPetugas();
        $apar = $this->createOperationalApar();
        $apar->update(['status' => Apar::STATUS_TIDAK_AKTIF]);

        $this->expectException(DomainException::class);
        $this->service->executeInspection($apar, $this->generateValidChecklistPayload(), $user);
    }
}
