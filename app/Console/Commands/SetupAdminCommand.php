<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SetupAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hafis:setup-admin
                            {--admin-name= : Nama lengkap Admin}
                            {--admin-username= : Username Admin (default: admin)}
                            {--admin-email= : Email Admin}
                            {--admin-password= : Password Admin (minimal 8 karakter)}
                            {--petugas-name= : Nama lengkap Petugas}
                            {--petugas-username= : Username Petugas (default: petugas)}
                            {--petugas-email= : Email Petugas}
                            {--petugas-password= : Password Petugas (minimal 8 karakter)}
                            {--force : Timpa password jika akun sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup akun awal Admin dan Petugas HAFIS secara interaktif atau non-interaktif';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('==================================================');
        $this->info(' HAFIS : Setup Akun Admin dan Petugas Lapangan');
        $this->info(' RSUD dr. H. Moch. Ansari Saleh Banjarmasin');
        $this->info('==================================================');

        $force = (bool) $this->option('force');
        $isInteractive = $this->input->isInteractive();

        // 1. Setup Admin
        $this->info(PHP_EOL . '--- 1. Akun Admin (Kepala K3RS) ---');
        $adminResult = $this->setupUser(
            role: User::ROLE_ADMIN,
            defaultName: 'Kepala K3RS',
            defaultUsername: 'admin',
            optName: $this->option('admin-name'),
            optUsername: $this->option('admin-username'),
            optEmail: $this->option('admin-email') ?: env('HAFIS_ADMIN_EMAIL'),
            optPassword: $this->option('admin-password') ?: env('HAFIS_ADMIN_PASSWORD'),
            isInteractive: $isInteractive,
            force: $force
        );

        if ($adminResult === false) {
            $this->error('Gagal menyiapkan akun Admin.');
            return self::FAILURE;
        }

        // 2. Setup Petugas
        $this->info(PHP_EOL . '--- 2. Akun Petugas Lapangan ---');
        $setupPetugas = true;

        if ($isInteractive && empty($this->option('petugas-email')) && empty($this->option('petugas-password')) && empty(env('HAFIS_PETUGAS_PASSWORD'))) {
            $setupPetugas = $this->confirm('Apakah Anda ingin langsung membuat akun Petugas lapangan sekarang?', true);
        }

        if ($setupPetugas) {
            $petugasResult = $this->setupUser(
                role: User::ROLE_PETUGAS,
                defaultName: 'Petugas Lapangan K3RS',
                defaultUsername: 'petugas',
                optName: $this->option('petugas-name'),
                optUsername: $this->option('petugas-username'),
                optEmail: $this->option('petugas-email') ?: env('HAFIS_PETUGAS_EMAIL'),
                optPassword: $this->option('petugas-password') ?: env('HAFIS_PETUGAS_PASSWORD'),
                isInteractive: $isInteractive,
                force: $force
            );

            if ($petugasResult === false) {
                $this->warn('Peringatan: Akun Petugas lapangan tidak dibuat atau dilewati.');
            }
        } else {
            $this->line('Pembuatan akun Petugas lapangan dilewati.');
        }

        $this->newLine();
        $this->info('Status Akun Pengguna Saat Ini:');
        $users = User::all(['id', 'name', 'username', 'email', 'role', 'is_active']);
        $rows = $users->map(fn ($u) => [
            $u->id,
            $u->name,
            $u->username,
            $u->email ?: '-',
            $u->role === User::ROLE_ADMIN ? 'Admin (Kepala K3RS)' : 'Petugas',
            $u->is_active ? 'Aktif' : 'Nonaktif',
        ])->toArray();

        $this->table(['ID', 'Nama', 'Username', 'Email', 'Peran', 'Status'], $rows);

        return self::SUCCESS;
    }

    /**
     * Tangani pembuatan atau pembaruan akun pengguna
     */
    protected function setupUser(
        string $role,
        string $defaultName,
        string $defaultUsername,
        ?string $optName,
        ?string $optUsername,
        ?string $optEmail,
        ?string $optPassword,
        bool $isInteractive,
        bool $force
    ): bool {
        $roleLabel = ($role === User::ROLE_ADMIN) ? 'Admin' : 'Petugas';

        // Tentukan data dari interaktif atau opsi/env
        if ($isInteractive && empty($optPassword)) {
            // Mode Interaktif
            $name = trim((string) $this->ask("Nama lengkap {$roleLabel}", $defaultName));
            while (empty($name)) {
                $this->error('Nama lengkap tidak boleh kosong.');
                $name = trim((string) $this->ask("Nama lengkap {$roleLabel}", $defaultName));
            }

            $username = strtolower(trim((string) $this->ask("Username {$roleLabel}", $defaultUsername)));
            while (empty($username)) {
                $this->error('Username tidak boleh kosong.');
                $username = strtolower(trim((string) $this->ask("Username {$roleLabel}", $defaultUsername)));
            }

            $email = trim((string) $this->ask("Email {$roleLabel} (opsional, tekan enter jika kosong)", $optEmail ?: ''));
            if (! empty($email)) {
                $validator = Validator::make(['email' => $email], ['email' => 'email']);
                while ($validator->fails()) {
                    $this->error($validator->errors()->first('email'));
                    $email = trim((string) $this->ask("Email {$roleLabel} (opsional, tekan enter jika kosong)"));
                    if (empty($email)) {
                        break;
                    }
                    $validator = Validator::make(['email' => $email], ['email' => 'email']);
                }
            }

            $password = (string) $this->secret("Kata sandi {$roleLabel} (minimal 8 karakter)");
            while (strlen($password) < 8) {
                $this->error('Kata sandi harus terdiri dari minimal 8 karakter. Tidak ada password bawaan.');
                $password = (string) $this->secret("Kata sandi {$roleLabel} (minimal 8 karakter)");
            }

            $confirmPassword = (string) $this->secret('Konfirmasi kata sandi');
            while ($password !== $confirmPassword) {
                $this->error('Konfirmasi kata sandi tidak cocok.');
                $confirmPassword = (string) $this->secret('Konfirmasi kata sandi');
            }
        } else {
            // Mode Non-Interaktif / Opsi
            $name = $optName ?: $defaultName;
            $username = $optUsername ?: $defaultUsername;
            $email = $optEmail ?: null;
            $password = $optPassword;

            if (empty($password)) {
                $this->error("Kata sandi untuk {$roleLabel} tidak diberikan. Atur opsi --{$role}-password atau variabel env.");
                return false;
            }

            if (strlen($password) < 8) {
                $this->error("Kata sandi {$roleLabel} harus minimal 8 karakter.");
                return false;
            }

            if (! empty($email)) {
                $validator = Validator::make(['email' => $email], ['email' => 'email']);
                if ($validator->fails()) {
                    $this->error("Format email '{$email}' tidak valid.");
                    return false;
                }
            }
        }

        // Cek apakah akun dengan username atau email atau peran sudah ada
        $existingQuery = User::where('username', $username);
        if (! empty($email)) {
            $existingQuery->orWhere('email', $email);
        }
        $existing = $existingQuery->first();

        if ($existing) {
            if (! $force) {
                $this->warn("Akun {$roleLabel} ({$existing->username} / " . ($existing->email ?: '-') . ") sudah ada. Dilewati (gunakan --force untuk memperbarui).");
                return true;
            }

            $existing->name = $name;
            $existing->username = $username;
            $existing->email = ! empty($email) ? $email : null;
            $existing->password = $password; // Auto-hashed by casts()
            $existing->role = $role;
            $existing->is_active = true;
            $existing->save();

            $this->info("Akun {$roleLabel} ({$existing->username}) berhasil diperbarui (--force).");
            return true;
        }

        // Buat akun baru
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => ! empty($email) ? $email : null,
            'password' => $password,
            'role' => $role,
            'is_active' => true,
        ]);

        $this->info("Akun {$roleLabel} ({$user->username}) berhasil dibuat.");
        return true;
    }
}

