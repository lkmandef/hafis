<?php

namespace Database\Seeders;

use App\Models\MasterChecklist;
use Illuminate\Database\Seeder;

class MasterChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'nama' => 'Tekanan',
                'deskripsi_bantuan' => 'Jarum manometer berada tepat di dalam zona hijau (tekanan kerja normal).',
                'urutan' => 1,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'nama' => 'Pin/Segel',
                'deskripsi_bantuan' => 'Pin pengaman terpasang kokoh dan segel pengaman (safety seal) masih utuh tidak putus.',
                'urutan' => 2,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'nama' => 'Handle',
                'deskripsi_bantuan' => 'Tuas penekan (handle lever) dan pegangan bersih, tidak macet, dan tidak bengkok.',
                'urutan' => 3,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'nama' => 'Kondisi Fisik',
                'deskripsi_bantuan' => 'Tabung bebas korosi, karat, penyok, serta stiker instruksi dan identitas terbaca jelas.',
                'urutan' => 4,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'nama' => 'Nozel',
                'deskripsi_bantuan' => 'Corong semprot (nozzle) dan selang lentur, tidak retak, dan bebas dari sumbatan kotoran.',
                'urutan' => 5,
                'is_active' => true,
                'is_default' => true,
            ],
        ];

        foreach ($defaults as $item) {
            MasterChecklist::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
