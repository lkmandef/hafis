<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\PenggantianApar;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private function createHierarchy(): array
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-TEST',
            'nama' => 'Gedung Bedah Sentral',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 2,
            'nama' => 'Lantai 2 Bedah',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-OK-01',
            'nama' => 'Kamar Operasi 1',
            'kategori' => 'pelayanan',
        ]);

        $apar = Apar::create([
            'kode' => 'APAR-OK-01',
            'nomor_register' => 'REG-OK-001',
            'qr_code' => 'HAFIS-QR-APAROK01-TEST',
            'ruangan_id' => $ruangan->id,
            'posisi_detail' => 'Pintu Masuk Ruang OK 1',
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'merek' => 'Chubb',
            'tipe_model' => 'Portable CO2',
            'status' => Apar::STATUS_BAIK,
            'tanggal_pemasangan' => '2024-01-10',
            'tanggal_kadaluarsa' => '2027-01-10',
        ]);

        return [$gedung, $lantai, $ruangan, $apar];
    }

    public function test_menu_laporan_index_dapat_diakses_dan_menampilkan_5_jenis_laporan(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $response = $this->get(route('laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Pusat Laporan');
        $response->assertSee('Laporan Data APAR');
        $response->assertSee('Laporan Pemeriksaan APAR');
        $response->assertSee('Laporan Temuan Pemeriksaan');
        $response->assertSee('Laporan Tindak Lanjut');
        $response->assertSee('Laporan Penggantian APAR');

        // Memastikan tombol akses ke 5 laporan tersedia
        $response->assertSee(route('laporan.apar', [], false));
        $response->assertSee(route('laporan.pemeriksaan', [], false));
        $response->assertSee(route('laporan.temuan', [], false));
        $response->assertSee(route('laporan.tindak_lanjut', [], false));
        $response->assertSee(route('laporan.penggantian', [], false));
    }

    public function test_laporan_1_data_apar_dapat_difilter_dan_diunduh_csv_serta_print(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        // 1. Web View default
        $response = $this->get(route('laporan.apar'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Data APAR');
        $response->assertSee($apar->kode);
        $response->assertSee('Gedung Bedah Sentral');
        $response->assertSee('Export CSV (Excel)');
        $response->assertSee('Cetak Laporan');

        // 2. Filter dengan kondisi cocok
        $filterMatch = $this->get(route('laporan.apar', [
            'gedung_id' => $gedung->id,
            'kondisi' => 'baik',
            'jenis_media' => 'CO2 (Carbon Dioxide)',
        ]));
        $filterMatch->assertStatus(200);
        $filterMatch->assertSee($apar->kode);

        // 3. Filter kondisi tidak cocok (Empty State)
        $filterNoMatch = $this->get(route('laporan.apar', [
            'kondisi' => 'rusak',
        ]));
        $filterNoMatch->assertStatus(200);
        $filterNoMatch->assertSee('Belum ada data untuk filter yang dipilih.');

        // 4. Unduh format CSV
        $csvResponse = $this->get(route('laporan.apar', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        // 5. Tampilan Cetak (Print View)
        $printResponse = $this->get(route('laporan.apar', ['format' => 'print']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('LAPORAN DATA MASTER');
        $printResponse->assertSee($apar->kode);
    }

    public function test_laporan_2_pemeriksaan_apar_dapat_difilter_dan_terhubung_ke_detail_checklist(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $jadwal = Jadwal::create([
            'nama' => 'Inspeksi Triwulan I Bedah',
            'tanggal_mulai' => '2026-03-01',
            'tanggal_selesai' => '2026-03-31',
            'status' => 'aktif',
        ]);

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal->id,
            'pemeriksa_nama' => 'Ns. Budi Santoso',
            'tanggal_pemeriksaan' => Carbon::parse('2026-03-15 09:30:00'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'catatan' => 'Tabung dalam kondisi prima siap operasional',
            'status' => 'selesai',
        ]);

        // 1. Web View
        $response = $this->get(route('laporan.pemeriksaan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Pemeriksaan APAR');
        $response->assertSee('Ns. Budi Santoso');
        $response->assertSee($apar->kode);
        $response->assertSee(route('pemeriksaan.show', $pemeriksaan->id));

        // 2. Filter Tanggal & Hasil
        $filterMatch = $this->get(route('laporan.pemeriksaan', [
            'dari_tanggal' => '2026-03-01',
            'sampai_tanggal' => '2026-03-31',
            'hasil' => Pemeriksaan::HASIL_BAIK,
        ]));
        $filterMatch->assertStatus(200);
        $filterMatch->assertSee('Ns. Budi Santoso');

        // 3. Unduh CSV & Print
        $csvResponse = $this->get(route('laporan.pemeriksaan', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        $printResponse = $this->get(route('laporan.pemeriksaan', ['format' => 'print']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('LAPORAN RIWAYAT PEMERIKSAAN');
    }

    public function test_laporan_3_temuan_pemeriksaan_hanya_menampilkan_item_tidak_baik(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Teknisi Joko',
            'tanggal_pemeriksaan' => Carbon::parse('2026-03-10 10:00:00'),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'catatan' => 'Ditemukan selang retak',
            'status' => 'selesai',
        ]);

        // Item 1: Kondisi Baik (TIDAK BOLEH muncul di Laporan Temuan)
        $detailBaik = PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Tabung & Cat Fisik',
            'hasil' => PemeriksaanDetail::HASIL_BAIK,
            'catatan' => 'Cat mulus',
        ]);

        // Item 2: Kondisi Tidak Baik (HARUS muncul di Laporan Temuan)
        $detailRusak = PemeriksaanDetail::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'item_nama' => 'Selang & Nozzle',
            'hasil' => PemeriksaanDetail::HASIL_TIDAK_BAIK,
            'catatan' => 'Selang mengalami getas dan retak pada sambungan',
        ]);

        // 1. Web View
        $response = $this->get(route('laporan.temuan'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Temuan Pemeriksaan');
        $response->assertSee('Selang &amp; Nozzle', false);
        $response->assertSee('Selang mengalami getas dan retak pada sambungan');
        // Pastikan item baik tidak muncul
        $response->assertDontSee('Tabung & Cat Fisik');

        // 2. Filter Status Tindak Lanjut: Belum Dibuat TL
        $filterTl = $this->get(route('laporan.temuan', ['status_tl' => 'belum_ada_tl']));
        $filterTl->assertStatus(200);
        $filterTl->assertSee('Selang &amp; Nozzle', false);

        // 3. Unduh CSV & Print
        $csvResponse = $this->get(route('laporan.temuan', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        $printResponse = $this->get(route('laporan.temuan', ['format' => 'print']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('LAPORAN TEMUAN KERUSAKAN');
    }

    public function test_laporan_4_tindak_lanjut_dan_pemeliharaan_dapat_difilter_dan_dikelompokkan(): void
    {
        [$gedung, $lantai, $ruangan, $apar] = $this->createHierarchy();

        $tindakLanjut = TindakLanjut::create([
            'apar_id' => $apar->id,
            'tanggal_tindakan' => Carbon::parse('2026-03-12'),
            'deskripsi_temuan' => 'Selang pecah perlu ganti',
            'jenis_tindakan' => TindakLanjut::JENIS_PENGGANTIAN_KOMPONEN,
            'uraian_tindakan' => 'Penggantian 1 unit selang fleksibel OEM Chubb',
            'petugas_pelaksana' => 'Ahmad Teknisi K3',
            'status' => TindakLanjut::STATUS_SELESAI,
            'tanggal_selesai' => Carbon::parse('2026-03-12'),
            'catatan' => 'Selesai dan telah diuji tekanan aman',
        ]);

        // 1. Web View
        $response = $this->get(route('laporan.tindak_lanjut'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Tindak Lanjut');
        $response->assertSee('Ahmad Teknisi K3');
        $response->assertSee('Penggantian 1 unit selang fleksibel OEM Chubb');

        // 2. Filter Status & Jenis
        $filterMatch = $this->get(route('laporan.tindak_lanjut', [
            'status' => 'selesai',
            'jenis_tindakan' => 'penggantian_komponen',
        ]));
        $filterMatch->assertStatus(200);
        $filterMatch->assertSee('Ahmad Teknisi K3');

        // 3. Unduh CSV & Print
        $csvResponse = $this->get(route('laporan.tindak_lanjut', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        $printResponse = $this->get(route('laporan.tindak_lanjut', ['format' => 'print']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('LAPORAN REKAPITULASI TINDAK LANJUT');
    }

    public function test_laporan_5_penggantian_apar_menjaga_integritas_historis_lama_ke_baru(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama] = $this->createHierarchy();

        $aparBaru = Apar::create([
            'kode' => 'APAR-OK-01-NEW',
            'nomor_register' => 'REG-OK-NEW-002',
            'qr_code' => 'HAFIS-QR-APAROK01NEW-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'merek' => 'Yamato',
            'status' => Apar::STATUS_BAIK,
            'replaces_apar_id' => $aparLama->id,
        ]);

        $aparLama->update([
            'status' => Apar::STATUS_TIDAK_AKTIF,
            'replaced_by_apar_id' => $aparBaru->id,
        ]);

        $penggantian = PenggantianApar::create([
            'apar_lama_id' => $aparLama->id,
            'apar_baru_id' => $aparBaru->id,
            'tanggal_penggantian' => Carbon::parse('2026-03-20'),
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'alasan_keterangan' => 'Tabung lama mengalami korosi parah pada bagian bawah',
            'petugas_nama' => 'Bambang Staf IPSRS',
            'catatan' => 'Unit lama ditarik ke gudang B3',
        ]);

        // 1. Web View
        $response = $this->get(route('laporan.penggantian'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Penggantian APAR');
        // Integritas historis: harus melihat korelasi APAR lama dan APAR baru
        $response->assertSee('APAR-OK-01');
        $response->assertSee('APAR-OK-01-NEW');
        $response->assertSee('Bambang Staf IPSRS');
        $response->assertSee('Tabung lama mengalami korosi parah');

        // 2. Filter Alasan Penggantian
        $filterMatch = $this->get(route('laporan.penggantian', ['alasan' => 'rusak']));
        $filterMatch->assertStatus(200);
        $filterMatch->assertSee('APAR-OK-01-NEW');

        // 3. Unduh CSV & Print
        $csvResponse = $this->get(route('laporan.penggantian', ['format' => 'csv']));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('content-type'));

        $printResponse = $this->get(route('laporan.penggantian', ['format' => 'print']));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('LAPORAN HISTORI MUTASI');
    }

    public function test_legacy_riwayat_route_dialihkan_ke_laporan(): void
    {
        $response = $this->get('/riwayat');
        $response->assertRedirect(route('laporan.index'));

        $responseDetail = $this->get('/riwayat/INS-2026-001');
        $responseDetail->assertRedirect(route('laporan.pemeriksaan'));
    }

    public function test_format_cetak_5_laporan_memiliki_struktur_tanda_tangan_dan_autofit_konsisten(): void
    {
        $this->createHierarchy();

        $routes = [
            'laporan.apar' => 'portrait',
            'laporan.pemeriksaan' => 'portrait',
            'laporan.temuan' => 'portrait',
            'laporan.tindak_lanjut' => 'portrait',
            'laporan.penggantian' => 'landscape',
        ];

        foreach ($routes as $route => $expectedOrientation) {
            $response = $this->get(route($route, ['format' => 'print']));
            $response->assertStatus(200);

            $content = $response->getContent();

            // Memastikan tanda tangan resmi K3RS hadir
            $response->assertSee('Banjarmasin,');
            $response->assertSee('Petugas Pelaksana K3RS,');
            $response->assertSee('RSUD dr. H. Moch. Ansari Saleh');
            $response->assertSee('Mengetahui,');
            $response->assertSee('Kepala Instalasi K3RS');
            $response->assertSee('RSUD dr. H. Moch. Ansari Saleh Banjarmasin');
            $response->assertSee('( .................................................... )');
            $response->assertSee('NIP. .....................................');

            // Memastikan blok tanda tangan hanya muncul tepat SATU kali (tidak ada duplikasi)
            $this->assertEquals(1, substr_count($content, 'id="footerSignatures"'), "Tanda tangan harus muncul tepat satu kali di {$route}");

            // Memastikan container cetak dan script autoFitPrintPages hadir
            $response->assertSee('id="printContainer"', false);
            $response->assertSee('autoFitPrintPages');

            // Memastikan orientasi kertas sesuai spesifikasi
            $response->assertSee("size: A4 {$expectedOrientation};", false);
        }
    }
}
