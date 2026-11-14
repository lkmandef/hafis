<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AparExpiringStatusTest extends TestCase
{
    use RefreshDatabase;

    protected Gedung $gedung;

    protected Lantai $lantai;

    protected Ruangan $ruangan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();

        $this->gedung = Gedung::create([
            'kode' => 'GDG-EXP',
            'nama' => 'Gedung Uji Expired',
            'status' => 'aktif',
        ]);

        $this->lantai = Lantai::create([
            'gedung_id' => $this->gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        $this->ruangan = Ruangan::create([
            'lantai_id' => $this->lantai->id,
            'kode' => 'R-EXP-01',
            'nama' => 'Ruang Uji Expired',
        ]);
    }

    /**
     * APAR yang didaftarkan dengan tanggal kadaluarsa <= 7 hari otomatis berstatus 'perhatian'
     */
    public function test_apar_status_automatically_becomes_perhatian_when_created_with_expiry_within_7_days(): void
    {
        $expiringDate = Carbon::now()->addDays(5)->toDateString();

        $payload = [
            'kode' => 'APAR-EXP-001',
            'nomor_register' => 'REG-EXP-001',
            'gedung_id' => $this->gedung->id,
            'lantai_id' => $this->lantai->id,
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
            'tanggal_pemasangan' => Carbon::now()->subYear()->toDateString(),
            'tanggal_kadaluarsa' => $expiringDate,
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertSessionHasNoErrors();

        $apar = Apar::where('kode', 'APAR-EXP-001')->firstOrFail();
        $this->assertSame(Apar::STATUS_PERHATIAN, $apar->status);
        $this->assertTrue($apar->isExpiringSoonOrExpired());
    }

    /**
     * APAR yang didaftarkan dengan tanggal kadaluarsa yang sudah lewat otomatis berstatus 'perhatian'
     */
    public function test_apar_status_automatically_becomes_perhatian_when_created_with_past_expiry_date(): void
    {
        $pastDate = Carbon::now()->subDays(2)->toDateString();

        $payload = [
            'kode' => 'APAR-EXP-002',
            'nomor_register' => 'REG-EXP-002',
            'gedung_id' => $this->gedung->id,
            'lantai_id' => $this->lantai->id,
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => 'baik',
            'tanggal_pemasangan' => Carbon::now()->subYears(2)->toDateString(),
            'tanggal_kadaluarsa' => $pastDate,
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertSessionHasNoErrors();

        $apar = Apar::where('kode', 'APAR-EXP-002')->firstOrFail();
        $this->assertSame(Apar::STATUS_PERHATIAN, $apar->status);
        $this->assertTrue($apar->isExpiringSoonOrExpired());
    }

    /**
     * APAR yang didaftarkan dengan tanggal kadaluarsa lebih dari 7 hari tetap berstatus 'baik'
     */
    public function test_apar_status_remains_baik_when_created_with_expiry_beyond_7_days(): void
    {
        $futureDate = Carbon::now()->addDays(30)->toDateString();

        $payload = [
            'kode' => 'APAR-EXP-003',
            'nomor_register' => 'REG-EXP-003',
            'gedung_id' => $this->gedung->id,
            'lantai_id' => $this->lantai->id,
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Foam (AFFF)',
            'kapasitas' => '6 L',
            'status' => 'baik',
            'tanggal_pemasangan' => Carbon::now()->toDateString(),
            'tanggal_kadaluarsa' => $futureDate,
        ];

        $response = $this->post(route('apar.store'), $payload);
        $response->assertSessionHasNoErrors();

        $apar = Apar::where('kode', 'APAR-EXP-003')->firstOrFail();
        $this->assertSame(Apar::STATUS_BAIK, $apar->status);
        $this->assertFalse($apar->isExpiringSoonOrExpired());
    }

    /**
     * Batch sync memperbarui record APAR yang sudah ada di database ketika mencapai H-7 kadaluarsa
     */
    public function test_sync_expired_status_updates_database_records(): void
    {
        // Simulasikan APAR yang sebelumnya berstatus baik dan belum pernah disinkronisasi
        DB::table('apars')->insert([
            'kode' => 'APAR-EXP-RAW',
            'qr_code' => 'HAFIS-QR-RAW-001',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(4)->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $affected = Apar::syncExpiredStatus();
        $this->assertSame(1, $affected);

        $apar = Apar::where('kode', 'APAR-EXP-RAW')->firstOrFail();
        $this->assertSame(Apar::STATUS_PERHATIAN, $apar->status);
    }

    /**
     * APAR dapat kembali berstatus 'baik' jika tanggal kadaluarsa diperpanjang melampaui H-7
     */
    public function test_apar_status_can_be_reverted_to_baik_when_expiry_date_is_extended(): void
    {
        $apar = Apar::create([
            'kode' => 'APAR-EXP-RENEW',
            'qr_code' => 'HAFIS-QR-RENEW-001',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        // Karena kadaluarsa dalam 2 hari, otomatis berstatus perhatian
        $this->assertSame(Apar::STATUS_PERHATIAN, $apar->fresh()->status);

        // Perpanjang tanggal kadaluarsa hingga 2 tahun ke depan dan set status ke baik
        $payload = [
            'kode' => $apar->kode,
            'gedung_id' => $this->gedung->id,
            'lantai_id' => $this->lantai->id,
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => $apar->jenis_media,
            'kapasitas' => $apar->kapasitas,
            'status' => 'baik',
            'tanggal_kadaluarsa' => Carbon::now()->addYears(2)->toDateString(),
        ];

        $response = $this->put(route('apar.update', $apar->id), $payload);
        $response->assertSessionHasNoErrors();

        $this->assertSame(Apar::STATUS_BAIK, $apar->fresh()->status);
    }

    /**
     * Hasil pemeriksaan berkala 'baik' tidak mengembalikan status APAR ke 'baik' jika APAR mendekati kadaluarsa
     */
    public function test_sync_status_after_inspection_does_not_revert_expiring_apar_to_baik(): void
    {
        $apar = Apar::create([
            'kode' => 'APAR-EXP-INSP',
            'qr_code' => 'HAFIS-QR-INSP-001',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(3)->toDateString(),
        ]);

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Petugas Uji',
            'tanggal_pemeriksaan' => Carbon::now()->toDateString(),
            'periode_tahun' => (int) Carbon::now()->year,
            'periode_bulan' => (int) Carbon::now()->month,
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'user_id' => auth()->id(),
        ]);

        $apar->syncStatusAfterInspection($pemeriksaan);

        $this->assertSame(Apar::STATUS_PERHATIAN, $apar->fresh()->status);
    }

    /**
     * Penyelesaian tindak lanjut tidak mengembalikan status ke 'baik' jika APAR masih mendekati kadaluarsa
     */
    public function test_tindak_lanjut_completion_does_not_revert_expiring_apar_to_baik(): void
    {
        $apar = Apar::create([
            'kode' => 'APAR-EXP-TL',
            'qr_code' => 'HAFIS-QR-TL-001',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(3)->toDateString(),
        ]);

        $tl = TindakLanjut::create([
            'apar_id' => $apar->id,
            'tanggal_tindakan' => Carbon::now()->toDateString(),
            'jenis_tindakan' => TindakLanjut::JENIS_PERBAIKAN,
            'uraian_tindakan' => 'Pengecekan segel pengaman',
            'petugas_pelaksana' => 'Teknisi K3RS',
            'status' => TindakLanjut::STATUS_DALAM_PROSES,
            'user_id' => auth()->id(),
        ]);

        $response = $this->patch(route('tindak-lanjut.status', $tl->id), [
            'status' => 'selesai',
            'update_kondisi_apar' => 1,
            'catatan' => 'Selesai dicek, namun kadaluarsa masih dalam H-3',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame(Apar::STATUS_PERHATIAN, $apar->fresh()->status);
    }

    /**
     * Dashboard dan Topbar secara otomatis menyinkronkan dan menampilkan unit yang mendekati kadaluarsa
     */
    public function test_dashboard_and_topbar_display_expiring_apar(): void
    {
        Apar::create([
            'kode' => 'APAR-EXP-DASH',
            'qr_code' => 'HAFIS-QR-DASH-001',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('APAR-EXP-DASH');
        $response->assertSee('Perlu Perhatian');
    }

    /**
     * Halaman buat tindak lanjut dapat diakses langsung untuk APAR mendekati kadaluarsa tanpa riwayat pemeriksaan
     */
    public function test_tindak_lanjut_create_page_loads_successfully_for_expiring_apar_without_prior_inspection(): void
    {
        $apar = Apar::create([
            'kode' => 'APAR-119-01',
            'qr_code' => 'HAFIS-QR-119-01',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(6)->toDateString(),
        ]);

        $response = $this->get(route('tindak-lanjut.create', ['apar_id' => $apar->id]));

        $response->assertStatus(200);
        $response->assertViewIs('tindak-lanjut.create');
        $response->assertSee('APAR-119-01');
        $response->assertSee('Pemeliharaan');
        $response->assertSee('Masa Kadaluarsa');
        $response->assertSee('Pengisian Ulang');
        $response->assertSee('value="pengisian_ulang"', false);
    }

    /**
     * Tindak lanjut untuk pemeliharaan/pengisian ulang dapat disimpan tanpa pemeriksaan_id
     */
    public function test_can_store_tindak_lanjut_without_pemeriksaan_id_for_expiring_apar(): void
    {
        $apar = Apar::create([
            'kode' => 'APAR-119-02',
            'qr_code' => 'HAFIS-QR-119-02',
            'ruangan_id' => $this->ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_PERHATIAN,
            'tanggal_kadaluarsa' => Carbon::now()->addDays(5)->toDateString(),
        ]);

        $payload = [
            'apar_id' => $apar->id,
            'pemeriksaan_id' => null,
            'jenis_tindakan' => 'pengisian_ulang',
            'prioritas' => 'tinggi',
            'uraian_tindakan' => 'Pengisian ulang media serbuk kimia kering sebelum batas kadaluarsa.',
            'status' => 'dalam_proses',
            'petugas_pelaksana' => 'Teknisi Vendor K3RS',
            'tanggal_tindakan' => Carbon::now()->toDateString(),
        ];

        $response = $this->post(route('tindak-lanjut.store'), $payload);
        $response->assertSessionHasNoErrors();

        $tl = TindakLanjut::where('apar_id', $apar->id)->firstOrFail();
        $this->assertNull($tl->pemeriksaan_id);
        $this->assertSame(TindakLanjut::JENIS_PENGISIAN_ULANG, $tl->jenis_tindakan);
        $response->assertRedirect(route('tindak-lanjut.show', $tl->id));
    }
}
