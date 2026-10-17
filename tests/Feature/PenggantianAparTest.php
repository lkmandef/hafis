<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\PenggantianApar;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenggantianAparTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    private function createHierarchy(): array
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-RS-TEST',
            'nama' => 'Gedung Utama Rumah Sakit',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1 Poliklinik',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-POLI-01',
            'nama' => 'Poli Penyakit Dalam',
            'kategori' => 'pelayanan',
        ]);

        $aparLama = Apar::create([
            'kode' => 'APAR-TEST-LAMA-01',
            'nomor_register' => 'REG-RS-OLD-001',
            'qr_code' => 'HAFIS-QR-APARTESTLAMA01-ABC123',
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Dinding barat ruang tunggu',
            'koordinat_x' => 45.5,
            'koordinat_y' => 60.0,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Yamato',
            'tipe_model' => 'Portable',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2023-01-15',
            'tanggal_kadaluarsa' => '2026-01-15',
            'keterangan' => 'Unit lama pengujian',
        ]);

        return [$gedung, $lantai, $ruangan, $aparLama];
    }

    /**
     * Test A: Penggantian Normal
     * APAR aktif -> Ganti APAR -> isi APAR baru -> simpan.
     * Pastikan APAR baru dibuat, APAR lama tetap ada, APAR lama menjadi tidak aktif, replacement tercatat.
     */
    public function test_a_replacement_normal(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        // 1. Buka form penggantian
        $responseCreate = $this->get(route('apar.ganti', $aparLama->id));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee($aparLama->kode);
        $responseCreate->assertSee('Informasi Unit APAR Lama');
        $responseCreate->assertSee('Data Unit APAR Baru');

        // 2. Submit penggantian
        $payload = [
            'kode' => 'APAR-TEST-BARU-01',
            'nomor_register' => 'REG-RS-NEW-001',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Dinding barat ruang tunggu (posisi sama)',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Servvo',
            'tipe_model' => 'Portable',
            'tanggal_pemasangan' => '2026-10-03',
            'tanggal_kadaluarsa' => '2029-10-03',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'alasan_keterangan' => null,
            'catatan' => 'Tabung lama mengalami korosi pada seal katup',
            'petugas_nama' => 'Ahmad Teknisi K3RS',
            'salin_titik_denah' => 1,
        ];

        $this->actingAsAdmin(['name' => 'Ahmad Teknisi K3RS']);
        $responseStore = $this->post(route('apar.ganti.store', $aparLama->id), $payload);

        // Cari unit baru di database
        $aparBaru = Apar::where('kode', 'APAR-TEST-BARU-01')->first();
        $this->assertNotNull($aparBaru);
        $responseStore->assertRedirect(route('apar.show', $aparBaru->id));

        // APAR lama tetap ada di database (TIDAK BOLEH DIDELETE)
        $this->assertDatabaseHas('apars', [
            'id' => $aparLama->id,
            'kode' => 'APAR-TEST-LAMA-01',
            'status' => Apar::STATUS_TIDAK_AKTIF,
            'alasan_nonaktif' => 'sudah_diganti',
            'replaced_by_apar_id' => $aparBaru->id,
        ]);

        // APAR baru berstatus aktif & mereferensikan APAR lama
        $this->assertEquals(Apar::STATUS_BAIK, $aparBaru->status);
        $this->assertEquals($aparLama->id, $aparBaru->replaces_apar_id);
        $this->assertTrue($aparBaru->is_replacement);

        // Record replacement tercatat di tabel penggantian_apars
        $this->assertDatabaseHas('penggantian_apars', [
            'apar_lama_id' => $aparLama->id,
            'apar_baru_id' => $aparBaru->id,
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'petugas_nama' => 'Ahmad Teknisi K3RS',
        ]);
    }

    /**
     * Test B: Histori Lama Tetap Utuh
     * Buka APAR lama, pastikan pemeriksaan, checklist, dan maintenance lama tetap ada.
     */
    public function test_b_histori_lama_tetap_ada(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        // Buat pemeriksaan historis untuk APAR lama
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $aparLama->id,
            'tanggal_pemeriksaan' => Carbon::now()->subMonths(2),
            'pemeriksa_nama' => 'Petugas Inspeksi Lama',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
            'catatan' => 'Ditemukan tekanan turun dan korosi',
        ]);

        PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Indikator tekanan / manometer',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'catatan' => 'Jarum di bawah zona hijau',
        ]);

        // Buat tindak lanjut untuk APAR lama
        TindakLanjut::create([
            'apar_id' => $aparLama->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'jenis_tindakan' => 'penggantian_unit',
            'status' => 'selesai',
            'tanggal_tindakan' => Carbon::now()->subDays(5),
            'petugas_pelaksana' => 'Teknisi Logistik',
            'deskripsi_temuan' => 'Tabung bocor mikro',
            'uraian_tindakan' => 'Diusulkan pergantian unit total',
        ]);

        // Eksekusi penggantian
        $payload = [
            'kode' => 'APAR-PENGGANTI-02',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_HASIL_PEMERIKSAAN,
            'petugas_nama' => 'Ahmad Teknisi K3RS',
        ];
        $this->post(route('apar.ganti.store', $aparLama->id), $payload);

        // Buka detail APAR lama
        $responseShow = $this->get(route('apar.show', $aparLama->id));
        $responseShow->assertStatus(200);

        // Pastikan catatan pemeriksaan lama tetap tampil
        $responseShow->assertSee('Ditemukan tekanan turun dan korosi');
        $responseShow->assertSee('Petugas Inspeksi Lama');

        // Pastikan catatan tindak lanjut lama tetap tampil
        $responseShow->assertSee('Tabung bocor mikro');
        $responseShow->assertSee('Teknisi Logistik');

        // Pastikan banner status penggantian tampil
        $responseShow->assertSee('Unit APAR Ini Sudah Tidak Aktif (Sudah Diganti)');
        $responseShow->assertSee('APAR-PENGGANTI-02');
    }

    /**
     * Test C: APAR Baru Bersih & Memiliki QR Sendiri
     * Buka APAR baru, pastikan QR baru tersedia, status aktif, lokasi benar,
     * dan tidak membawa histori pemeriksaan APAR lama.
     */
    public function test_c_apar_baru_bersih_dan_memiliki_qr_sendiri(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        // Buat pemeriksaan untuk APAR lama
        Pemeriksaan::create([
            'apar_id' => $aparLama->id,
            'tanggal_pemeriksaan' => Carbon::now()->subMonths(1),
            'pemeriksa_nama' => 'Pemeriksa Lama',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
            'catatan' => 'Catatan Rahasia Inspeksi Lama',
        ]);

        // Eksekusi penggantian
        $payload = [
            'kode' => 'APAR-UNIT-BARU-03',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_PENGGANTIAN_UNIT,
            'petugas_nama' => 'Petugas K3RS',
        ];
        $this->post(route('apar.ganti.store', $aparLama->id), $payload);

        $aparBaru = Apar::where('kode', 'APAR-UNIT-BARU-03')->first();
        $this->assertNotNull($aparBaru);

        // Pastikan QR code unik baru
        $this->assertNotEquals($aparLama->qr_code, $aparBaru->qr_code);
        $this->assertStringContainsString('HAFIS-QR-', $aparBaru->qr_code);

        // Pastikan status aktif dan lokasi benar
        $this->assertEquals(Apar::STATUS_BAIK, $aparBaru->status);
        $this->assertEquals($ruangan->id, $aparBaru->ruangan_id);

        // Pastikan histori pemeriksaan baru masih 0 (TIDAK MEMBAWA HISTORI LAMA)
        $this->assertEquals(0, $aparBaru->pemeriksaans()->count());

        // Buka detail APAR baru
        $response = $this->get(route('apar.show', $aparBaru->id));
        $response->assertStatus(200);
        $response->assertDontSee('Catatan Rahasia Inspeksi Lama');
        $response->assertSee('Unit APAR Pengganti Aktif');
        $response->assertSee($aparLama->kode);
    }

    /**
     * Test D: Scan QR Lama Membuka APAR Lama
     * Pastikan QR lama membuka APAR lama, status tidak aktif/sudah diganti, dan ada link APAR pengganti.
     */
    public function test_d_scan_qr_lama_membuka_apar_lama_dengan_warning_pengganti(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        $payload = [
            'kode' => 'APAR-BARU-SCAN-04',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_KADALUARSA,
            'petugas_nama' => 'Petugas K3RS',
        ];
        $this->post(route('apar.ganti.store', $aparLama->id), $payload);

        // Scan menggunakan QR Code APAR LAMA
        $responseScan = $this->get(route('scan.cari', ['kode' => $aparLama->qr_code]));
        // Redirect ke Detail APAR LAMA (bukan diam-diam diarahkan ke baru)
        $responseScan->assertRedirect(route('apar.show', $aparLama->id));

        $followed = $this->followRedirects($responseScan);
        $followed->assertSee('Unit APAR Ini Sudah Tidak Aktif (Sudah Diganti)');
        $followed->assertSee('APAR-BARU-SCAN-04');
        $followed->assertSee('Lihat APAR Pengganti');
    }

    /**
     * Test E: Scan QR Baru Membuka APAR Baru
     */
    public function test_e_scan_qr_baru_membuka_apar_baru(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        $payload = [
            'kode' => 'APAR-BARU-SCAN-05',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_KADALUARSA,
            'petugas_nama' => 'Petugas K3RS',
        ];
        $this->post(route('apar.ganti.store', $aparLama->id), $payload);
        $aparBaru = Apar::where('kode', 'APAR-BARU-SCAN-05')->first();

        // Scan QR APAR baru
        $responseScan = $this->get(route('scan.cari', ['kode' => $aparBaru->qr_code]));
        $responseScan->assertRedirect(route('apar.show', $aparBaru->id));

        $followed = $this->followRedirects($responseScan);
        $followed->assertSee('Unit APAR Pengganti Aktif');
        $followed->assertSee('APAR-BARU-SCAN-05');
    }

    /**
     * Test F: Duplicate Replacement Ditolak
     * Coba mengganti APAR lama yang sudah pernah diganti. Harus ditolak.
     */
    public function test_f_duplicate_replacement_ditolak(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        $payload = [
            'kode' => 'APAR-BARU-06A',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'petugas_nama' => 'Petugas K3RS',
        ];
        $this->post(route('apar.ganti.store', $aparLama->id), $payload);

        // Coba ganti lagi APAR lama yang sudah diganti
        $payloadSecond = [
            'kode' => 'APAR-BARU-06B',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'petugas_nama' => 'Petugas K3RS',
        ];

        // 1. Akses halaman form harus ditolak
        $responseGet = $this->get(route('apar.ganti', $aparLama->id));
        $responseGet->assertRedirect(route('apar.show', $aparLama->id));

        // 2. Akses store harus ditolak
        $responsePost = $this->post(route('apar.ganti.store', $aparLama->id), $payloadSecond);
        $responsePost->assertRedirect(route('apar.show', $aparLama->id));

        // Pastikan APAR-BARU-06B tidak dibuat
        $this->assertDatabaseMissing('apars', ['kode' => 'APAR-BARU-06B']);
        // Jumlah replacement untuk aparLama tetap 1
        $this->assertEquals(1, PenggantianApar::where('apar_lama_id', $aparLama->id)->count());
    }

    /**
     * Test G: Kode Duplikat Ditolak
     */
    public function test_g_kode_duplikat_ditolak(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        // Coba gunakan kode yang sama dengan APAR lama
        $payloadSameCode = [
            'kode' => $aparLama->kode,
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'petugas_nama' => 'Petugas K3RS',
        ];

        $response = $this->post(route('apar.ganti.store', $aparLama->id), $payloadSameCode);
        $response->assertSessionHasErrors('kode');

        // APAR lama tetap berstatus Baik
        $aparLama->refresh();
        $this->assertEquals(Apar::STATUS_BAIK, $aparLama->status);
        $this->assertNull($aparLama->replaced_by_apar_id);
    }

    /**
     * Test H: Atomic Database Transaction
     * Validasi gagal jika field penting tidak valid dan transaksi tidak meninggalkan data parsial.
     */
    public function test_h_transaction_atomicity(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        // Kirim alasan invalid yang menyebabkan validasi gagal
        $payloadInvalid = [
            'kode' => 'APAR-NEW-FAIL-01',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => 'alasan_invalid_palsu',
            'petugas_nama' => 'Petugas K3RS',
        ];

        $response = $this->post(route('apar.ganti.store', $aparLama->id), $payloadInvalid);
        $response->assertSessionHasErrors('alasan');

        // Pastikan APAR baru tidak pernah terbuat
        $this->assertDatabaseMissing('apars', ['kode' => 'APAR-NEW-FAIL-01']);
        // Pastikan APAR lama tetap Aktif/Baik
        $aparLama->refresh();
        $this->assertEquals(Apar::STATUS_BAIK, $aparLama->status);
        $this->assertNull($aparLama->replaced_by_apar_id);
    }

    /**
     * Test I: Denah & Titik APAR Terkelola Aman
     * Pastikan penyalinan koordinat denah ke APAR baru berhasil dan koordinat aktif APAR lama dibersihkan.
     */
    public function test_i_denah_titik_terkelola_aman(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();
        $this->assertEquals(45.5, $aparLama->koordinat_x);
        $this->assertEquals(60.0, $aparLama->koordinat_y);

        $payload = [
            'kode' => 'APAR-DENAH-NEW-01',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_PENGGANTIAN_UNIT,
            'petugas_nama' => 'Petugas K3RS',
            'salin_titik_denah' => 1,
        ];

        $this->post(route('apar.ganti.store', $aparLama->id), $payload);

        $aparBaru = Apar::where('kode', 'APAR-DENAH-NEW-01')->first();
        $aparLama->refresh();

        // APAR baru mewarisi titik koordinat
        $this->assertEquals(45.5, $aparBaru->koordinat_x);
        $this->assertEquals(60.0, $aparBaru->koordinat_y);

        // APAR lama dikosongkan titiknya agar tidak ada dua unit aktif di satu titik fisik
        $this->assertNull($aparLama->koordinat_x);
        $this->assertNull($aparLama->koordinat_y);
    }

    /**
     * Test J: Regression Test & Indeks Riwayat Penggantian
     */
    public function test_j_regression_penggantian_index_and_detail(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        $payload = [
            'kode' => 'APAR-REGRESS-01',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'tanggal_penggantian' => '2026-10-03',
            'alasan' => PenggantianApar::ALASAN_LAINNYA,
            'alasan_keterangan' => 'Proyek peningkatan proteksi gedung',
            'petugas_nama' => 'Koordinator K3RS',
        ];

        $this->actingAsAdmin(['name' => 'Koordinator K3RS']);
        $this->post(route('apar.ganti.store', $aparLama->id), $payload);
        $penggantian = PenggantianApar::first();

        // 1. Indeks Riwayat Penggantian
        $responseIndex = $this->get(route('penggantian.index'));
        $responseIndex->assertStatus(200);
        $responseIndex->assertSee($aparLama->kode);
        $responseIndex->assertSee('APAR-REGRESS-01');
        $responseIndex->assertSee('Koordinator K3RS');

        // 2. Detail Penggantian
        $responseShow = $this->get(route('penggantian.show', $penggantian->id));
        $responseShow->assertStatus(200);
        $responseShow->assertSee($aparLama->kode);
        $responseShow->assertSee('APAR-REGRESS-01');
        $responseShow->assertSee('Proyek peningkatan proteksi gedung');

        // 3. Filter Master APAR (Section 20)
        $respAktif = $this->get(route('apar.index', ['status' => 'aktif']));
        $respAktif->assertStatus(200);
        $respAktif->assertSee('APAR-REGRESS-01');
        $respAktif->assertSee('Unit Aktif');

        $respSudahDiganti = $this->get(route('apar.index', ['status' => 'sudah_diganti']));
        $respSudahDiganti->assertStatus(200);
        $respSudahDiganti->assertSee($aparLama->kode);
        $respSudahDiganti->assertSee('Sudah Diganti');
    }
}
