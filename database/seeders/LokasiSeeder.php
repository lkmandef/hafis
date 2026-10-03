<?php

namespace Database\Seeders;

use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Data awal/draft hasil konsultasi internal K3RS
     */
    public function run(): void
    {
        $gedungs = [
            [
                'kode' => 'GDG-IGD',
                'nama' => 'Gedung IGD',
                'keterangan' => 'Draft konsultasi: 1 lantai, meliputi Ruang IGD dan Stroke Center.',
                'lantais' => [
                    [
                        'nomor_lantai' => 1,
                        'nama' => 'Lantai 1',
                        'keterangan' => 'Lantai operasional IGD dan Stroke Center',
                        'ruangans' => [
                            ['kode' => 'IGD-01', 'nama' => 'Ruang IGD', 'kategori' => 'Pelayanan Gawat Darurat'],
                            ['kode' => 'IGD-02', 'nama' => 'Stroke Center', 'kategori' => 'Pelayanan Khusus'],
                        ],
                    ],
                ],
            ],
            [
                'kode' => 'GDG-IRJ',
                'nama' => 'Gedung Instalasi Rawat Jalan',
                'keterangan' => 'Draft konsultasi: 2 lantai (Poliklinis & Manajemen). Menunggu konfirmasi pembagian lantai.',
                'lantais' => [
                    [
                        'nomor_lantai' => 1,
                        'nama' => 'Lantai 1',
                        'keterangan' => 'Draft: Area Poliklinis rawat jalan',
                        'ruangans' => [
                            ['kode' => 'IRJ-POLI', 'nama' => 'Poliklinis', 'kategori' => 'Pelayanan Rawat Jalan'],
                        ],
                    ],
                    [
                        'nomor_lantai' => 2,
                        'nama' => 'Lantai 2',
                        'keterangan' => 'Draft: Area Manajemen / Administrasi',
                        'ruangans' => [
                            ['kode' => 'IRJ-MNJ', 'nama' => 'Manajemen', 'kategori' => 'Administrasi & Manajemen'],
                        ],
                    ],
                ],
            ],
            [
                'kode' => 'GDG-EMR',
                'nama' => 'Gedung Emerald',
                'keterangan' => 'Draft konsultasi: 5 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                    ['nomor_lantai' => 3, 'nama' => 'Lantai 3', 'keterangan' => null],
                    ['nomor_lantai' => 4, 'nama' => 'Lantai 4', 'keterangan' => null],
                    ['nomor_lantai' => 5, 'nama' => 'Lantai 5', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-NLM',
                'nama' => 'Gedung Nilam',
                'keterangan' => 'Draft konsultasi: 3 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                    ['nomor_lantai' => 3, 'nama' => 'Lantai 3', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-ALX',
                'nama' => 'Gedung Alexandri',
                'keterangan' => 'Draft konsultasi: 4 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                    ['nomor_lantai' => 3, 'nama' => 'Lantai 3', 'keterangan' => null],
                    ['nomor_lantai' => 4, 'nama' => 'Lantai 4', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-GIK',
                'nama' => 'Gedung Giok',
                'keterangan' => 'Draft konsultasi: 2 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-LOG',
                'nama' => 'Gedung Gudang Logistik',
                'keterangan' => 'Draft konsultasi: 1 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Gudang penyimpanan logistik'],
                ],
            ],
            [
                'kode' => 'GDG-IPS',
                'nama' => 'Gedung IPSRS',
                'keterangan' => 'Draft konsultasi: 1 lantai (Instalasi Pemeliharaan Sarana RS).',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Bengkel teknis dan pemeliharaan sarana'],
                ],
            ],
            [
                'kode' => 'GDG-GIZ',
                'nama' => 'Gedung Gizi',
                'keterangan' => 'Draft konsultasi: 2 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Instalasi Dapur & Pengolahan Makanan'],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => 'Ruang Ahli Gizi & Penyimpanan'],
                ],
            ],
            [
                'kode' => 'GDG-CSD',
                'nama' => 'Gedung CSSD',
                'keterangan' => 'Draft konsultasi: 3 lantai (Central Sterile Supply Department).',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                    ['nomor_lantai' => 3, 'nama' => 'Lantai 3', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-IBS',
                'nama' => 'Gedung IBS',
                'keterangan' => 'Draft konsultasi: 4 lantai (Instalasi Bedah Sentral).',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                    ['nomor_lantai' => 3, 'nama' => 'Lantai 3', 'keterangan' => null],
                    ['nomor_lantai' => 4, 'nama' => 'Lantai 4', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-KML',
                'nama' => 'Gedung Kumala',
                'keterangan' => 'Draft konsultasi: 5 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => null],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => null],
                    ['nomor_lantai' => 3, 'nama' => 'Lantai 3', 'keterangan' => null],
                    ['nomor_lantai' => 4, 'nama' => 'Lantai 4', 'keterangan' => null],
                    ['nomor_lantai' => 5, 'nama' => 'Lantai 5', 'keterangan' => null],
                ],
            ],
            [
                'kode' => 'GDG-DGN',
                'nama' => 'Gedung Diagnostik',
                'keterangan' => 'Draft konsultasi: Meliputi Radiologi dan Laboratorium. Disimpan dalam struktur Ruangan/Area sambil menunggu konfirmasi K3RS terkait pembagian lantai spesifik.',
                'lantais' => [
                    [
                        'nomor_lantai' => 1,
                        'nama' => 'Lantai 1',
                        'keterangan' => 'Lantai dasar penunjang diagnostik (Menunggu konfirmasi pemisahan lantai)',
                        'ruangans' => [
                            ['kode' => 'DGN-RAD', 'nama' => 'Radiologi', 'kategori' => 'Penunjang Medis (Draft)'],
                            ['kode' => 'DGN-LAB', 'nama' => 'Laboratorium', 'kategori' => 'Penunjang Medis (Draft)'],
                        ],
                    ],
                ],
            ],
            [
                'kode' => 'GDG-FRM',
                'nama' => 'Gedung Farmasi',
                'keterangan' => 'Draft konsultasi: 2 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Depo Farmasi & Pelayanan Resep'],
                    ['nomor_lantai' => 2, 'nama' => 'Lantai 2', 'keterangan' => 'Gudang Farmasi & Ruang Racik'],
                ],
            ],
            [
                'kode' => 'GDG-JNZ',
                'nama' => 'Gedung Jenazah',
                'keterangan' => 'Draft konsultasi: 1 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Instalasi Forensik & Pemulasaraan Jenazah'],
                ],
            ],
            [
                'kode' => 'GDG-DNS',
                'nama' => 'Gedung Dinas',
                'keterangan' => 'Draft konsultasi: 1 lantai.',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Mess / Rumah Dinas Staf'],
                ],
            ],
            [
                'kode' => 'GDG-INS',
                'nama' => 'Gedung Insinerator',
                'keterangan' => 'Draft konsultasi: 1 lantai (Area Pengolahan Limbah B3).',
                'lantais' => [
                    ['nomor_lantai' => 1, 'nama' => 'Lantai 1', 'keterangan' => 'Area Insinerator & Pengolahan Limbah Medis'],
                ],
            ],
        ];

        foreach ($gedungs as $gData) {
            $lantais = $gData['lantais'] ?? [];
            unset($gData['lantais']);

            $gedung = Gedung::updateOrCreate(
                ['kode' => $gData['kode']],
                $gData
            );

            foreach ($lantais as $lData) {
                $ruangans = $lData['ruangans'] ?? [];
                unset($lData['ruangans']);

                $lData['gedung_id'] = $gedung->id;
                $lantai = Lantai::updateOrCreate(
                    [
                        'gedung_id' => $gedung->id,
                        'nomor_lantai' => $lData['nomor_lantai'],
                    ],
                    $lData
                );

                foreach ($ruangans as $rData) {
                    $rData['lantai_id'] = $lantai->id;
                    Ruangan::updateOrCreate(
                        [
                            'lantai_id' => $lantai->id,
                            'nama' => $rData['nama'],
                        ],
                        $rData
                    );
                }
            }
        }
    }
}
