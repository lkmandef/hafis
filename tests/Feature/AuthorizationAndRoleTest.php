<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    private function createSampleApar(): Apar
    {
        $gedung = Gedung::create(['kode' => 'GDG-TEST', 'nama' => 'Gedung Uji', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Uji', 'kode' => 'R-01']);
        $lokasi = Lokasi::create(['nama' => 'Lokasi Uji', 'status' => Lokasi::STATUS_AKTIF]);

        return Apar::create([
            'kode' => 'APAR-TEST-'.rand(100, 999),
            'qr_code' => 'HAFIS-QR-TEST-'.uniqid(),
            'ruangan_id' => $ruangan->id,
            'lokasi_id' => $lokasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);
    }

    /**
     * Admin (Kepala K3RS) dapat membuka halaman Pengguna & Akses
     */
    public function test_admin_can_access_user_management(): void
    {
        $this->actingAsAdmin();

        $response = $this->get(route('users.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengguna & Akses');
        $response->assertSee('Tambah Akun Petugas');
    }

    /**
     * Petugas K3RS dilarang membuka halaman Pengguna & Akses (403 Forbidden)
     */
    public function test_petugas_is_forbidden_from_user_management(): void
    {
        $this->actingAsPetugas();

        $response = $this->get(route('users.index'));
        $response->assertStatus(403);

        $createResponse = $this->get(route('users.create'));
        $createResponse->assertStatus(403);
    }

    /**
     * Petugas K3RS dilarang menghapus unit APAR (403 Forbidden)
     */
    public function test_petugas_is_forbidden_from_deleting_apar(): void
    {
        $this->actingAsPetugas();
        $apar = $this->createSampleApar();

        $response = $this->delete(route('apar.destroy', $apar->id));
        $response->assertStatus(403);
        $this->assertNotSoftDeleted('apars', ['id' => $apar->id]);
    }

    /**
     * Admin (Kepala K3RS) diizinkan menghapus unit APAR (SoftDelete)
     */
    public function test_admin_can_delete_apar(): void
    {
        $this->actingAsAdmin();
        $apar = $this->createSampleApar();

        $response = $this->delete(route('apar.destroy', $apar->id));
        $response->assertRedirect(route('apar.index'));
        $this->assertSoftDeleted('apars', ['id' => $apar->id]);
    }

    /**
     * Petugas K3RS dilarang membatalkan atau menghapus jadwal inspeksi
     */
    public function test_petugas_is_forbidden_from_canceling_or_deleting_jadwal(): void
    {
        $this->actingAsPetugas();
        $jadwal = Jadwal::create([
            'nama' => 'Jadwal Uji',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'tipe_target' => 'semua',
            'status' => 'berjalan',
            'dibuat_oleh' => 'Admin K3RS',
        ]);

        $cancelResponse = $this->post(route('jadwal.batalkan', $jadwal->id));
        $cancelResponse->assertStatus(403);

        $deleteResponse = $this->delete(route('jadwal.destroy', $jadwal->id));
        $deleteResponse->assertStatus(403);
    }

    /**
     * Admin dapat membuat akun petugas baru melalui form Pengguna & Akses
     */
    public function test_admin_can_create_new_user(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('users.store'), [
            'name' => 'Ahmad Fauzi',
            'username' => 'ahmad_k3',
            'email' => 'ahmad@rsansarisaleh.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_PETUGAS,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'ahmad_k3',
            'name' => 'Ahmad Fauzi',
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);
    }

    /**
     * Admin dapat mengedit data pengguna lain
     */
    public function test_admin_can_edit_existing_user(): void
    {
        $admin = $this->actingAsAdmin();
        $petugas = User::factory()->petugas()->create([
            'name' => 'Budi Lama',
            'username' => 'budi_lama',
        ]);

        $response = $this->put(route('users.update', $petugas->id), [
            'name' => 'Budi Baru',
            'username' => 'budi_baru',
            'email' => 'budi.baru@rsansarisaleh.id',
            'role' => User::ROLE_PETUGAS,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $petugas->id,
            'name' => 'Budi Baru',
            'username' => 'budi_baru',
        ]);
    }

    /**
     * Admin dapat menonaktifkan akun petugas
     */
    public function test_admin_can_toggle_user_status(): void
    {
        $this->actingAsAdmin();
        $petugas = User::factory()->petugas()->create(['is_active' => true]);

        $response = $this->post(route('users.toggle-status', $petugas->id));
        $response->assertRedirect(route('users.index'));

        $this->assertFalse($petugas->fresh()->isActive());
    }

    /**
     * Admin dicegah menonaktifkan akunnya sendiri
     */
    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = $this->actingAsAdmin(['is_active' => true]);

        $response = $this->post(route('users.toggle-status', $admin->id));
        $response->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->isActive());
    }

    /**
     * Admin dapat mereset kata sandi pengguna lain
     */
    public function test_admin_can_reset_user_password(): void
    {
        $this->actingAsAdmin();
        $petugas = User::factory()->petugas()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->post(route('users.reset-password', $petugas->id), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertTrue(Hash::check('new-secure-password', $petugas->fresh()->password));
    }

    /**
     * Kedua peran (Petugas dan Admin) dapat mengakses modul operasional harian
     */
    public function test_both_roles_can_access_daily_operational_modules(): void
    {
        // 1. Petugas
        $this->actingAsPetugas();

        $this->get(route('dashboard'))->assertStatus(200);
        $this->get(route('apar.index'))->assertStatus(200);
        $this->get(route('scan.index'))->assertStatus(200);
        $this->get(route('pemeriksaan.index'))->assertStatus(200);
        $this->get(route('tindak-lanjut.index'))->assertStatus(200);
        $this->get(route('penggantian.index'))->assertStatus(200);
        $this->get(route('laporan.index'))->assertStatus(200);
        $this->get(route('lokasi.index'))->assertStatus(200);

        // 2. Admin
        $this->actingAsAdmin();

        $this->get(route('dashboard'))->assertStatus(200);
        $this->get(route('apar.index'))->assertStatus(200);
        $this->get(route('scan.index'))->assertStatus(200);
        $this->get(route('pemeriksaan.index'))->assertStatus(200);
        $this->get(route('tindak-lanjut.index'))->assertStatus(200);
        $this->get(route('penggantian.index'))->assertStatus(200);
        $this->get(route('laporan.index'))->assertStatus(200);
        $this->get(route('lokasi.index'))->assertStatus(200);
    }
}
