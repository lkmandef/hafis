<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TindakLanjutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    private function createDummyHierarchy(): array
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-RS-1',
            'nama' => 'Gedung Rawat Inap',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-101',
            'nama' => 'Ruang Perawatan 101',
            'kategori' => 'rawat_inap',
        ]);

        $apar = Apar::create([
            'kode' => 'APAR-TEST-TL-01',
            'nomor_register' => 'REG-TL-001',
            'qr_code' => 'QR-TL-001',
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Samping pintu masuk kamar 101',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 Kg',
            'merek' => 'Yamato',
            'tipe_model' => 'Portable',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_pemasangan' => '2025-01-01',
            'tanggal_kadaluarsa' => '2030-01-01',
        ]);

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Ahmad Teknisi',
            'tanggal_pemeriksaan' => Carbon::now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
            'catatan' => 'Ditemukan selang retak dan manometer drop.',
        ]);

        $detailBaik = PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Posisi APAR sesuai lokasi dan ketinggian',
            'hasil' => PemeriksaanDetail::HASIL_BAIK,
            'urutan' => 0,
        ]);

        $detailRusak = PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Selang (hose) dan nozzle dalam kondisi lentur',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'catatan' => 'Selang mengalami keretakan pada sambungan nozzle',
            'urutan' => 1,
        ]);

        return compact('gedung', 'lantai', 'ruangan', 'apar', 'pemeriksaan', 'detailBaik', 'detailRusak');
    }

    /**
     * Test A: Halaman create dapat diakses dan tindak lanjut dapat dibuat dengan sukses
     */
    public function test_can_create_tindak_lanjut_successfully(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];
        $detailRusak = $data['detailRusak'];

        // 1. Render halaman create dengan param pemeriksaan_id dan detail_id
        $createResponse = $this->get(route('tindak-lanjut.create', [
            'pemeriksaan_id' => $pemeriksaan->id,
            'detail_id' => $detailRusak->id,
        ]));
        $createResponse->assertStatus(200);
        $createResponse->assertSee($apar->kode);
        $createResponse->assertSee($detailRusak->item_nama);

        // 2. Submit form tindak lanjut
        $this->actingAsPetugas(['name' => 'Budi Setiawan K3RS']);
        $payload = [
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $detailRusak->id,
            'jenis_tindakan' => 'penggantian_komponen',
            'prioritas' => 'tinggi',
            'uraian_tindakan' => 'Penggantian selang nozzle baru karena retak',
            'status' => 'belum_ditindaklanjuti',
            'petugas_pelaksana' => 'Budi Setiawan K3RS',
            'tanggal_tindakan' => '2026-10-03',
            'catatan' => 'Part selang cadangan diambil dari logistik K3RS',
        ];

        $postResponse = $this->post(route('tindak-lanjut.store'), $payload);

        $tindakLanjut = TindakLanjut::first();
        $this->assertNotNull($tindakLanjut);
        $postResponse->assertRedirect(route('tindak-lanjut.show', $tindakLanjut->id));

        $this->assertDatabaseHas('tindak_lanjuts', [
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $detailRusak->id,
            'jenis_tindakan' => 'penggantian_komponen',
            'status' => 'belum_ditindaklanjuti',
            'petugas_pelaksana' => 'Budi Setiawan K3RS',
        ]);
    }

    /**
     * Test B: Relasi tindak lanjut terhubung dengan APAR, Pemeriksaan, dan Temuan Checklist
     */
    public function test_tindak_lanjut_relationships(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];
        $detailRusak = $data['detailRusak'];

        $tl = TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $detailRusak->id,
            'jenis_tindakan' => 'perbaikan',
            'uraian_tindakan' => 'Perbaikan klem selang',
            'status' => 'dalam_proses',
            'prioritas' => 'sedang',
            'petugas_pelaksana' => 'Teknisi Joko',
            'tanggal_tindakan' => '2026-10-03',
        ]);

        // Cek relasi dari TindakLanjut
        $this->assertEquals($apar->id, $tl->apar->id);
        $this->assertEquals($pemeriksaan->id, $tl->pemeriksaan->id);
        $this->assertEquals($detailRusak->id, $tl->pemeriksaanDetail->id);

        // Cek relasi balik dari Apar
        $this->assertTrue($apar->tindakLanjuts->contains($tl));

        // Cek relasi balik dari Pemeriksaan
        $this->assertTrue($pemeriksaan->tindakLanjuts->contains($tl));

        // Cek relasi balik dari PemeriksaanDetail
        $this->assertEquals($tl->id, $detailRusak->tindak_lanjut->id);
    }

    /**
     * Test C: Alur status Belum Ditindaklanjuti -> Dalam Proses -> Selesai
     */
    public function test_status_progression_from_belum_to_dalam_proses_to_selesai(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];

        $tl = TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'jenis_tindakan' => 'pengisian_ulang',
            'uraian_tindakan' => 'Pengisian ulang media powder',
            'status' => 'belum_ditindaklanjuti',
            'prioritas' => 'tinggi',
            'petugas_pelaksana' => 'Vendor APAR',
            'tanggal_tindakan' => '2026-10-03',
        ]);

        $this->assertEquals('belum_ditindaklanjuti', $tl->status);
        $this->assertNull($tl->tanggal_selesai);

        // Transisi 1: Belum Ditindaklanjuti -> Dalam Proses
        $response1 = $this->patch(route('tindak-lanjut.status', $tl->id), [
            'status' => 'dalam_proses',
            'catatan' => 'Pekerjaan perbaikan dimulai hari ini',
        ]);
        $response1->assertRedirect();
        $tl->refresh();
        $this->assertEquals('dalam_proses', $tl->status);
        $this->assertNull($tl->tanggal_selesai);

        // Transisi 2: Dalam Proses -> Selesai
        $response2 = $this->patch(route('tindak-lanjut.status', $tl->id), [
            'status' => 'selesai',
            'update_kondisi_apar' => 1,
            'catatan' => 'Pengisian selesai dan tabung kembali siap digunakan',
        ]);
        $response2->assertRedirect();
        $tl->refresh();
        $this->assertEquals('selesai', $tl->status);
        $this->assertNotNull($tl->tanggal_selesai);

        // Pastikan kondisi unit APAR juga diperbarui menjadi 'baik'
        $apar->refresh();
        $this->assertEquals(Apar::STATUS_BAIK, $apar->status);
    }

    /**
     * Test D: Tindak lanjut muncul pada Detail APAR -> Riwayat Tindak Lanjut
     */
    public function test_detail_apar_shows_tindak_lanjut_history(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];

        $tl = TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'jenis_tindakan' => 'pembersihan',
            'uraian_tindakan' => 'Pembersihan debu dan kerak korosi pada leher tabung',
            'status' => 'selesai',
            'prioritas' => 'rendah',
            'petugas_pelaksana' => 'Siti K3RS',
            'tanggal_tindakan' => '2026-10-03',
            'tanggal_selesai' => '2026-10-03',
        ]);

        $response = $this->get(route('apar.show', $apar->id));
        $response->assertStatus(200);
        $response->assertSee('Riwayat Tindak Lanjut & Pemeliharaan APAR');
        $response->assertSee('Pembersihan');
        $response->assertSee('Siti K3RS');
        $response->assertSee(route('tindak-lanjut.show', $tl->id));
    }

    /**
     * Test E: Hasil checklist menampilkan indikator tindak lanjut
     */
    public function test_pemeriksaan_show_displays_tindak_lanjut_status_for_temuan(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];
        $detailRusak = $data['detailRusak'];

        // Kondisi 1: Belum ada tindak lanjut -> ada tombol "Buat Tindak Lanjut"
        $response1 = $this->get(route('pemeriksaan.show', $pemeriksaan->id));
        $response1->assertStatus(200);
        $response1->assertSee('Buat Tindak Lanjut');
        $response1->assertSee(route('tindak-lanjut.create', [
            'pemeriksaan_id' => $pemeriksaan->id,
            'detail_id' => $detailRusak->id,
        ]));

        // Buat tindak lanjut
        $tl = TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $detailRusak->id,
            'jenis_tindakan' => 'penggantian_komponen',
            'uraian_tindakan' => 'Ganti selang',
            'status' => 'dalam_proses',
            'prioritas' => 'tinggi',
            'petugas_pelaksana' => 'Teknisi RS',
            'tanggal_tindakan' => '2026-10-03',
        ]);

        // Kondisi 2: Sudah ada tindak lanjut -> tampilkan indikator "Lihat Tindak Lanjut"
        $response2 = $this->get(route('pemeriksaan.show', $pemeriksaan->id));
        $response2->assertStatus(200);
        $response2->assertSee('Lihat Tindak Lanjut');
        $response2->assertSee(route('tindak-lanjut.show', $tl->id));
    }

    /**
     * Test F: Mencegah duplicate tindak lanjut untuk temuan yang sama
     */
    public function test_prevents_duplicate_tindak_lanjut_for_same_checklist_detail(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];
        $detailRusak = $data['detailRusak'];

        // Buat tindak lanjut pertama
        $existing = TindakLanjut::create([
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $detailRusak->id,
            'jenis_tindakan' => 'penggantian_komponen',
            'uraian_tindakan' => 'Ganti selang nozzle',
            'status' => 'dalam_proses',
            'prioritas' => 'tinggi',
            'petugas_pelaksana' => 'Teknisi 1',
            'tanggal_tindakan' => '2026-10-03',
        ]);

        // Coba buka form create lagi untuk detail yang sama -> harus di-redirect ke show
        $createResponse = $this->get(route('tindak-lanjut.create', [
            'pemeriksaan_id' => $pemeriksaan->id,
            'detail_id' => $detailRusak->id,
        ]));
        $createResponse->assertRedirect(route('tindak-lanjut.show', $existing->id));

        // Coba kirim POST store duplicate -> harus ditolak dan redirect ke show
        $postResponse = $this->post(route('tindak-lanjut.store'), [
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $detailRusak->id,
            'jenis_tindakan' => 'perbaikan',
            'prioritas' => 'sedang',
            'uraian_tindakan' => 'Tindakan ganda tidak disengaja',
            'status' => 'belum_ditindaklanjuti',
            'petugas_pelaksana' => 'Teknisi 2',
            'tanggal_tindakan' => '2026-10-03',
        ]);
        $postResponse->assertRedirect(route('tindak-lanjut.show', $existing->id));

        // Pastikan jumlah tindak lanjut tetap 1 (tidak duplikat)
        $this->assertEquals(1, TindakLanjut::where('pemeriksaan_detail_id', $detailRusak->id)->count());
    }

    /**
     * Test G: Validasi menolak input tidak valid atau relasi yang tidak cocok
     */
    public function test_validation_rejects_invalid_inputs_and_mismatched_relations(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];

        // 1. Field wajib kosong
        $resp1 = $this->post(route('tindak-lanjut.store'), []);
        $resp1->assertSessionHasErrors(['apar_id', 'pemeriksaan_id', 'jenis_tindakan', 'uraian_tindakan', 'status', 'petugas_pelaksana', 'tanggal_tindakan']);

        // 2. APAR ID tidak valid
        $resp2 = $this->post(route('tindak-lanjut.store'), [
            'apar_id' => 99999,
            'pemeriksaan_id' => $pemeriksaan->id,
            'jenis_tindakan' => 'perbaikan',
            'uraian_tindakan' => 'Perbaikan coba',
            'status' => 'belum_ditindaklanjuti',
            'prioritas' => 'sedang',
            'petugas_pelaksana' => 'Budi',
            'tanggal_tindakan' => '2026-10-03',
        ]);
        $resp2->assertSessionHasErrors(['apar_id']);

        // 3. Detail ID milik pemeriksaan lain / tidak cocok
        $otherPemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Pemeriksa Lain',
            'tanggal_pemeriksaan' => Carbon::now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);
        $otherDetail = PemeriksaanDetail::create([
            'pemeriksaan_id' => $otherPemeriksaan->id,
            'item_nama' => 'Item detail milik pemeriksaan lain',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'urutan' => 0,
        ]);

        $resp3 = $this->post(route('tindak-lanjut.store'), [
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'pemeriksaan_detail_id' => $otherDetail->id, // Mismatched!
            'jenis_tindakan' => 'perbaikan',
            'uraian_tindakan' => 'Coba manipulasi detail ID',
            'status' => 'belum_ditindaklanjuti',
            'prioritas' => 'sedang',
            'petugas_pelaksana' => 'Budi',
            'tanggal_tindakan' => '2026-10-03',
        ]);
        $resp3->assertSessionHasErrors(['pemeriksaan_detail_id']);
    }

    /**
     * Test H: Regresi fitur Tahap 1 sampai 6 tetap berfungsi normal
     */
    public function test_regression_tahap_1_to_6_still_work(): void
    {
        $data = $this->createDummyHierarchy();
        $apar = $data['apar'];
        $pemeriksaan = $data['pemeriksaan'];

        // Tahap 1: Gedung, Lantai, Ruangan, Denah
        $respLokasi = $this->get(route('lokasi.index'));
        $respLokasi->assertStatus(200);

        // Tahap 2: Master APAR index & detail
        $respApar = $this->get(route('apar.index'));
        $respApar->assertStatus(200);
        $respAparShow = $this->get(route('apar.show', $apar->id));
        $respAparShow->assertStatus(200);

        // Tahap 3: Penempatan Titik Denah
        $respDenah = $this->get(route('penempatan.index'));
        $respDenah->assertStatus(200);

        // Tahap 4: Jadwal Inspeksi
        $jadwal = Jadwal::create([
            'nama' => 'Inspeksi Triwulan Q4 2026',
            'tanggal_mulai' => '2026-10-01',
            'tanggal_selesai' => '2026-10-31',
            'status' => 'berjalan',
        ]);
        $respJadwal = $this->get(route('jadwal.index'));
        $respJadwal->assertStatus(200);

        // Tahap 5: Scanner QR
        $respScan = $this->get(route('scan.index'));
        $respScan->assertStatus(200);

        // Tahap 6: Pemeriksaan & Lembar Checklist
        $respPemeriksaanShow = $this->get(route('pemeriksaan.show', $pemeriksaan->id));
        $respPemeriksaanShow->assertStatus(200);
        $respPemeriksaanShow->assertSee('Hasil Checklist Pemeriksaan Fisik');
    }
}
