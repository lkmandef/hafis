<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hafis:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat akun Kepala K3RS (Admin) pertama untuk sistem HAFIS secara interaktif dan aman';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('==================================================');
        $this->info(' HAFIS : Pembuatan Akun Kepala K3RS (Admin)');
        $this->info(' RSUD dr. H. Moch. Ansari Saleh Banjarmasin');
        $this->info('==================================================');

        $name = trim((string) $this->ask('Nama lengkap Kepala K3RS'));
        while (empty($name)) {
            $this->error('Nama lengkap tidak boleh kosong.');
            $name = trim((string) $this->ask('Nama lengkap Kepala K3RS'));
        }

        $username = strtolower(trim((string) $this->ask('Username login (contoh: kepala_k3rs)')));
        while (empty($username) || User::where('username', $username)->exists()) {
            if (empty($username)) {
                $this->error('Username tidak boleh kosong.');
            } else {
                $this->error("Username '{$username}' sudah digunakan oleh akun lain.");
            }
            $username = strtolower(trim((string) $this->ask('Username login (contoh: kepala_k3rs)')));
        }

        $email = trim((string) $this->ask('Alamat email (opsional, tekan enter untuk melewati)'));
        if (! empty($email)) {
            $validator = Validator::make(['email' => $email], [
                'email' => ['email', 'unique:users,email'],
            ]);
            while ($validator->fails()) {
                $this->error($validator->errors()->first('email'));
                $email = trim((string) $this->ask('Alamat email (opsional, tekan enter untuk melewati)'));
                if (empty($email)) {
                    break;
                }
                $validator = Validator::make(['email' => $email], [
                    'email' => ['email', 'unique:users,email'],
                ]);
            }
        }

        $password = (string) $this->secret('Kata sandi (minimal 8 karakter)');
        while (strlen($password) < 8) {
            $this->error('Kata sandi harus terdiri dari minimal 8 karakter.');
            $password = (string) $this->secret('Kata sandi (minimal 8 karakter)');
        }

        $confirmPassword = (string) $this->secret('Konfirmasi kata sandi');
        while ($password !== $confirmPassword) {
            $this->error('Konfirmasi kata sandi tidak cocok.');
            $confirmPassword = (string) $this->secret('Konfirmasi kata sandi');
        }

        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => ! empty($email) ? $email : null,
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->newLine();
        $this->info('Akun Kepala K3RS (Admin) berhasil dibuat.');
        $this->table(
            ['ID', 'Nama', 'Username', 'Email', 'Peran', 'Status'],
            [[$user->id, $user->name, $user->username, $user->email ?: '-', $user->role_label, 'Aktif']]
        );

        return self::SUCCESS;
    }
}
