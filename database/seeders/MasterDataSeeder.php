<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MasterDataSeeder extends Seeder
{
    /**
     * Muat seluruh data master HAFIS dari file CSV berurutan sesuai dependensi.
     * Idempoten: hanya memasukkan baris jika belum ada (tidak pernah menimpa data yang sudah ada).
     */
    public function run(): void
    {
        $dataPath = database_path('seeders/data');

        if (! File::isDirectory($dataPath)) {
            $this->command->error("[MasterDataSeeder] Direktori data seeder tidak ditemukan: {$dataPath}");

            return;
        }

        // Jika ini instalasi fresh (tabel apars & pemeriksaans masih kosong),
        // bersihkan baris placeholder sementara hasil migrasi rekonsiliasi agar integritas ID 100% sinkron dengan CSV
        $isFreshSetup = (DB::table('apars')->count() === 0 && DB::table('pemeriksaans')->count() === 0);

        // Urutan dependensi tabel master:
        // 1. gedungs (tanpa dependensi)
        // 2. lantais (bergantung gedung_id)
        // 3. ruangans (bergantung lantai_id)
        // 4. lokasis (bergantung gedung_id, lantai_id, ruangan_id)
        // 5. denahs (bergantung lantai_id)
        // 6. master_checklists (tanpa dependensi)
        // 7. apars (bergantung ruangan_id, lokasi_id)
        $tables = [
            'gedungs' => ['check_columns' => ['id', 'kode']],
            'lantais' => ['check_columns' => ['id']],
            'ruangans' => ['check_columns' => ['id']],
            'lokasis' => ['check_columns' => ['id']],
            'denahs' => ['check_columns' => ['id']],
            'master_checklists' => ['check_columns' => ['id']],
            'apars' => ['check_columns' => ['id', 'kode', 'qr_code']],
        ];

        DB::transaction(function () use ($dataPath, $tables, $isFreshSetup) {
            if ($isFreshSetup) {
                // Hapus data placeholder parsial migrasi berurutan dari child ke parent
                DB::table('lokasis')->delete();
                DB::table('ruangans')->delete();
                DB::table('lantais')->delete();
                DB::table('gedungs')->delete();
            }

            foreach ($tables as $table => $config) {
                $file = "{$dataPath}/{$table}.csv";
                if (! File::exists($file)) {
                    $this->command->warn("[MasterDataSeeder] File CSV untuk tabel '{$table}' tidak ditemukan: {$file}");

                    continue;
                }

                $this->seedTableFromCsv($table, $file, $config['check_columns']);
            }
        });

        $this->command->info('[MasterDataSeeder] Seluruh data master berhasil diproses secara idempoten.');
    }

    /**
     * Parse dan masukkan baris CSV ke tabel database jika belum ada.
     *
     * @param  array<int, string>  $checkColumns
     */
    protected function seedTableFromCsv(string $table, string $filePath, array $checkColumns): void
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            $this->command->error("[MasterDataSeeder] Gagal membuka file: {$filePath}");

            return;
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);

            return;
        }

        // Bersihkan UTF-8 BOM pada header kolom pertama bila ada
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $loaded = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $record = [];
            foreach ($headers as $index => $header) {
                $val = $row[$index];
                // Konversi string kosong menjadi NULL untuk menjaga integritas foreign key & tipe data
                $record[$header] = ($val === '' || $val === null) ? null : $val;
            }

            // Pengecekan keberadaan baris: tidak pernah menimpa data yang sudah ada
            $query = DB::table($table);
            $query->where(function ($q) use ($checkColumns, $record) {
                $first = true;
                foreach ($checkColumns as $col) {
                    if (isset($record[$col]) && $record[$col] !== null) {
                        if ($first) {
                            $q->where($col, $record[$col]);
                            $first = false;
                        } else {
                            $q->orWhere($col, $record[$col]);
                        }
                    }
                }
            });

            if ($query->exists()) {
                $skipped++;
            } else {
                DB::table($table)->insert($record);
                $loaded++;
            }
        }

        fclose($handle);

        $this->command->line(sprintf(
            '  - %-18s : <info>%3d dimuat</info>, <comment>%3d dilewati</comment>',
            $table,
            $loaded,
            $skipped
        ));
    }
}
