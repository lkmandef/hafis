<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hafis:reset-password {identifier? : Username atau alamat email pengguna}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset kata sandi akun pengguna HAFIS secara interaktif melalui terminal konsol server';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $identifier = $this->argument('identifier');

        if (empty($identifier)) {
            $identifier = trim((string) $this->ask('Masukkan username atau alamat email pengguna yang ingin direset'));
        }

        $user = User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (! $user) {
            $this->error("Pengguna dengan identitas '{$identifier}' tidak ditemukan di sistem HAFIS.");

            return self::FAILURE;
        }

        $this->info("Pengguna ditemukan: {$user->name} (@{$user->username}) - {$user->role_label}");

        $password = (string) $this->secret('Masukkan kata sandi baru (minimal 8 karakter)');
        while (strlen($password) < 8) {
            $this->error('Kata sandi harus terdiri dari minimal 8 karakter.');
            $password = (string) $this->secret('Masukkan kata sandi baru (minimal 8 karakter)');
        }

        $confirmPassword = (string) $this->secret('Konfirmasi kata sandi baru');
        while ($password !== $confirmPassword) {
            $this->error('Konfirmasi kata sandi tidak cocok.');
            $confirmPassword = (string) $this->secret('Konfirmasi kata sandi baru');
        }

        $user->password = $password;
        $user->save();

        $this->info("Kata sandi untuk pengguna '{$user->name}' berhasil diperbarui.");

        return self::SUCCESS;
    }
}
