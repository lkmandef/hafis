<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use App\Models\TindakLanjut;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionUserIdentityTest extends TestCase
{
    use RefreshDatabase;

    private function createOperationalApar(array $attributes = []): Apar
    {
        $gedung = Gedung::create(['kode' => 'GDG-'.uniqid(), 'nama' => 'Gedung Uji', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Uji', 'kode' => 'R-'.uniqid()]);
        $lokasi = Lokasi::create(['nama' => 'Lokasi Uji '.uniqid(), 'status' => Lokasi::STATUS_AKTIF]);

        return Apar::create(array_merge([
            'kode' => 'APAR-ID-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-ID-'.uniqid(),
            'ruangan_id' => $ruangan->id,
            'lokasi_id' => $lokasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Yamato',
            'status' => Apar::STATUS_BAIK,
        ], $attributes));
    }

    /**
     * Pemeriksaan fisik baru mengunci user_id dan pemeriksa_nama sesuai akun aktif
     */
    public function test_new_inspection_automatically_records_authenticated_user_identity(): void
    {
        $user = $this->actingAsPetugas(['name' => 'Siti Rahmah, A.Md.Kep']);
        $apar = $this->createOperationalApar();

        $items = [];
        foreach (Pemeriksaan::CANONICAL_CHECKLIST_ITEMS as $name) {
            $items[] = [
                'nama' => $name,
                'hasil' => 'baik',
                'catatan' => null,
            ];
        }

        $response = $this->post(route('pemeriksaan.store'), [
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Nama Palsu Yang Dikirim Browser', // Harus di-override server-side
            'items' => $items,
        ]);

        $response->assertRedirect();

        $pemeriksaan = Pemeriksaan::where('apar_id', $apar->id)
            ->where('periode_tahun', 2026)
            ->where('periode_bulan', 10)
            ->first();

        $this->assertNotNull($pemeriksaan);
        $this->assertEquals($user->id, $pemeriksaan->user_id);
        $this->assertEquals('Siti Rahmah, A.Md.Kep', $pemeriksaan->pemeriksa_nama);
        $this->assertNotEquals('Nama Palsu Yang Dikirim Browser', $pemeriksaan->pemeriksa_nama);
    }

    /**
     * Pembuatan jadwal baru otomatis mencatat user_id dan dibuat_oleh sesuai akun aktif
     */
    public function test_new_schedule_automatically_records_authenticated_user(): void
    {
        $user = $this->actingAsAdmin(['name' => 'dr. H. Hendra Kurniawan, Sp.PK']);

        $response = $this->post(route('jadwal.store'), [
            'nama' => 'Inspeksi Triwulan Akhir',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(14)->toDateString(),
            'tipe_target' => 'semua',
            'status' => 'belum_dimulai',
            'dibuat_oleh' => 'Nama Bebas',
        ]);

        $response->assertRedirect();

        $jadwal = Jadwal::where('nama', 'Inspeksi Triwulan Akhir')->first();
        $this->assertNotNull($jadwal);
        $this->assertEquals($user->id, $jadwal->user_id);
        $this->assertEquals('dr. H. Hendra Kurniawan, Sp.PK', $jadwal->dibuat_oleh);
    }

    /**
     * Pembuatan tindak lanjut baru otomatis mencatat user_id dan petugas_pelaksana
     */
    public function test_new_followup_automatically_records_authenticated_user(): void
    {
        $user = $this->actingAsPetugas(['name' => 'Bambang Irawan']);
        $apar = $this->createOperationalApar();

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'pemeriksa_nama' => 'Pemeriksa Lama',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_PERLU_TINDAKAN,
            'status' => 'selesai',
        ]);

        $response = $this->post(route('tindak-lanjut.store'), [
            'apar_id' => $apar->id,
            'pemeriksaan_id' => $pemeriksaan->id,
            'jenis_tindakan' => 'perbaikan',
            'uraian_tindakan' => 'Penggantian pin dan segel pengaman',
            'status' => 'dalam_proses',
            'prioritas' => 'sedang',
            'petugas_pelaksana' => 'Nama Form Lain',
            'tanggal_tindakan' => now()->toDateString(),
        ]);

        $response->assertRedirect();

        $tl = TindakLanjut::where('pemeriksaan_id', $pemeriksaan->id)->first();
        $this->assertNotNull($tl);
        $this->assertEquals($user->id, $tl->user_id);
        $this->assertEquals('Bambang Irawan', $tl->petugas_pelaksana);
    }

    /**
     * Data historis dengan user_id = NULL tetap dapat ditampilkan tanpa galat
     */
    public function test_historical_records_with_null_user_id_render_gracefully(): void
    {
        $this->actingAsPetugas();
        $apar = $this->createOperationalApar();

        $pemHistoris = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'user_id' => null,
            'pemeriksa_nama' => 'Petugas K3RS Historis',
            'tanggal_pemeriksaan' => Carbon::now()->subMonths(3),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
            'periode_tahun' => 2026,
            'periode_bulan' => 7,
        ]);

        $response = $this->get(route('pemeriksaan.show', $pemHistoris->id));
        $response->assertStatus(200);
        $response->assertSee('Petugas K3RS Historis');

        // Buka laporan pemeriksaan
        $laporanResponse = $this->get(route('laporan.pemeriksaan'));
        $laporanResponse->assertStatus(200);
        $laporanResponse->assertSee('Petugas K3RS Historis');
    }

    /**
     * Menonaktifkan akun pengguna tidak menghapus atau merusak data riwayat pemeriksaan
     */
    public function test_deactivating_user_preserves_inspection_audit_history(): void
    {
        $admin = $this->actingAsAdmin();
        $petugas = User::factory()->petugas()->create(['name' => 'Petugas Mutasi']);
        $apar = $this->createOperationalApar();

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'user_id' => $petugas->id,
            'pemeriksa_nama' => $petugas->name,
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Admin menonaktifkan petugas
        $this->post(route('users.toggle-status', $petugas->id));

        $this->assertFalse($petugas->fresh()->isActive());

        // Pastikan catatan pemeriksaan tetap ada dan merujuk nama petugas
        $this->assertDatabaseHas('pemeriksaans', [
            'id' => $pemeriksaan->id,
            'user_id' => $petugas->id,
            'pemeriksa_nama' => 'Petugas Mutasi',
        ]);
    }
}
