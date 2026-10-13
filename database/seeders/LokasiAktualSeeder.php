<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiAktualSeeder extends Seeder
{
    /**
     * Master data 64 lokasi K3RS berdasarkan LOCATION_APAR_MASTER.md dan Tabel 12 Laporan Triwulan Ke-3 2026.
     * Rincian agregat media dan kapasitas per lokasi dialokasikan secara administratif dan deterministik.
     */
    public const LOCATIONS_DATA = [
        [
            'nama' => 'POS PANTAU SECURITY',
            'kode' => 'SEC',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Pos Keamanan / Security',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'IGD Lt. 1',
            'kode' => 'IGD1',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Gawat Darurat Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'STROKE CENTER',
            'kode' => 'STR',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Unit Layanan Stroke Center',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'IRJA Lt. 1',
            'kode' => 'IRJ1',
            'target_apar' => 6,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Rawat Jalan Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'KASIR',
            'kode' => 'KAS',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Loket Kasir Pembayaran',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'REKAM MEDIS',
            'kode' => 'RM',
            'target_apar' => 3,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Rekam Medis',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'FISIOTERAPI',
            'kode' => 'FIS',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Rehabilitasi Medik / Fisioterapi',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
            ],
        ],
        [
            'nama' => 'IRJA LT 2',
            'kode' => 'IRJ2',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Rawat Jalan Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'KOMITE MEDIK',
            'kode' => 'KM',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Ruang Komite Medik',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'AUDITORIUM',
            'kode' => 'AUD',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Pertemuan Auditorium',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
            ],
        ],
        [
            'nama' => 'MANAJEMEN + Rg ARSIP KEPEGAWAIAN',
            'kode' => 'MNJ',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Manajemen & Arsip Kepegawaian',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'INSTALASI FARMASI',
            'kode' => 'FAR',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Farmasi Sentral',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'EMERALD Lt 1',
            'kode' => 'EMR1',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Emerald Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'EMERALD Lt 2',
            'kode' => 'EMR2',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Emerald Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'EMERALD Lt 3',
            'kode' => 'EMR3',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Emerald Lantai 3',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'EMERALD Lt 4',
            'kode' => 'EMR4',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Emerald Lantai 4',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'EMERALD Lt 5',
            'kode' => 'EMR5',
            'target_apar' => 6,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Emerald Lantai 5',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'NILAM Lt 1',
            'kode' => 'NLM1',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Nilam Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'NILAM Lt 2',
            'kode' => 'NLM2',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Nilam Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'NILAM Lt 3',
            'kode' => 'NLM3',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Nilam Lantai 3',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'DEPO RAWAT INAP',
            'kode' => 'DRI',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Depo Farmasi Rawat Inap',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'ALEXANDRI Lt 1',
            'kode' => 'ALX1',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Alexandri Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'ALEXANDRI Lt 2',
            'kode' => 'ALX2',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Alexandri Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'ALEXANDRI Lt 3',
            'kode' => 'ALX3',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Alexandri Lantai 3',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'ALEXANDRI Lt 4',
            'kode' => 'ALX4',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Alexandri Lantai 4',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'JAMRUD',
            'kode' => 'JMR',
            'target_apar' => 0,
            'status' => Lokasi::STATUS_KOSONG,
            'keterangan' => 'Ruang Jamrud (Master kosong, 0 APAR)',
            'items' => [
            ],
        ],
        [
            'nama' => 'YAKUT',
            'kode' => 'YKT',
            'target_apar' => 0,
            'status' => Lokasi::STATUS_KOSONG,
            'keterangan' => 'Ruang Yakut (Master kosong, 0 APAR)',
            'items' => [
            ],
        ],
        [
            'nama' => 'GIOK',
            'kode' => 'GIO',
            'target_apar' => 0,
            'status' => Lokasi::STATUS_KOSONG,
            'keterangan' => 'Ruang Giok (Master kosong, 0 APAR)',
            'items' => [
            ],
        ],
        [
            'nama' => 'POLI JIWA DAN PSIKOLOGI',
            'kode' => 'JIW',
            'target_apar' => 3,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Poliklinik Jiwa dan Psikologi',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'KUMALA Lt 1',
            'kode' => 'KML1',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Kumala Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'KUMALA Lt 2',
            'kode' => 'KML2',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Kumala Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'KUMALA Lt 3',
            'kode' => 'KML3',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Kumala Lantai 3',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4',
            'kode' => 'KML4',
            'target_apar' => 6,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Area Transisi Kumala Lt 4 s.d. Mutiara Lt 4 (dipertahankan utuh)',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'DEPO OK',
            'kode' => 'DOK',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Depo Farmasi Kamar Operasi (OK)',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'KUMALA Lt 5',
            'kode' => 'KML5',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Kumala Lantai 5',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'POLI ESWL',
            'kode' => 'ESWL',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Poliklinik ESWL (Pemecah Batu Ginjal)',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
            ],
        ],
        [
            'nama' => 'RADIOLOGI',
            'kode' => 'RAD',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Radiologi & Pencitraan',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'LABORATORIUM',
            'kode' => 'LAB',
            'target_apar' => 3,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Laboratorium Patologi & Klinis',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'MUTIARA Lt 1 HD',
            'kode' => 'MTR1',
            'target_apar' => 7,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Mutiara Lantai 1 Unit Hemodialisa',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'MUTIARA Lt 2 Bayi',
            'kode' => 'MTR2',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Mutiara Lantai 2 Ruang Perawatan Bayi',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'MUTIARA Lt 3 ICU',
            'kode' => 'MTR3',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gedung Mutiara Lantai 3 Ruang ICU',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'LAUNDRY LT 1',
            'kode' => 'LND1',
            'target_apar' => 4,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Laundry / Binatu Rumah Sakit',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '25 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
            ],
        ],
        [
            'nama' => 'CSSD LT 2',
            'kode' => 'CSD2',
            'target_apar' => 3,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Central Sterile Services Department Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
            ],
        ],
        [
            'nama' => 'BURN CENTER',
            'kode' => 'BRN',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Unit Perawatan Luka Bakar (Burn Center)',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'INSTALASI GIZI LT 1',
            'kode' => 'GIZ1',
            'target_apar' => 5,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Gizi & Dapur Utama Lantai 1',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '25 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
            ],
        ],
        [
            'nama' => 'INSTALASI GIZI LT 2',
            'kode' => 'GIZ2',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Gizi Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'IT DALAM',
            'kode' => 'ITD',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Ruang Server & IT Internal',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '7 kg'],
            ],
        ],
        [
            'nama' => 'IT LUAR',
            'kode' => 'ITL',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Selasar / Area Koridor Luar IT',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'GUDANG LIMBAH B3',
            'kode' => 'LB3',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gudang Penyimpanan Sementara Limbah Medis B3',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'C S FREEZEER (SANITASI)',
            'kode' => 'CSF',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Cold Storage / Freezer Unit Sanitasi',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'INSTALASI SANITASI',
            'kode' => 'SAN',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Kantor Instalasi Kesehatan Lingkungan & Sanitasi',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'INCENERATOR',
            'kode' => 'INC',
            'target_apar' => 3,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Area Pengolahan Sampah Medis Incenerator',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
            ],
        ],
        [
            'nama' => 'GUDANG LOGISTIK',
            'kode' => 'LOG',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Gudang Logistik Umum & Barang',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'K3RS',
            'kode' => 'K3RS',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Kantor / Sekretariat Komite K3RS',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
            ],
        ],
        [
            'nama' => 'IPSRS',
            'kode' => 'IPSR',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Pemeliharaan Sarana RS (IPSRS)',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'GENZET',
            'kode' => 'GEN',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Ruang Mesin Pembangkit Listrik (Genset)',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '25 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '5 kg'],
            ],
        ],
        [
            'nama' => 'KAMAR JENAZAH',
            'kode' => 'JNZ',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Instalasi Forensik & Kamar Jenazah',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'LIFT',
            'kode' => 'LFT',
            'target_apar' => 8,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Area Penempatan APAR Selasar/Lobi Lift Vertikal',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
        [
            'nama' => 'KANTIN',
            'kode' => 'KTN',
            'target_apar' => 0,
            'status' => Lokasi::STATUS_KOSONG,
            'keterangan' => 'Area Kantin Rumah Sakit (Master kosong, 0 APAR)',
            'items' => [
            ],
        ],
        [
            'nama' => 'RESEPCIONIST Lt 2',
            'kode' => 'RSP2',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Area Meja Resepsionis Lantai 2',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'GAS MEDIS (BELAKANG CSSD)',
            'kode' => 'GAS',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Sentral Distribusi Gas Medis Belakang CSSD',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => '119',
            'kode' => '119',
            'target_apar' => 1,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Pos / Pusat Layanan Gawat Darurat PSC 119',
            'items' => [
                ['media' => 'Dry Chemical Powder', 'kapasitas' => '6 kg'],
            ],
        ],
        [
            'nama' => 'GUDANG K3RS',
            'kode' => 'GK3',
            'target_apar' => 0,
            'status' => Lokasi::STATUS_STOK,
            'keterangan' => '64 unit stok sediaan (sumber K3RS) - bukan target inspeksi aktif',
            'items' => [
            ],
        ],
        [
            'nama' => 'OK CITO',
            'kode' => 'OKC',
            'target_apar' => 2,
            'status' => Lokasi::STATUS_AKTIF,
            'keterangan' => 'Kamar Operasi Cito / Bedah Darurat',
            'items' => [
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
                ['media' => 'CO2 (Carbon Dioxide)', 'kapasitas' => '3 kg'],
            ],
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::LOCATIONS_DATA as $item) {
            Lokasi::updateOrCreate(
                ['nama' => $item['nama']],
                [
                    'kode' => $item['kode'],
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}
