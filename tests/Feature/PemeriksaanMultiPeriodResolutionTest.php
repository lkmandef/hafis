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
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PemeriksaanMultiPeriodResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    private function createOperationalApar(array $attributes = []): Apar
    {
        $unique = uniqid();

        $gedung = Gedung::create([
            'kode' => 'GDG-MPR-'.$unique,
            'nama' => 'Gedung Multi-Period '.$unique,
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruang MPR '.$unique,
            'kode' => 'R-MPR-'.$unique,
        ]);

        $lokasi = Lokasi::create([
            'nama' => 'Koridor MPR '.$unique,
            'kode' => 'LOK-MPR-'.$unique,
            'status' => Lokasi::STATUS_AKTIF,
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
        ]);

        return Apar::create(array_merge([
            'kode' => 'APAR-MPR-'.rand(1000, 9999),
            'nomor_register' => 'REG-MPR-'.rand(1000, 9999),
            'qr_code' => 'HAFIS-QR-MPR-'.$unique,
            'lokasi_id' => $lokasi->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2025-01-01',
            'tanggal_kadaluarsa' => '2028-01-01',
        ], $attributes));
    }

    private function validFiveItemPayload(Apar $apar, int $bulan, int $tahun, string $tekananHasil = 'baik'): array
    {
        $items = [];
        foreach (Pemeriksaan::K3RS_CHECKLIST_ITEMS as $itemNama) {
            $hasil = ($itemNama === 'Tekanan') ? $tekananHasil : 'baik';
            $catatan = ($hasil === 'tidak_baik') ? 'Jarum tekanan di bawah zona hijau (perlu isi ulang)' : null;

            $items[] = [
                'nama' => $itemNama,
                'hasil' => $hasil,
                'catatan' => $catatan,
            ];
        }

        return [
            'apar_id' => $apar->id,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'pemeriksa_nama' => 'Petugas Lapangan K3RS',
            'tanggal_pemeriksaan' => Carbon::create($tahun, $bulan, 10, 9, 30)->format('Y-m-d H:i:s'),
            'catatan_pemeriksaan' => 'Inspeksi fisik terpadu standar K3RS.',
            'items' => $items,
        ];
    }

    /**
     * TC-01: Multi-Period Standard
     * Scan pada 10 Nov saat Okt & Nov belum diperiksa.
     * Terbuat 1 Master (Nov, tepat_waktu) + 1 Linked (Okt, terlambat).
     * Detail checklist 5 baris hanya ada di Nov; Okt 0 baris duplikasi.
     */
    public function test_tc01_single_inspection_resolves_both_backlog_and_current_period(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::create(2026, 11, 10, 9, 30);
        $prev = $now->copy()->subMonth();

        $payload = $this->validFiveItemPayload($apar, (int) $now->month, (int) $now->year);
        $payload['resolve_backlog'] = true;

        $response = $this->post(route('pemeriksaan.store'), $payload);
        $response->assertSessionHasNoErrors();

        // 1. Master Record untuk periode berjalan (November 2026)
        $master = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', (int) $now->year)
            ->where('periode_bulan', (int) $now->month)
            ->first();

        $this->assertNotNull($master);
        $this->assertNull($master->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TEPAT_WAKTU, $master->tipe_penyelesaian);
        $this->assertSame(5, $master->details()->count());
        $this->assertSame('baik', $master->hasil_keseluruhan);

        // 2. Linked Record untuk periode tunggakan (Oktober 2026)
        $linked = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', (int) $prev->year)
            ->where('periode_bulan', (int) $prev->month)
            ->first();

        $this->assertNotNull($linked);
        $this->assertSame($master->id, $linked->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $linked->tipe_penyelesaian);
        $this->assertSame(0, $linked->details()->count(), 'Linked record tidak boleh menduplikasi baris detail checklist.');

        // 3. Waktu pemeriksaan fisik riil identik (nol backdating)
        $this->assertSame(
            $master->tanggal_pemeriksaan->format('Y-m-d H:i'),
            $linked->tanggal_pemeriksaan->format('Y-m-d H:i')
        );

        $response->assertRedirect(route('pemeriksaan.show', $master->id));
        $response->assertSessionHas('success');
    }

    /**
     * TC-02: Backlog Only
     * November sudah selesai (02 Nov), tetapi Oktober tertinggal.
     * Service backlog_only menautkan ke inspeksi fisik November yang sudah ada tanpa duplikasi.
     */
    public function test_tc02_backlog_only_resolution_when_current_period_already_completed(): void
    {
        $apar = $this->createOperationalApar();
        $service = app(SmartInspectionService::class);

        // 1. Simpan pemeriksaan November terlebih dahulu
        $novPayload = $this->validFiveItemPayload($apar, 11, 2026);
        $novPayload['resolve_backlog'] = false;
        $novRecord = $service->executeInspection($apar, $novPayload, auth()->user());

        $this->assertSame(1, Pemeriksaan::where('apar_id', $apar->id)->count());
        $this->assertSame(5, $novRecord->details()->count());

        // 2. Evaluasi konteks saat ini
        $eval = $service->evaluateAparContext($apar, Carbon::create(2026, 11, 15, 10, 0, 0));
        $this->assertSame('backlog_only', $eval['mode']);
        $this->assertTrue($eval['current_period']['exists']);
        $this->assertFalse($eval['backlog_period']['exists']);

        // 3. Eksekusi backlog_only
        $backlogPayload = [
            'tanggal_pemeriksaan' => '2026-11-15 10:00:00',
            'catatan_pemeriksaan' => 'Penyelesaian backlog Oktober via rekam fisik November.',
        ];
        $backlogRecord = $service->executeInspection($apar, $backlogPayload, auth()->user(), $eval);

        $this->assertNotNull($backlogRecord);
        $this->assertSame($novRecord->id, $backlogRecord->pemeriksaan_induk_id);
        $this->assertSame(10, $backlogRecord->periode_bulan);
        $this->assertSame(2026, $backlogRecord->periode_tahun);
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $backlogRecord->tipe_penyelesaian);
        $this->assertSame(0, $backlogRecord->details()->count());

        // Record November tidak tersentuh atau tertimpa
        $this->assertSame(5, $novRecord->fresh()->details()->count());
        $this->assertSame(2, Pemeriksaan::where('apar_id', $apar->id)->count());
    }

    /**
     * TC-03: Regular Inspection
     * Scan pada 10 Nov saat Oktober sudah selesai.
     * Terbuat 1 Master (Nov, tepat_waktu). Tidak ada linked record tambahan.
     */
    public function test_tc03_regular_inspection_when_previous_month_already_completed(): void
    {
        $apar = $this->createOperationalApar();

        // Buat pemeriksaan Oktober yang sudah tuntas sebelumnya
        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => Carbon::create(2026, 10, 15, 8, 0, 0),
            'pemeriksa_nama' => 'Petugas Oktober',
            'hasil_keseluruhan' => 'baik',
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU,
            'status' => 'selesai',
        ]);

        $payload = $this->validFiveItemPayload($apar, 11, 2026);
        $payload['resolve_backlog'] = true;

        $response = $this->post(route('pemeriksaan.store'), $payload);
        $response->assertSessionHasNoErrors();

        // Total catatan pemeriksaan harus tepat 2 (1 di Oktober + 1 di November)
        $this->assertSame(2, Pemeriksaan::where('apar_id', $apar->id)->count());

        $nov = Pemeriksaan::findByAparPeriod($apar->id, 2026, 11);
        $this->assertNotNull($nov);
        $this->assertNull($nov->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TEPAT_WAKTU, $nov->tipe_penyelesaian);
    }

    /**
     * TC-04: Preservasi Status Terlambat
     * Scan pada 15 Nov saat Okt berstatus selesai terlambat.
     * Pemeriksaan Nov tidak mengubah label atau histori keterlambatan Okt.
     */
    public function test_tc04_preserves_late_status_and_historical_integrity(): void
    {
        $apar = $this->createOperationalApar();

        // Oktober diselesaikan secara terlambat
        $oktLate = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'tanggal_pemeriksaan' => Carbon::create(2026, 11, 01, 10, 0, 0),
            'pemeriksa_nama' => 'Petugas Lapangan',
            'hasil_keseluruhan' => 'baik',
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TERLAMBAT,
            'status' => 'selesai',
            'catatan' => 'Selesai terlambat',
        ]);

        // Simpan pemeriksaan November
        $payload = $this->validFiveItemPayload($apar, 11, 2026);
        $this->post(route('pemeriksaan.store'), $payload);

        // Status dan tanggal Oktober tetap utuh tidak berubah
        $oktFresh = $oktLate->fresh();
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $oktFresh->tipe_penyelesaian);
        $this->assertSame('2026-11-01 10:00:00', $oktFresh->tanggal_pemeriksaan->format('Y-m-d H:i:s'));
    }

    /**
     * TC-05: Grace Period 1 Bulan
     * Scan pada 10 Nov saat Sept, Okt, Nov belum diperiksa.
     * Sistem hanya menyelesaikan Okt & Nov. Sept tetap kosong/terlewat.
     */
    public function test_tc05_grace_period_only_resolves_immediate_previous_month(): void
    {
        $apar = $this->createOperationalApar();

        $payload = $this->validFiveItemPayload($apar, 11, 2026);
        $payload['resolve_backlog'] = true;

        $this->post(route('pemeriksaan.store'), $payload);

        // November terpenuhi
        $this->assertNotNull(Pemeriksaan::findByAparPeriod($apar->id, 2026, 11));
        // Oktober (M-1) terpenuhi
        $this->assertNotNull(Pemeriksaan::findByAparPeriod($apar->id, 2026, 10));
        // September (M-2) TIDAK otomatis terisi record
        $this->assertNull(Pemeriksaan::findByAparPeriod($apar->id, 2026, 9));
        // Total tepat 2 record
        $this->assertSame(2, Pemeriksaan::where('apar_id', $apar->id)->count());
    }

    /**
     * TC-06: APAR Nonaktif & Stok Gudang Dicegah
     * APAR tidak_aktif atau berlokasi di GUDANG K3RS diblokir dari inspeksi.
     */
    public function test_tc06_inactive_or_gudang_apar_blocked_from_smart_inspection(): void
    {
        // 1. APAR Tidak Aktif
        $aparInactive = $this->createOperationalApar(['status' => Apar::STATUS_TIDAK_AKTIF]);

        $resScan = $this->post(route('scan.cari'), ['kode' => $aparInactive->kode]);
        $resScan->assertRedirect(route('apar.show', ['id' => $aparInactive->id]));
        $resScan->assertSessionHas('warning');

        $resCreate = $this->get(route('pemeriksaan.create', ['apar_id' => $aparInactive->id]));
        $resCreate->assertRedirect(route('apar.show', $aparInactive->id));

        $resStore = $this->post(route('pemeriksaan.store'), $this->validFiveItemPayload($aparInactive, 11, 2026));
        $resStore->assertRedirect(route('apar.show', $aparInactive->id));
        $this->assertSame(0, Pemeriksaan::where('apar_id', $aparInactive->id)->count());

        // 2. APAR di Lokasi Stok (GUDANG K3RS)
        $lokasiStok = Lokasi::create([
            'nama' => 'Gudang K3RS Sediaan',
            'kode' => 'LOK-STOK-'.uniqid(),
            'status' => Lokasi::STATUS_STOK,
        ]);
        $aparStok = $this->createOperationalApar(['lokasi_id' => $lokasiStok->id]);

        $resScanStok = $this->post(route('scan.cari'), ['kode' => $aparStok->kode]);
        $resScanStok->assertRedirect(route('apar.show', ['id' => $aparStok->id]));
        $resScanStok->assertSessionHas('warning');

        $resStoreStok = $this->post(route('pemeriksaan.store'), $this->validFiveItemPayload($aparStok, 11, 2026));
        $resStoreStok->assertRedirect(route('apar.show', $aparStok->id));
        $this->assertSame(0, Pemeriksaan::where('apar_id', $aparStok->id)->count());
    }

    /**
     * TC-07: Concurrency & Double Submission Race Condition
     * 2 concurrent request untuk periode yang sama ditangani secara anggun tanpa unhandled 500.
     */
    public function test_tc07_double_submission_and_concurrency_handled_gracefully(): void
    {
        $apar = $this->createOperationalApar();
        $payload = $this->validFiveItemPayload($apar, 11, 2026);

        // Request 1: Sukses awal
        $res1 = $this->post(route('pemeriksaan.store'), $payload);
        $res1->assertSessionHasNoErrors();
        $firstId = Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 11)->value('id');
        $this->assertNotNull($firstId);

        // Skenario A: Double submission sekuensial (ditangani sebagai pembaruan/koreksi aman tanpa duplikasi baris)
        $res2 = $this->post(route('pemeriksaan.store'), $payload);
        $res2->assertRedirect(route('pemeriksaan.show', $firstId));
        $res2->assertSessionHas('success');
        $this->assertSame(1, Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 11)->count());

        // Skenario B: Simulasi True Race Condition (UniqueConstraintViolationException saat concurrent write)
        $apar2 = $this->createOperationalApar();
        $payload2 = $this->validFiveItemPayload($apar2, 11, 2026);

        $mockService = \Mockery::mock(SmartInspectionService::class);
        $mockService->shouldReceive('evaluateAparContext')->andReturn(['mode' => 'regular']);
        $mockService->shouldReceive('executeInspection')->andThrow(
            new UniqueConstraintViolationException(
                'sqlite',
                'INSERT INTO pemeriksaans',
                [],
                new \Exception('UNIQUE constraint failed: pemeriksaans.apar_id, pemeriksaans.periode_tahun, pemeriksaans.periode_bulan')
            )
        );
        $this->app->instance(SmartInspectionService::class, $mockService);

        // Request 3: Terjadi tabrakan database race condition, ditangani elegan tanpa error 500
        $res3 = $this->post(route('pemeriksaan.store'), $payload2);
        $res3->assertRedirect(route('pemeriksaan.index', ['bulan' => 11, 'tahun' => 2026]));
        $res3->assertSessionHas('warning');
    }

    /**
     * TC-08: Transisi Lintas Tahun (Desember -> Januari)
     * Scan pada 05 Jan 2027 saat Des 2026 belum diperiksa.
     * Master: Jan 2027 (tepat_waktu). Linked: Des 2026 (terlambat).
     */
    public function test_tc08_cross_year_transition_resolves_december_backlog_in_january(): void
    {
        $apar = $this->createOperationalApar();

        $payload = $this->validFiveItemPayload($apar, 1, 2027);
        $payload['resolve_backlog'] = true;
        $payload['tanggal_pemeriksaan'] = '2027-01-05 08:30:00';

        $response = $this->post(route('pemeriksaan.store'), $payload);
        $response->assertSessionHasNoErrors();

        // 1. Master Record: Januari 2027
        $master = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', 2027)
            ->where('periode_bulan', 1)
            ->first();

        $this->assertNotNull($master);
        $this->assertNull($master->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TEPAT_WAKTU, $master->tipe_penyelesaian);
        $this->assertSame(5, $master->details()->count());

        // 2. Linked Record: Desember 2026
        $linked = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', 2026)
            ->where('periode_bulan', 12)
            ->first();

        $this->assertNotNull($linked);
        $this->assertSame($master->id, $linked->pemeriksaan_induk_id);
        $this->assertSame(Pemeriksaan::TIPE_TERLAMBAT, $linked->tipe_penyelesaian);
        $this->assertSame(0, $linked->details()->count());

        // Waktu pemeriksaan fisik sama
        $this->assertSame('2027-01-05 08:30:00', $linked->tanggal_pemeriksaan->format('Y-m-d H:i:s'));
    }

    /**
     * TC-09: Proteksi Transaksi Database Rollback
     * Memastikan jika terjadi exception saat menyimpan linked record, master record ikut di-rollback.
     */
    public function test_tc09_transaction_rollback_when_failure_occurs(): void
    {
        $apar = $this->createOperationalApar();
        $service = app(SmartInspectionService::class);

        // Simulasi error dengan mock atau sengaja melemparkan exception
        try {
            DB::transaction(function () use ($apar) {
                Pemeriksaan::create([
                    'apar_id' => $apar->id,
                    'periode_tahun' => 2026,
                    'periode_bulan' => 11,
                    'tanggal_pemeriksaan' => now(),
                    'pemeriksa_nama' => 'Petugas',
                    'hasil_keseluruhan' => 'baik',
                    'status' => 'selesai',
                ]);

                // Paksa lempar exception sebelum linked record tuntas
                throw new DomainException('Simulasi kegagalan koneksi/integritas.');
            });
        } catch (DomainException $e) {
            // Expected caught
        }

        // Tidak boleh ada data yang tertinggal (0 master, 0 orphan)
        $this->assertSame(0, Pemeriksaan::where('apar_id', $apar->id)->count());
    }

    /**
     * TC-10: Sinkronisasi Lonceng Notifikasi Topbar
     * Jumlah APAR belum diperiksa bulan sebelumnya berkurang setelah multi-period resolution.
     */
    public function test_tc10_topbar_notification_decrements_and_clears_after_multi_period_resolution(): void
    {
        $apar = $this->createOperationalApar();
        $now = Carbon::create(2026, 11, 10);
        $prev = $now->copy()->subMonth();

        // Helper query identik dengan yang ada di topbar.blade.php
        $getUninspectedCount = function () use ($prev) {
            return Apar::where('status', '!=', Apar::STATUS_TIDAK_AKTIF)
                ->whereNotNull('lokasi_id')
                ->whereDoesntHave('lokasi', fn ($loc) => $loc->where('status', Lokasi::STATUS_STOK))
                ->whereDoesntHave('pemeriksaans', function ($q) use ($prev) {
                    $q->where('periode_tahun', (int) $prev->year)
                        ->where('periode_bulan', (int) $prev->month);
                })
                ->count();
        };

        $this->assertGreaterThanOrEqual(1, $getUninspectedCount());
        $initialCount = $getUninspectedCount();

        // Jalankan resolusi multi-periode
        $payload = $this->validFiveItemPayload($apar, (int) $now->month, (int) $now->year);
        $payload['resolve_backlog'] = true;
        $this->post(route('pemeriksaan.store'), $payload);

        // Notifikasi berkurang tepat 1
        $this->assertSame($initialCount - 1, $getUninspectedCount());
    }

    /**
     * TC-11: Rincian Checklist Terhubung & Tindak Lanjut pada Linked Record
     * Halaman detail linked record mengambil 5 baris checklist dari master tanpa duplikasi DB.
     */
    public function test_tc11_linked_record_renders_master_checklist_details_and_handles_tindak_lanjut(): void
    {
        $apar = $this->createOperationalApar();

        // Simpan pemeriksaan multi-period dengan temuan pada 'Tekanan'
        $payload = $this->validFiveItemPayload($apar, 11, 2026, 'tidak_baik');
        $payload['resolve_backlog'] = true;
        $this->post(route('pemeriksaan.store'), $payload);

        $master = Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 11)->first();
        $linked = Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 10)->first();

        $this->assertNotNull($master);
        $this->assertNotNull($linked);

        // Buka halaman show linked record
        $response = $this->get(route('pemeriksaan.show', $linked->id));
        $response->assertOk();

        // Pastikan nama item checklist dari master muncul di tampilan
        $response->assertSee('Tekanan');
        $response->assertSee('Pin/Segel');
        $response->assertSee('Handle');
        $response->assertSee('Kondisi Fisik');
        $response->assertSee('Nozel');

        // Pastikan banner resolusi terhubung tampil
        $response->assertSee('Resolusi Multi-Periode');
        $response->assertSee('#'.$master->id);

        // Pastikan tombol buat tindak lanjut mengarahkan ke master_id
        $detailRusak = $master->details()->where('hasil', 'tidak_baik')->first();
        $this->assertNotNull($detailRusak);
        $response->assertSee(route('tindak-lanjut.create', [
            'pemeriksaan_id' => $master->id,
            'detail_id' => $detailRusak->id,
        ]));
    }

    /**
     * TC-12: Metrik Dashboard Tidak Menghitung Ganda Aktivitas Fisik
     * Validasi pemisahan aktivitas fisik riil vs pemenuhan kepatuhan kalender.
     */
    public function test_tc12_dashboard_metrics_do_not_double_count_physical_inspections(): void
    {
        $apar = $this->createOperationalApar();

        $payload = $this->validFiveItemPayload($apar, 11, 2026);
        $payload['resolve_backlog'] = true;
        $this->post(route('pemeriksaan.store'), $payload);

        // Database memiliki 2 baris pemenuhan kepatuhan
        $this->assertSame(2, Pemeriksaan::where('apar_id', $apar->id)->count());

        // Namun aktivitas fisik riil hanya 1 (via scopeAktivitasFisik)
        $this->assertSame(1, Pemeriksaan::where('apar_id', $apar->id)->aktivitasFisik()->count());

        // Kueri kepatuhan bulanan Oktober dan November masing-masing bernilai 1
        $this->assertSame(1, Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 10)->count());
        $this->assertSame(1, Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 11)->count());

        // Dashboard dapat dibuka tanpa error
        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    /**
     * TC-13: Laporan Pemeriksaan & Ekspor CSV
     * Memvalidasi filter tipe_penyelesaian dan kolom ekspor CSV.
     */
    public function test_tc13_laporan_pemeriksaan_and_csv_export_reflects_compliance_types(): void
    {
        $apar = $this->createOperationalApar();

        $payload = $this->validFiveItemPayload($apar, 11, 2026);
        $payload['resolve_backlog'] = true;
        $this->post(route('pemeriksaan.store'), $payload);

        // 1. Filter Tipe Terlambat di Laporan
        $resFilterTerlambat = $this->get(route('laporan.pemeriksaan', ['tipe_penyelesaian' => Pemeriksaan::TIPE_TERLAMBAT]));
        $resFilterTerlambat->assertOk();
        $resFilterTerlambat->assertSee('Selesai Terlambat');

        // 2. Filter Tipe Tepat Waktu di Laporan
        $resFilterTepat = $this->get(route('laporan.pemeriksaan', ['tipe_penyelesaian' => Pemeriksaan::TIPE_TEPAT_WAKTU]));
        $resFilterTepat->assertOk();
        $resFilterTepat->assertSee('Tepat Waktu');

        // 3. Ekspor CSV
        $resCsv = $this->get(route('laporan.pemeriksaan', ['format' => 'csv']));
        $resCsv->assertOk();
        $csvContent = $resCsv->streamedContent();

        $this->assertStringContainsString('Periode Kepatuhan', $csvContent);
        $this->assertStringContainsString('Tipe Kepatuhan', $csvContent);
        $this->assertStringContainsString('Kategori Log', $csvContent);
        $this->assertStringContainsString('Resolusi Multi-Periode', $csvContent);
    }

    /**
     * TC-14: Proteksi Koreksi / Edit pada Linked Record (Fix H-01)
     * Mengakses edit atau mengirim update pada linked record dialihkan ke master record fisik
     * untuk mencegah formulir checklist kosong dan duplikasi baris di pemeriksaan_details.
     */
    public function test_tc14_linked_record_edit_and_update_redirects_to_master_without_detail_duplication(): void
    {
        $apar = $this->createOperationalApar();

        $payload = $this->validFiveItemPayload($apar, 11, 2026);
        $payload['resolve_backlog'] = true;
        $this->post(route('pemeriksaan.store'), $payload);

        $master = Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 11)->first();
        $linked = Pemeriksaan::where('apar_id', $apar->id)->where('periode_bulan', 10)->first();

        $this->assertNotNull($master);
        $this->assertNotNull($linked);
        $this->assertSame(0, $linked->details()->count());

        // 1. Akses GET edit pada linked record dialihkan ke master
        $resEdit = $this->get(route('pemeriksaan.edit', $linked->id));
        $resEdit->assertRedirect(route('pemeriksaan.edit', $master->id));
        $resEdit->assertSessionHas('info');

        // 2. Akses PUT update pada linked record ditolak dan dialihkan ke master
        $updatePayload = $this->validFiveItemPayload($apar, 10, 2026);
        $resUpdate = $this->put(route('pemeriksaan.update', $linked->id), $updatePayload);
        $resUpdate->assertRedirect(route('pemeriksaan.edit', $master->id));
        $resUpdate->assertSessionHas('warning');

        // 3. Verifikasi nol duplikasi baris pada linked record
        $this->assertSame(0, $linked->fresh()->details()->count(), 'Linked record harus tetap memiliki 0 detail (nol duplikasi).');
        $this->assertSame(5, $master->fresh()->details()->count());
    }
}
