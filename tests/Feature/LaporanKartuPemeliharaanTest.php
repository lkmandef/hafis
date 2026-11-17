<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\MasterChecklist;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\PenggantianApar;
use App\Models\User;
use App\Services\KartuPemeliharaanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LaporanKartuPemeliharaanTest extends TestCase
{
    use RefreshDatabase;

    private function createHierarchy(string $kodePrefix = 'TEST'): array
    {
        $gedung = Gedung::create([
            'kode' => "GDG-{$kodePrefix}",
            'nama' => "Gedung {$kodePrefix}",
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $lokasi = Lokasi::create([
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'kode' => "LOK-{$kodePrefix}",
            'nama' => "Ruang {$kodePrefix}",
            'tipe' => Lokasi::TIPE_ROOM,
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $apar = Apar::create([
            'kode' => "APAR-{$kodePrefix}-01",
            'nomor_register' => "REG-{$kodePrefix}-001",
            'qr_code' => "HAFIS-QR-{$kodePrefix}-01",
            'lokasi_id' => $lokasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
            'tanggal_kadaluarsa' => Carbon::now()->addYears(2)->toDateString(),
        ]);

        return compact('gedung', 'lantai', 'lokasi', 'apar');
    }

    public function test_akses_laporan_kartu_pemeliharaan(): void
    {
        $data = $this->createHierarchy('AKSES');

        // 1. Admin mendapat 200 OK
        $this->actingAsAdmin();
        $responseAdmin = $this->get(route('laporan.kartu-pemeliharaan', ['apar_id' => $data['apar']->id]));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('Ceklist Pemeliharaan APAR');

        // 2. Petugas K3RS mendapat 200 OK
        $this->actingAsPetugas();
        $responsePetugas = $this->get(route('laporan.kartu-pemeliharaan', ['apar_id' => $data['apar']->id]));
        $responsePetugas->assertOk();

        // 3. Guest diarahkan ke halaman login
        auth()->logout();
        $responseGuest = $this->get(route('laporan.kartu-pemeliharaan'));
        $responseGuest->assertRedirect(route('login'));

        // 4. Akun nonaktif ditolak
        $inactiveUser = User::factory()->create(['is_active' => false]);
        $this->actingAs($inactiveUser);
        $responseInactive = $this->get(route('laporan.kartu-pemeliharaan'));
        $responseInactive->assertRedirect(route('login'));
    }

    public function test_hub_laporan_menampilkan_kartu_06(): void
    {
        $this->actingAsAdmin();
        $response = $this->get(route('laporan.index'));

        $response->assertOk();
        $response->assertSee('6 Modul Laporan Aktif');
        $response->assertSee('06');
        $response->assertSee('Ceklist Pemeliharaan APAR');
        $response->assertSee('Kartu Riwayat Tahunan');
        $response->assertSee(route('laporan.kartu-pemeliharaan'));
    }

    public function test_mapping_simbol_matriks(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('SIMBOL');
        $apar = $data['apar'];

        // Buat master checklist sederhana
        MasterChecklist::query()->delete();
        MasterChecklist::create(['nama' => 'Tekanan', 'urutan' => 1, 'is_active' => true]);
        MasterChecklist::create(['nama' => 'Pin/Segel', 'urutan' => 2, 'is_active' => true]);
        MasterChecklist::create(['nama' => 'Handle', 'urutan' => 3, 'is_active' => true]);

        // Buat pemeriksaan Januari 2026
        $pem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 1,
            'tanggal_pemeriksaan' => '2026-01-15 10:00:00',
            'pemeriksa_nama' => 'Budi Santoso',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem->id,
            'item_nama' => 'Tekanan',
            'hasil' => PemeriksaanDetail::HASIL_BAIK,
            'urutan' => 1,
        ]);
        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem->id,
            'item_nama' => 'Pin/Segel',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'catatan' => 'Segel putus',
            'urutan' => 2,
        ]);
        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem->id,
            'item_nama' => 'Handle',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_DIPERIKSA,
            'urutan' => 3,
        ]);

        $service = new KartuPemeliharaanService;
        $columns = $service->getChecklistColumns(2026, $apar);
        $matrix = $service->buildAparYearMatrix($apar, 2026, $columns);

        // Verifikasi pemetaan simbol di bulan Januari (bulan 1)
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_BAIK, $matrix[1]['cells']['tekanan']['symbol']);
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_TIDAK_BAIK, $matrix[1]['cells']['pin/segel']['symbol']);
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_TIDAK_DIPERIKSA, $matrix[1]['cells']['handle']['symbol']);

        // Verifikasi bulan Februari (bulan 2) belum diperiksa -> simbol kosong
        $this->assertFalse($matrix[2]['has_pemeriksaan']);
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_KOSONG, $matrix[2]['cells']['tekanan']['symbol']);

        // Verifikasi di web view
        $response = $this->get(route('laporan.kartu-pemeliharaan', ['apar_id' => $apar->id, 'tahun' => 2026]));
        $response->assertOk();
        $response->assertSee('Budi Santoso');
    }

    public function test_fleksibilitas_checklist_kolom_aktif_dan_historis_non_aktif(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('FLEKSI');
        $apar = $data['apar'];

        MasterChecklist::query()->delete();
        MasterChecklist::create(['nama' => 'Tekanan', 'urutan' => 1, 'is_active' => true]);
        MasterChecklist::create(['nama' => 'Nozel', 'urutan' => 2, 'is_active' => true]);

        // Buat pemeriksaan dengan item historis yang tidak ada di master checklist aktif
        $pem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 3,
            'tanggal_pemeriksaan' => '2026-03-10 09:00:00',
            'pemeriksa_nama' => 'Siti Rahma',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem->id,
            'item_nama' => ' tekanan ', // Uji case-insensitive dan trim
            'hasil' => PemeriksaanDetail::HASIL_BAIK,
            'urutan' => 1,
        ]);
        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem->id,
            'item_nama' => 'Selang Jadul', // Item lama tidak aktif
            'hasil' => PemeriksaanDetail::HASIL_BAIK,
            'urutan' => 2,
        ]);

        $service = new KartuPemeliharaanService;
        $columns = $service->getChecklistColumns(2026, $apar);

        // Harus ada 3 kolom: Tekanan (aktif), Nozel (aktif), dan Selang Jadul (non-aktif)
        $this->assertCount(3, $columns);
        $this->assertEquals('Tekanan', $columns[0]['title']);
        $this->assertTrue($columns[0]['is_active']);

        $this->assertEquals('Nozel', $columns[1]['title']);
        $this->assertTrue($columns[1]['is_active']);

        $this->assertEquals('Selang Jadul (non-aktif)', $columns[2]['title']);
        $this->assertFalse($columns[2]['is_active']);

        $matrix = $service->buildAparYearMatrix($apar, 2026, $columns);
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_BAIK, $matrix[3]['cells']['selang jadul']['symbol']);
    }

    public function test_periode_bulan_sebagai_kunci_dan_inspeksi_terlambat(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('TERLAMBAT');
        $apar = $data['apar'];

        MasterChecklist::query()->delete();
        MasterChecklist::create(['nama' => 'Tekanan', 'urutan' => 1, 'is_active' => true]);

        // Inspeksi dilakukan pada 5 Desember untuk periode November (bulan 11) berstatus terlambat
        $pem = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 11,
            'tanggal_pemeriksaan' => '2026-12-05 14:00:00',
            'pemeriksa_nama' => 'Ahmad Fauzi',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'tipe_penyelesaian' => Pemeriksaan::TIPE_TERLAMBAT,
            'status' => 'selesai',
        ]);

        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem->id,
            'item_nama' => 'Tekanan',
            'hasil' => PemeriksaanDetail::HASIL_BAIK,
            'urutan' => 1,
        ]);

        $service = new KartuPemeliharaanService;
        $columns = $service->getChecklistColumns(2026, $apar);
        $matrix = $service->buildAparYearMatrix($apar, 2026, $columns);

        // November (bulan 11) harus terisi dan berstatus terlambat
        $this->assertTrue($matrix[11]['has_pemeriksaan']);
        $this->assertTrue($matrix[11]['is_terlambat']);
        $this->assertEquals('05/12/2026', $matrix[11]['tanggal_pemeriksaan']);
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_BAIK, $matrix[11]['cells']['tekanan']['symbol']);

        // Desember (bulan 12) tetap belum diperiksa
        $this->assertFalse($matrix[12]['has_pemeriksaan']);
    }

    public function test_record_ganda_per_bulan_ambil_terbaru_dan_abaikan_dibatalkan(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('GANDA');
        $apar = $data['apar'];

        MasterChecklist::query()->delete();
        MasterChecklist::create(['nama' => 'Tekanan', 'urutan' => 1, 'is_active' => true]);

        // Simulasi multi-record in-memory (Record 1: Lama, Record 2: Baru/Koreksi, Record 3: Dibatalkan)
        $pem1 = new Pemeriksaan([
            'id' => 1,
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 5,
            'tanggal_pemeriksaan' => Carbon::parse('2026-05-02 08:00:00'),
            'pemeriksa_nama' => 'Petugas Lama',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);
        $pem1->setRelation('details', collect([
            new PemeriksaanDetail(['item_nama' => 'Tekanan', 'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK]),
        ]));

        $pem2 = new Pemeriksaan([
            'id' => 2,
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 5,
            'tanggal_pemeriksaan' => Carbon::parse('2026-05-10 11:00:00'),
            'pemeriksa_nama' => 'Petugas Baru',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);
        $pem2->setRelation('details', collect([
            new PemeriksaanDetail(['item_nama' => 'Tekanan', 'hasil' => PemeriksaanDetail::HASIL_BAIK]),
        ]));

        $pem3 = new Pemeriksaan([
            'id' => 3,
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 5,
            'tanggal_pemeriksaan' => Carbon::parse('2026-05-25 15:00:00'),
            'pemeriksa_nama' => 'Petugas Batal',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'dibatalkan',
        ]);
        $pem3->setRelation('details', collect());

        $apar->setRelation('pemeriksaans', collect([$pem1, $pem2, $pem3]));

        $service = new KartuPemeliharaanService;
        $columns = $service->getChecklistColumns(2026, $apar);
        $matrix = $service->buildAparYearMatrix($apar, 2026, $columns);

        // Harus memilih Petugas Baru (pem2) dan mengabaikan Petugas Lama (pem1) serta Petugas Batal (pem3)
        $this->assertEquals('Petugas Baru', $matrix[5]['pemeriksa_nama']);
        $this->assertEquals(KartuPemeliharaanService::SYMBOL_BAIK, $matrix[5]['cells']['tekanan']['symbol']);
    }

    public function test_kasus_tepi_apar_diganti_stok_soft_deleted_dan_pra_operasional(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('TEPI');
        $apar = $data['apar'];

        $service = new KartuPemeliharaanService;

        // 1. Kasus APAR Diganti
        $aparBaru = Apar::create([
            'kode' => 'APAR-BARU-01',
            'qr_code' => 'QR-BARU-01',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $apar->update([
            'replaced_by_apar_id' => $aparBaru->id,
            'alasan_nonaktif' => 'sudah_diganti',
            'status' => Apar::STATUS_TIDAK_AKTIF,
        ]);

        PenggantianApar::create([
            'apar_lama_id' => $apar->id,
            'apar_baru_id' => $aparBaru->id,
            'tanggal_penggantian' => '2026-10-01',
            'alasan' => PenggantianApar::ALASAN_PENGGANTIAN_UNIT,
            'petugas_nama' => 'Admin K3RS',
        ]);

        $apar->refresh();
        $meta = $service->getAparCardMeta($apar, 2026);
        $this->assertTrue($meta['is_replaced']);
        $this->assertEquals('APAR-BARU-01', $meta['replaced_by_kode']);

        // 2. Kasus APAR Stok
        $lokasiStok = Lokasi::create([
            'kode' => 'LOK-STOK',
            'nama' => 'Gudang Stok K3RS',
            'tipe' => Lokasi::TIPE_WAREHOUSE,
            'status' => Lokasi::STATUS_STOK,
        ]);
        $aparStok = Apar::create([
            'kode' => 'APAR-STOK-01',
            'qr_code' => 'QR-STOK-01',
            'lokasi_id' => $lokasiStok->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => Apar::STATUS_BAIK,
        ]);
        $metaStok = $service->getAparCardMeta($aparStok, 2026);
        $this->assertTrue($metaStok['is_stok']);

        // 3. Kasus Soft-Deleted
        $apar->delete();
        $this->assertTrue($apar->trashed());
        $responseSoftDeleted = $this->get(route('laporan.kartu-pemeliharaan', ['apar_id' => $apar->id]));
        $responseSoftDeleted->assertOk();
        $responseSoftDeleted->assertSee('TERHAPUS / ARSIP');

        // 4. Kasus Pra-Operasional (Tahun sebelum operational_start_year)
        $metaPra = $service->getAparCardMeta($aparBaru, 2024);
        $this->assertTrue($metaPra['is_pra_digitalisasi_year']);
    }

    public function test_ekspor_csv_valid_single_dan_batch(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('CSV');
        $apar = $data['apar'];

        // 1. Ekspor Single APAR
        $responseSingle = $this->get(route('laporan.kartu-pemeliharaan', [
            'apar_id' => $apar->id,
            'tahun' => 2026,
            'format' => 'csv',
        ]));

        $responseSingle->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $responseSingle->headers->get('content-type'));
        $this->assertStringContainsString("kartu-pemeliharaan-{$apar->kode}-2026.csv", $responseSingle->headers->get('content-disposition'));

        // 2. Ekspor Batch per Gedung
        $responseBatch = $this->get(route('laporan.kartu-pemeliharaan', [
            'gedung_id' => $data['gedung']->id,
            'tahun' => 2026,
            'mode' => 'batch',
            'format' => 'csv',
        ]));

        $responseBatch->assertOk();
        $this->assertEquals('text/csv; charset=UTF-8', $responseBatch->headers->get('content-type'));
        $this->assertStringContainsString('rekap-kartu-pemeliharaan-massal-2026.csv', $responseBatch->headers->get('content-disposition'));
    }

    public function test_format_print_memuat_kop_dan_orientasi_dinamis(): void
    {
        $this->actingAsAdmin();
        $data = $this->createHierarchy('PRINT');
        $apar = $data['apar'];

        // Master checklist <= 6 -> Portrait
        MasterChecklist::query()->delete();
        for ($i = 1; $i <= 5; $i++) {
            MasterChecklist::create(['nama' => "Item {$i}", 'urutan' => $i, 'is_active' => true]);
        }

        $responsePortrait = $this->get(route('laporan.kartu-pemeliharaan', [
            'apar_id' => $apar->id,
            'tahun' => 2026,
            'format' => 'print',
        ]));

        $responsePortrait->assertOk();
        $responsePortrait->assertSee('A4 PORTRAIT');
        $responsePortrait->assertSee('KARTU CEKLIST PEMELIHARAAN APAR (TAHUNAN)');

        // Master checklist > 6 -> Landscape
        for ($i = 6; $i <= 8; $i++) {
            MasterChecklist::create(['nama' => "Item {$i}", 'urutan' => $i, 'is_active' => true]);
        }

        $responseLandscape = $this->get(route('laporan.kartu-pemeliharaan', [
            'apar_id' => $apar->id,
            'tahun' => 2026,
            'format' => 'print',
        ]));

        $responseLandscape->assertOk();
        $responseLandscape->assertSee('A4 LANDSCAPE');
    }

    public function test_jumlah_query_konstan_pada_cetak_massal_bebas_n_plus_one(): void
    {
        $this->actingAsAdmin();
        $gedung = Gedung::create(['kode' => 'GDG-N1', 'nama' => 'Gedung N1', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);

        // Buat 2 APAR
        for ($i = 1; $i <= 2; $i++) {
            $lokasi = Lokasi::create([
                'gedung_id' => $gedung->id,
                'lantai_id' => $lantai->id,
                'kode' => "LOK-N1-{$i}",
                'nama' => "Ruang {$i}",
                'tipe' => Lokasi::TIPE_ROOM,
                'status' => Lokasi::STATUS_AKTIF,
            ]);

            $a = Apar::create([
                'kode' => "APAR-N1-0{$i}",
                'qr_code' => "QR-N1-0{$i}",
                'lokasi_id' => $lokasi->id,
                'jenis_media' => 'Dry Chemical Powder',
                'kapasitas' => '6 kg',
                'status' => Apar::STATUS_BAIK,
            ]);

            $p = Pemeriksaan::create([
                'apar_id' => $a->id,
                'periode_tahun' => 2026,
                'periode_bulan' => 1,
                'tanggal_pemeriksaan' => '2026-01-10 10:00:00',
                'pemeriksa_nama' => 'Tester',
                'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
                'status' => 'selesai',
            ]);

            PemeriksaanDetail::create([
                'pemeriksaan_id' => $p->id,
                'item_nama' => 'Tekanan',
                'hasil' => PemeriksaanDetail::HASIL_BAIK,
                'urutan' => 1,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->get(route('laporan.kartu-pemeliharaan', [
            'gedung_id' => $gedung->id,
            'tahun' => 2026,
            'mode' => 'batch',
            'format' => 'print',
        ]));

        $queriesCount2Apars = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Tambah 3 APAR lagi (total 5 APAR)
        for ($i = 3; $i <= 5; $i++) {
            $lokasi = Lokasi::create([
                'gedung_id' => $gedung->id,
                'lantai_id' => $lantai->id,
                'kode' => "LOK-N1-{$i}",
                'nama' => "Ruang {$i}",
                'tipe' => Lokasi::TIPE_ROOM,
                'status' => Lokasi::STATUS_AKTIF,
            ]);

            $a = Apar::create([
                'kode' => "APAR-N1-0{$i}",
                'qr_code' => "QR-N1-0{$i}",
                'lokasi_id' => $lokasi->id,
                'jenis_media' => 'Dry Chemical Powder',
                'kapasitas' => '6 kg',
                'status' => Apar::STATUS_BAIK,
            ]);

            $p = Pemeriksaan::create([
                'apar_id' => $a->id,
                'periode_tahun' => 2026,
                'periode_bulan' => 1,
                'tanggal_pemeriksaan' => '2026-01-10 10:00:00',
                'pemeriksa_nama' => 'Tester',
                'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
                'status' => 'selesai',
            ]);

            PemeriksaanDetail::create([
                'pemeriksaan_id' => $p->id,
                'item_nama' => 'Tekanan',
                'hasil' => PemeriksaanDetail::HASIL_BAIK,
                'urutan' => 1,
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->get(route('laporan.kartu-pemeliharaan', [
            'gedung_id' => $gedung->id,
            'tahun' => 2026,
            'mode' => 'batch',
            'format' => 'print',
        ]));

        $queriesCount5Apars = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Jumlah query tidak boleh bertambah seiring bertambahnya jumlah APAR (Bebas N+1)
        $this->assertEquals($queriesCount2Apars, $queriesCount5Apars);
    }
}
