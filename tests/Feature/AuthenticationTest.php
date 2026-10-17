<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tamu diarahkan ke halaman login saat mencoba mengakses halaman internal
     */
    public function test_guest_is_redirected_to_login_when_accessing_internal_routes(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        $aparResponse = $this->get(route('apar.index'));
        $aparResponse->assertRedirect(route('login'));

        $pemeriksaanResponse = $this->get(route('pemeriksaan.index'));
        $pemeriksaanResponse->assertRedirect(route('login'));

        $laporanResponse = $this->get(route('laporan.index'));
        $laporanResponse->assertRedirect(route('login'));
    }

    /**
     * Halaman login dapat dibuka oleh pengunjung tamu
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
        $response->assertSee('RSUD dr. H. Moch. Ansari Saleh');
    }

    /**
     * Pengguna yang sudah login dialihkan dari halaman login ke dashboard
     */
    public function test_authenticated_user_is_redirected_to_dashboard_from_login(): void
    {
        $user = $this->actingAsPetugas();

        $response = $this->get(route('login'));
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Pengguna dapat login menggunakan username
     */
    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'petugas_k3',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'login' => 'petugas_k3',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Pengguna dapat login menggunakan alamat email
     */
    public function test_users_can_authenticate_using_email(): void
    {
        $user = User::factory()->create([
            'email' => 'petugas@rsansarisaleh.id',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login'), [
            'login' => 'petugas@rsansarisaleh.id',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Login dengan kata sandi salah ditolak
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'username' => 'petugas_k3',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'login' => 'petugas_k3',
            'password' => 'kata-sandi-salah',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    /**
     * Pengguna dengan status nonaktif ditolak meskipun kata sandi benar
     */
    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->inactive()->create([
            'username' => 'petugas_nonaktif',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'login' => 'petugas_nonaktif',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    /**
     * Percobaan login berulang yang salah dibatasi oleh rate limiter
     */
    public function test_login_attempts_are_throttled_after_too_many_failures(): void
    {
        $user = User::factory()->create([
            'username' => 'target_user',
            'password' => Hash::make('password123'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'login' => 'target_user',
                'password' => 'wrong-pass',
            ]);
        }

        // Percobaan ke-6 harus ditahan oleh rate limiter
        $response = $this->post(route('login'), [
            'login' => 'target_user',
            'password' => 'wrong-pass',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertStringContainsString('Terlalu banyak percobaan masuk', session('errors')->first('login'));
    }

    /**
     * Pengguna dapat keluar dari sistem dengan aman (logout via POST)
     */
    public function test_users_can_logout(): void
    {
        $user = $this->actingAsPetugas();

        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    /**
     * Rute registrasi publik tidak tersedia di sistem
     */
    public function test_public_registration_route_does_not_exist(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(404);

        $postResponse = $this->post('/register', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
        ]);
        $postResponse->assertStatus(404);
    }
}
