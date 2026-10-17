<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\PenggantianApar;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanLokasiFilterTest extends TestCase
{
    use RefreshDatabase;

    private Ruangan $fallbackRuangan;

    private Lokasi $lokasiA;

    private Lokasi $lokasiB;

    private Lokasi $lokasiC;

    private Apar $apar1;

    private Apar $apar2;

    private Apar $apar3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();

        // 1. Fallback legacy hierarchy (mirrors production seeder where all APAR point to fallback ruangan_id = 1)
        $gedung = Gedung::create([
            'kode' => 'GDG-UTAMA',
            'nama' => 'Gedung Utama',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $this->fallbackRuangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-IGD',
            'nama' => 'Ruang IGD',
            'kategori' => 'pelayanan',
        ]);

        // 2. Master Lokasi K3RS
        $this->lokasiA = Lokasi::create([
            'nama' => 'Alexandri 1 (Penyakit Dalam)',
            'kode' => 'LOK-ALX-01',
            'kategori' => 'rawat_inap',
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $this->lokasiB = Lokasi::create([
            'nama' => 'ICU Sentral Bedah',
            'kode' => 'LOK-ICU-01',
            'kategori' => 'kritis',
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $this->lokasiC = Lokasi::create([
            'nama' => 'Instalasi Farmasi Sentral',
            'kode' => 'LOK-FAR-01',
            'kategori' => 'penunjang',
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        // 3. APAR units (all sharing same fallback ruangan_id, but each having its own real lokasi_id)
        $this->apar1 = Apar::create([
            'kode' => 'APAR-ALX-001',
            'nomor_register' => 'REG-ALX-01',
            'qr_code' => 'HAFIS-QR-ALX01',
            'ruangan_id' => $this->fallbackRuangan->id,
            'lokasi_id' => $this->lokasiA->id,
            'posisi_detail' => 'Dekat Nurse Station Alexandri 1',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Yamato',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2024-01-01',
            'tanggal_kadaluarsa' => '2027-01-01',
        ]);

        $this->apar2 = Apar::create([
            'kode' => 'APAR-ICU-001',
            'nomor_register' => 'REG-ICU-01',
            'qr_code' => 'HAFIS-QR-ICU01',
            'ruangan_id' => $this->fallbackRuangan->id,
            'lokasi_id' => $this->lokasiB->id,
            'posisi_detail' => 'Pintu Masuk Koridor ICU',
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'merek' => 'Chubb',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2024-02-01',
            'tanggal_kadaluarsa' => '2027-02-01',
        ]);

        $this->apar3 = Apar::create([
            'kode' => 'APAR-FAR-001',
            'nomor_register' => 'REG-FAR-01',
            'qr_code' => 'HAFIS-QR-FAR01',
            'ruangan_id' => $this->fallbackRuangan->id,
            'lokasi_id' => $this->lokasiC->id,
            'posisi_detail' => 'Ruang Racik Farmasi',
            'jenis_media' => 'Clean Agent',
            'kapasitas' => '3 kg',
            'merek' => 'Servvo',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_pemasangan' => '2024-03-01',
            'tanggal_kadaluarsa' => '2027-03-01',
        ]);
    }

    /**
     * Uji 1 & 6: Master APAR (apar.index) memakai filter lokasi_id K3RS dan reset bersih
     */
    public function test_master_apar_index_filters_by_lokasi_k3rs(): void
    {
        // Tanpa filter lokasi: semua unit aktif muncul
        $resDefault = $this->get(route('apar.index', ['status' => 'semua']));
        $resDefault->assertStatus(200);
        $resDefault->assertSee($this->apar1->kode);
        $resDefault->assertSee($this->apar2->kode);
        $resDefault->assertSee($this->apar3->kode);
        $resDefault->assertSee('Semua Lokasi K3RS');

        // Filter lokasi A
        $resLokasiA = $this->get(route('apar.index', ['lokasi_id' => $this->lokasiA->id, 'status' => 'semua']));
        $resLokasiA->assertStatus(200);
        $resLokasiA->assertSee($this->apar1->kode);
        $resLokasiA->assertDontSee($this->apar2->kode);
        $resLokasiA->assertDontSee($this->apar3->kode);

        // Filter teks + lokasi bersamaan
        $resSearchMatch = $this->get(route('apar.index', [
            'lokasi_id' => $this->lokasiA->id,
            'q' => 'ALX',
            'status' => 'semua',
        ]));
        $resSearchMatch->assertStatus(200);
        $resSearchMatch->assertSee($this->apar1->kode);

        // Filter teks tidak cocok pada lokasi yang dipilih
        $resSearchMismatch = $this->get(route('apar.index', [
            'lokasi_id' => $this->lokasiA->id,
            'q' => 'ICU',
            'status' => 'semua',
        ]));
        $resSearchMismatch->assertStatus(200);
        $resSearchMismatch->assertDontSee($this->apar1->kode);
        $resSearchMismatch->assertDontSee($this->apar2->kode);

        // Tombol reset tersedia saat filter lokasi aktif
        $resLokasiA->assertSee('Reset');
    }

    /**
     * Uji 2 & 5: Laporan 1 (Data APAR) konsisten dengan lokasi K3RS, CSV, dan Print
     */
    public function test_laporan_1_apar_filters_by_lokasi_k3rs_and_supports_csv_and_print(): void
    {
        // 1. Web View terfilter lokasi B (ICU)
        $response = $this->get(route('laporan.apar', ['lokasi_id' => $this->lokasiB->id]));
        $response->assertStatus(200);
        $response->assertSee($this->apar2->kode);
        $response->assertDontSee($this->apar1->kode);
        $response->assertDontSee($this->apar3->kode);
        $response->assertSee('ICU Sentral Bedah');
        $response->assertSee('Lokasi K3RS');

        // Rekap terfilter menunjukkan 1 unit dari 3 total
        $response->assertSee('Menampilkan <strong class="text-slate-800 font-semibold">1</strong> unit', false);

        // 2. CSV Export menghormati lokasi_id
        $csvResponse = $this->get(route('laporan.apar', [
            'lokasi_id' => $this->lokasiB->id,
            'format' => 'csv',
        ]));
        $csvResponse->assertStatus(200);
        $csvContent = $csvResponse->streamedContent();
        $this->assertStringContainsString($this->apar2->kode, $csvContent);
        $this->assertStringNotContainsString($this->apar1->kode, $csvContent);

        // 3. Print View menyertakan kop metadata Lokasi K3RS
        $printResponse = $this->get(route('laporan.apar', [
            'lokasi_id' => $this->lokasiB->id,
            'format' => 'print',
        ]));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('ICU Sentral Bedah');
        $printResponse->assertSee($this->apar2->kode);
        $printResponse->assertDontSee($this->apar1->kode);
    }

    /**
     * Uji 7: Laporan 2 (Pemeriksaan) memfilter lokasi melalui relasi apar.lokasi_id
     */
    public function test_laporan_2_pemeriksaan_filters_by_lokasi_k3rs(): void
    {
        $jadwal = Jadwal::create([
            'nama' => 'Inspeksi Maret 2026',
            'tanggal_mulai' => '2026-03-01',
            'tanggal_selesai' => '2026-03-31',
            'status' => 'aktif',
        ]);

        $pem1 = Pemeriksaan::create([
            'apar_id' => $this->apar1->id,
            'jadwal_id' => $jadwal->id,
            'pemeriksa_nama' => 'Petugas Anton',
            'tanggal_pemeriksaan' => Carbon::parse('2026-03-10 09:00:00'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'catatan' => 'Pemeriksaan Alexandri aman',
            'status' => 'selesai',
        ]);

        $pem2 = Pemeriksaan::create([
            'apar_id' => $this->apar2->id,
            'jadwal_id' => $jadwal->id,
            'pemeriksa_nama' => 'Petugas Budi',
            'tanggal_pemeriksaan' => Carbon::parse('2026-03-11 10:00:00'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'catatan' => 'Pemeriksaan ICU perlu tindakan',
            'status' => 'selesai',
        ]);

        // Filter lokasi A (Alexandri)
        $resA = $this->get(route('laporan.pemeriksaan', ['lokasi_id' => $this->lokasiA->id]));
        $resA->assertStatus(200);
        $resA->assertSee($this->apar1->kode);
        $resA->assertSee('Petugas Anton');
        $resA->assertDontSee($this->apar2->kode);
        $resA->assertDontSee('Petugas Budi');

        // Filter lokasi B (ICU)
        $resB = $this->get(route('laporan.pemeriksaan', ['lokasi_id' => $this->lokasiB->id]));
        $resB->assertStatus(200);
        $resB->assertSee($this->apar2->kode);
        $resB->assertSee('Petugas Budi');
        $resB->assertDontSee($this->apar1->kode);
    }

    /**
     * Uji 7 & 10: Laporan 3 (Temuan) memfilter lokasi via pemeriksaan.apar.lokasi_id dan status_tl konsisten
     */
    public function test_laporan_3_temuan_filters_by_lokasi_k3rs_and_status_tl_consistency(): void
    {
        $pem1 = Pemeriksaan::create([
            'apar_id' => $this->apar1->id,
            'pemeriksa_nama' => 'Petugas Anton',
            'tanggal_pemeriksaan' => Carbon::parse('2026-03-10'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        $pem2 = Pemeriksaan::create([
            'apar_id' => $this->apar2->id,
            'pemeriksa_nama' => 'Petugas Budi',
            'tanggal_pemeriksaan' => Carbon::parse('2026-03-11'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        $detail1 = PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem1->id,
            'item_nama' => 'Selang & Nozzle',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'catatan' => 'Selang retak di Alexandri',
        ]);

        $detail2 = PemeriksaanDetail::create([
            'pemeriksaan_id' => $pem2->id,
            'item_nama' => 'Manometer Tekanan',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'catatan' => 'Jarum turun di ICU',
        ]);

        // Detail 1 diberi tindak lanjut SELESAI
        TindakLanjut::create([
            'apar_id' => $this->apar1->id,
            'pemeriksaan_detail_id' => $detail1->id,
            'tanggal_tindakan' => Carbon::parse('2026-03-12'),
            'deskripsi_temuan' => 'Selang retak',
            'jenis_tindakan' => TindakLanjut::JENIS_PENGGANTIAN_KOMPONEN,
            'uraian_tindakan' => 'Ganti selang baru',
            'petugas_pelaksana' => 'Teknisi K3',
            'status' => TindakLanjut::STATUS_SELESAI,
        ]);

        // Detail 2 TIDAK memiliki tiket tindak lanjut (belum_ada_tl / belum_ditindaklanjuti)

        // 1. Filter lokasi A
        $resLokasi = $this->get(route('laporan.temuan', ['lokasi_id' => $this->lokasiA->id]));
        $resLokasi->assertStatus(200);
        $resLokasi->assertSee('Selang retak di Alexandri');
        $resLokasi->assertDontSee('Jarum turun di ICU');

        // 2. Konsistensi status_tl: 'belum_ditindaklanjuti' harus mencakup temuan tanpa tiket
        $resBelumTl = $this->get(route('laporan.temuan', ['status_tl' => 'belum_ditindaklanjuti']));
        $resBelumTl->assertStatus(200);
        $resBelumTl->assertSee('Jarum turun di ICU');
        $resBelumTl->assertDontSee('Selang retak di Alexandri');

        // 3. Filter 'belum_ada_tl' juga menemukan temuan tanpa tiket
        $resBelumAdaTl = $this->get(route('laporan.temuan', ['status_tl' => 'belum_ada_tl']));
        $resBelumAdaTl->assertStatus(200);
        $resBelumAdaTl->assertSee('Jarum turun di ICU');
    }

    /**
     * Uji 7: Laporan 4 (Tindak Lanjut) memfilter lokasi via apar.lokasi_id
     */
    public function test_laporan_4_tindak_lanjut_filters_by_lokasi_k3rs(): void
    {
        $tl1 = TindakLanjut::create([
            'apar_id' => $this->apar1->id,
            'tanggal_tindakan' => Carbon::parse('2026-03-12'),
            'deskripsi_temuan' => 'Pin segel putus di Alexandri',
            'jenis_tindakan' => TindakLanjut::JENIS_PERBAIKAN,
            'uraian_tindakan' => 'Pasang pin segel baru',
            'petugas_pelaksana' => 'Teknisi Dani',
            'status' => TindakLanjut::STATUS_SELESAI,
        ]);

        $tl2 = TindakLanjut::create([
            'apar_id' => $this->apar2->id,
            'tanggal_tindakan' => Carbon::parse('2026-03-13'),
            'deskripsi_temuan' => 'Tekanan turun di ICU',
            'jenis_tindakan' => TindakLanjut::JENIS_PENGISIAN_ULANG,
            'uraian_tindakan' => 'Refill tabung CO2',
            'petugas_pelaksana' => 'Teknisi Eko',
            'status' => TindakLanjut::STATUS_DALAM_PROSES,
        ]);

        // Filter lokasi B (ICU)
        $resB = $this->get(route('laporan.tindak_lanjut', ['lokasi_id' => $this->lokasiB->id]));
        $resB->assertStatus(200);
        $resB->assertSee('Refill tabung CO2');
        $resB->assertSee('Teknisi Eko');
        $resB->assertDontSee('Pasang pin segel baru');
        $resB->assertDontSee('Teknisi Dani');
    }

    /**
     * Uji 7: Laporan 5 (Penggantian) memfilter lokasi via aparBaru ATAU aparLama
     */
    public function test_laporan_5_penggantian_filters_by_lokasi_k3rs_and_supports_landscape(): void
    {
        $aparBaru = Apar::create([
            'kode' => 'APAR-ALX-001-NEW',
            'nomor_register' => 'REG-ALX-NEW',
            'qr_code' => 'HAFIS-QR-ALXNEW',
            'ruangan_id' => $this->fallbackRuangan->id,
            'lokasi_id' => $this->lokasiA->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
            'replaces_apar_id' => $this->apar1->id,
        ]);

        $this->apar1->update([
            'status' => Apar::STATUS_TIDAK_AKTIF,
            'replaced_by_apar_id' => $aparBaru->id,
        ]);

        $penggantian1 = PenggantianApar::create([
            'apar_lama_id' => $this->apar1->id,
            'apar_baru_id' => $aparBaru->id,
            'tanggal_penggantian' => Carbon::parse('2026-03-20'),
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'alasan_keterangan' => 'Tabung Alexandri bocor',
            'petugas_nama' => 'Petugas Gugun',
        ]);

        // Filter lokasi A (Alexandri)
        $resA = $this->get(route('laporan.penggantian', ['lokasi_id' => $this->lokasiA->id]));
        $resA->assertStatus(200);
        $resA->assertSee('APAR-ALX-001');
        $resA->assertSee('APAR-ALX-001-NEW');
        $resA->assertSee('Tabung Alexandri bocor');

        // Filter lokasi B (ICU) tidak menampilkan penggantian lokasi A
        $resB = $this->get(route('laporan.penggantian', ['lokasi_id' => $this->lokasiB->id]));
        $resB->assertStatus(200);
        $resB->assertDontSee('APAR-ALX-001-NEW');

        // Cetak penggantian menggunakan orientasi landscape
        $printRes = $this->get(route('laporan.penggantian', [
            'lokasi_id' => $this->lokasiA->id,
            'format' => 'print',
        ]));
        $printRes->assertStatus(200);
        $printRes->assertSee('A4 landscape', false);
    }

    /**
     * Uji 3 & 8: Filter lokasi kosong tidak menyisakan filter lantai legacy dan mengembalikan data utuh
     */
    public function test_clearing_location_filter_does_not_retain_stale_floor_parameters(): void
    {
        // Parameter kosong lokasi_id dan residu lantai_id acak tanpa gedung_id
        $res = $this->get(route('laporan.apar', [
            'lokasi_id' => '',
            'gedung_id' => '',
            'lantai_id' => 99999, // stale legacy floor ID
        ]));

        $res->assertStatus(200);
        // Semua 3 unit APAR harus tetap tampil karena filter lantai tanpa gedung_id diabaikan
        $res->assertSee($this->apar1->kode);
        $res->assertSee($this->apar2->kode);
        $res->assertSee($this->apar3->kode);
    }
}
