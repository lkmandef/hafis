<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use App\Models\User;
use App\Services\SmartInspectionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CleanSlateResetVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji kredensial dan hak akses dua akun baru (Ketua K3RS & Petugas K3RS)
     */
    public function test_new_user_credentials_and_roles_are_valid(): void
    {
        $admin = User::create([
            'name' => 'Ketua K3RS',
            'username' => 'ketua.k3rs',
            'email' => 'ketua.k3rs@ansarisaleh.id',
            'password' => Hash::make('KetuaK3rs26!'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $petugas = User::create([
            'name' => 'Petugas K3RS',
            'username' => 'petugas.k3rs',
            'email' => 'petugas.k3rs@ansarisaleh.id',
            'password' => Hash::make('PetugasK3rs26!'),
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        // 1. Akun Ketua K3RS (Admin)
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('KetuaK3rs26!', $admin->password));
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->isActive());

        // 2. Akun Petugas K3RS
        $this->assertNotNull($petugas);
        $this->assertTrue(Hash::check('PetugasK3rs26!', $petugas->password));
        $this->assertTrue($petugas->isPetugas());
        $this->assertTrue($petugas->isActive());
    }

    /**
     * Uji bahwa baseline operasional November 2026 tidak memicu tunggakan periode sebelumnya
     */
    public function test_operational_baseline_november_2026_prevents_pre_baseline_backlog(): void
    {
        config([
            'hafis.operational_start_year' => 2026,
            'hafis.operational_start_month' => 11,
        ]);

        $gedung = Gedung::create(['kode' => 'GDG-TEST', 'nama' => 'Gedung Test', 'status' => 'aktif']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 1, 'nama' => 'Lantai 1']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'kode' => 'R-TEST', 'nama' => 'Ruangan Test']);
        $lokasi = Lokasi::create(['kode' => 'LOK-TEST', 'nama' => 'Lokasi Test', 'status' => 'aktif', 'gedung_id' => $gedung->id, 'lantai_id' => $lantai->id, 'ruangan_id' => $ruangan->id]);

        $apar = Apar::create([
            'kode' => 'APAR-TEST-01',
            'qr_code' => 'HAFIS-QR-APAR-TEST-01',
            'ruangan_id' => $ruangan->id,
            'lokasi_id' => $lokasi->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => 'baik',
        ]);

        $service = app(SmartInspectionService::class);

        // Evaluasi pada 12 November 2026
        $simulatedDate = Carbon::create(2026, 11, 12, 10, 0, 0);
        $context = $service->evaluateAparContext($apar, $simulatedDate);

        // Mode harus regular (bukan multi_period) karena Oktober 2026 berada sebelum baseline operasional resmi
        $this->assertSame('regular', $context['mode']);
        $this->assertTrue($context['backlog_period']['exists'], 'Backlog period Oktober harus diabaikan/dianggap tuntas.');
        $this->assertFalse($context['current_period']['exists'], 'Periode berjalan November belum ada pemeriksaan.');
    }

    /**
     * Uji otentikasi login via HTTP untuk Ketua K3RS dan Petugas K3RS
     */
    public function test_authentication_login_flow(): void
    {
        User::create([
            'name' => 'Ketua K3RS',
            'username' => 'ketua.k3rs',
            'email' => 'ketua.k3rs@ansarisaleh.id',
            'password' => Hash::make('KetuaK3rs26!'),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Petugas K3RS',
            'username' => 'petugas.k3rs',
            'email' => 'petugas.k3rs@ansarisaleh.id',
            'password' => Hash::make('PetugasK3rs26!'),
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        // 1. Login Ketua K3RS
        $responseAdmin = $this->post('/login', [
            'login' => 'ketua.k3rs',
            'password' => 'KetuaK3rs26!',
        ]);
        $responseAdmin->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        // 2. Akses halaman manajemen pengguna (hanya admin yang boleh)
        $respUsers = $this->get('/pengguna');
        $respUsers->assertStatus(200);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 3. Login Petugas K3RS
        $responsePetugas = $this->post('/login', [
            'login' => 'petugas.k3rs',
            'password' => 'PetugasK3rs26!',
        ]);
        $responsePetugas->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        // 4. Petugas tidak boleh mengakses halaman admin /pengguna
        $respForbidden = $this->get('/pengguna');
        $respForbidden->assertStatus(403);
    }
}
