<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hafis:init-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inisialisasi folder storage publik, symlink, dan salin aset denah bawaan';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('[HAFIS] Menginisialisasi direktori storage publik...');

        $directories = [
            storage_path('app/public/denah'),
            storage_path('app/public/pemeriksaan_foto'),
            storage_path('app/public/tindak_lanjut_foto'),
        ];

        foreach ($directories as $dir) {
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->line("  - Membuat direktori: <info>{$dir}</info>");
            }
        }

        // Salin denah SVG jika belum ada di storage publik
        $sourceDenah = public_path('images/denah/denah-lantai-1.svg');
        $targetDenah = storage_path('app/public/denah/denah-lantai-1.svg');

        if (! File::exists($targetDenah)) {
            if (File::exists($sourceDenah)) {
                File::copy($sourceDenah, $targetDenah);
                $this->info("  - Denah bawaan berhasil disalin ke: <info>{$targetDenah}</info>");
            } else {
                $this->warn("  - File denah sumber tidak ditemukan di: {$sourceDenah}");
            }
        } else {
            $this->line('  - File denah di storage publik sudah tersedia.');
        }

        // Buat symlink storage
        $this->call('storage:link');

        $this->info('[HAFIS] Inisialisasi storage selesai.');

        return self::SUCCESS;
    }
}

