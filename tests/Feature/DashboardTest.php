<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\PenggantianApar;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsPetugas();
    }

    private function createHierarchy(): array
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-RS-DASH',
            'nama' => 'Gedung Paviliun Garuda',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1 Garuda',
        ]);

        $ruangan = Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'R-GARUDA-01',
            'nama' => 'Ruang Perawatan VVIP 1',
            'kategori' => 'pelayanan',
        ]);

        $aparBaik = Apar::create([
            'kode' => 'APAR-GRD-01',
            'nomor_register' => 'REG-GRD-001',
            'qr_code' => 'HAFIS-QR-APARGRD01-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $aparPerhatian = Apar::create([
            'kode' => 'APAR-GRD-02',
            'nomor_register' => 'REG-GRD-002',
            'qr_code' => 'HAFIS-QR-APARGRD02-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => Apar::STATUS_PERHATIAN,
        ]);

        $aparRusak = Apar::create([
            'kode' => 'APAR-GRD-03',
            'nomor_register' => 'REG-GRD-003',
            'qr_code' => 'HAFIS-QR-APARGRD03-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Clean Agent',
            'kapasitas' => '4 kg',
            'status' => Apar::STATUS_RUSAK,
        ]);

        return [$gedung, $lantai, $ruangan, $aparBaik, $aparPerhatian, $aparRusak];
    }

    public function test_dashboard_dapat_dibuka_dan_menampilkan_ringkasan_data_nyata(): void
    {
        [$gedung, $lantai, $ruangan, $aparBaik, $aparPerhatian, $aparRusak] = $this->createHierarchy();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Monitoring APAR');

        // Memastikan route /dashboard juga bekerja
        $responseDash = $this->get('/dashboard');
        $responseDash->assertStatus(200);

        // Memastikan unit yang memerlukan perhatian muncul sesuai database
        $response->assertSee('APAR-GRD-02');
        $response->assertSee('APAR-GRD-03');
        $response->assertSee('Gedung Paviliun Garuda');

        // Memastikan link navigasi utama berfungsi
        $response->assertSee(route('apar.create'));
        $response->assertSee(route('scan.index'));
        $response->assertSee(route('laporan.index'));
    }

    public function test_dashboard_menghitung_status_dan_progres_jadwal_secara_akurat(): void
    {
        [$gedung, $lantai, $ruangan, $aparBaik, $aparPerhatian, $aparRusak] = $this->createHierarchy();

        $jadwal = Jadwal::create([
            'nama' => 'Inspeksi Triwulan IV Garuda',
            'tanggal_mulai' => Carbon::now()->subDays(2),
            'tanggal_selesai' => Carbon::now()->addDays(5),
            'status' => 'berjalan',
        ]);

        $jadwal->apars()->attach($aparBaik->id, ['status_pemeriksaan' => 'sudah_diperiksa']);
        $jadwal->apars()->attach($aparPerhatian->id, ['status_pemeriksaan' => 'belum_diperiksa']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Inspeksi Triwulan IV Garuda');
        // Progress: 1 dari 2 = 50%
        $response->assertSee('50%');
        $response->assertSee('1');
        $response->assertSee('2');
        $response->assertSee(route('jadwal.show', $jadwal->id));
    }

    public function test_dashboard_menampilkan_unit_memerlukan_perhatian_dan_link_tindak_lanjut(): void
    {
        [$gedung, $lantai, $ruangan, $aparBaik, $aparPerhatian, $aparRusak] = $this->createHierarchy();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Unit APAR Memerlukan Perhatian');

        // Unit yang harus ada di tabel perhatian
        $response->assertSee('APAR-GRD-02');
        $response->assertSee('APAR-GRD-03');
        $response->assertSee('+ Tindak Lanjut');
    }

    public function test_dashboard_menampilkan_log_pemeriksaan_dan_tindak_lanjut_terkini(): void
    {
        [$gedung, $lantai, $ruangan, $aparBaik, $aparPerhatian, $aparRusak] = $this->createHierarchy();

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $aparBaik->id,
            'pemeriksa_nama' => 'Inspektur Hendra',
            'tanggal_pemeriksaan' => Carbon::now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $tindakLanjut = TindakLanjut::create([
            'apar_id' => $aparRusak->id,
            'tanggal_tindakan' => Carbon::now(),
            'deskripsi_temuan' => 'Tekanan drop dan selang retak',
            'jenis_tindakan' => TindakLanjut::JENIS_PERBAIKAN,
            'uraian_tindakan' => 'Rekondisi dan pengisian ulang gas pendorong',
            'petugas_pelaksana' => 'Teknisi Rudi',
            'status' => TindakLanjut::STATUS_DALAM_PROSES,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Inspektur Hendra');
        $response->assertSee(route('pemeriksaan.show', $pemeriksaan->id));
        $response->assertSee('Teknisi Rudi');
        $response->assertSee('Rekondisi dan pengisian ulang gas pendorong');
        $response->assertSee(route('tindak-lanjut.show', $tindakLanjut->id));
    }

    public function test_dashboard_menampilkan_histori_penggantian_apar(): void
    {
        [$gedung, $lantai, $ruangan, $aparLama, $aparPerhatian, $aparRusak] = $this->createHierarchy();

        $aparBaru = Apar::create([
            'kode' => 'APAR-GRD-01-NEW',
            'nomor_register' => 'REG-GRD-NEW-01',
            'qr_code' => 'HAFIS-QR-APARGRD01NEW-TEST',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
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
            'tanggal_penggantian' => Carbon::now(),
            'alasan' => PenggantianApar::ALASAN_RUSAK,
            'petugas_nama' => 'Petugas IPSRS Budi',
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Penggantian APAR Terbaru');
        $response->assertSee('APAR-GRD-01');
        $response->assertSee('APAR-GRD-01-NEW');
        $response->assertSee('Petugas IPSRS Budi');
        $response->assertSee(route('penggantian.show', $penggantian->id));
    }

    public function test_dashboard_aman_dan_rapi_saat_database_kosong(): void
    {
        // Pengujian empty state murni tanpa data di database
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Monitoring APAR');
        $response->assertSee('Tidak ada jadwal inspeksi aktif');
        $response->assertSee('Semua Unit APAR dalam Kondisi Baik');
        $response->assertSee('Belum ada pemeriksaan tercatat');
        $response->assertSee('Belum ada catatan tindak lanjut');
        $response->assertSee('Belum ada riwayat penggantian unit');
    }

    public function test_dashboard_kartu_statistik_menggunakan_parameter_status_kanonis_dan_tanpa_kartu_pending_atas(): void
    {
        [$gedung, $lantai, $ruangan, $aparBaik, $aparPerhatian, $aparRusak] = $this->createHierarchy();

        $response = $this->get('/');
        $response->assertStatus(200);

        // 1. Empat kartu statistik teratas menggunakan parameter status kanonis
        $response->assertSee(route('apar.index', ['status' => 'aktif']));
        $response->assertSee(route('apar.index', ['status' => 'baik']));
        $response->assertSee(route('apar.index', ['status' => 'perhatian']));
        $response->assertSee(route('apar.index', ['status' => 'rusak']));

        // 2. Label kartu rusak tidak ambigu
        $response->assertSee('Kondisi Rusak');
        $response->assertDontSee('Rusak / Perlu Tindakan');

        // 3. Tidak ada lagi link yang menggunakan parameter 'kondisi'
        $response->assertDontSee(route('apar.index', ['kondisi' => 'baik']));
        $response->assertDontSee(route('apar.index', ['kondisi' => 'perhatian']));
        $response->assertDontSee(route('apar.index', ['kondisi' => 'rusak']));

        // 4. Kartu ringkasan Tindak Lanjut Pending telah dihapus dari deretan kartu atas
        $response->assertDontSee('Tindak Lanjut Pending');
        $response->assertDontSee(route('tindak-lanjut.index', ['status' => 'belum_ditindaklanjuti']));

        // 5. Bagian khusus Tindak Lanjut & Pemeliharaan di bawah Dashboard tetap ada
        $response->assertSee('Tindak Lanjut & Pemeliharaan', false);
        $response->assertSee(route('tindak-lanjut.index'));
    }
}
