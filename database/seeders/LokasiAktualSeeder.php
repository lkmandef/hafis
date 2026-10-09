<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiAktualSeeder extends Seeder
{
    /**
     * Master data 64 lokasi K3RS berdasarkan LOCATION_APAR_MASTER.md.
     * Candidate codes tetap TBD dan kolom kode dibiarkan NULL.
     */
    public const LOCATIONS_DATA = [
        ['nama' => 'POS PANTAU SECURITY', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'IGD Lt. 1', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama mengikuti data K3RS'],
        ['nama' => 'STROKE CENTER', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'IRJA Lt. 1', 'target_apar' => 6, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama mengikuti data K3RS'],
        ['nama' => 'KASIR', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'REKAM MEDIS', 'target_apar' => 3, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'FISIOTERAPI', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'IRJA LT 2', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KOMITE MEDIK', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'AUDITORIUM', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'MANAJEMEN + Rg ARSIP KEPEGAWAIAN', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama dipertahankan sebagai lokasi gabungan'],
        ['nama' => 'INSTALASI FARMASI', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'EMERALD Lt 1', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate code EMR1 (Candidate only)'],
        ['nama' => 'EMERALD Lt 2', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate code EMR2 (Candidate only)'],
        ['nama' => 'EMERALD Lt 3', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate code EMR3 (Candidate only)'],
        ['nama' => 'EMERALD Lt 4', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate code EMR4 (Candidate only)'],
        ['nama' => 'EMERALD Lt 5', 'target_apar' => 6, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate code EMR5 (Candidate only)'],
        ['nama' => 'NILAM Lt 1', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'NILAM Lt 2', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'NILAM Lt 3', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'DEPO RAWAT INAP', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'ALEXANDRI Lt 1', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'ALEXANDRI Lt 2', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'ALEXANDRI Lt 3', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'ALEXANDRI Lt 4', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'JAMRUD', 'target_apar' => 0, 'status' => Lokasi::STATUS_KOSONG, 'keterangan' => '0 APAR; lokasi kosong sah tanpa dummy'],
        ['nama' => 'YAKUT', 'target_apar' => 0, 'status' => Lokasi::STATUS_KOSONG, 'keterangan' => '0 APAR; lokasi kosong sah tanpa dummy'],
        ['nama' => 'GIOK', 'target_apar' => 0, 'status' => Lokasi::STATUS_KOSONG, 'keterangan' => '0 APAR; lokasi kosong sah tanpa dummy'],
        ['nama' => 'POLI JIWA DAN PSIKOLOGI', 'target_apar' => 3, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KUMALA Lt 1', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KUMALA Lt 2', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KUMALA Lt 3', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KUMALA Lt 4 (OK) s.d. Gedung Mutiara Lt 4', 'target_apar' => 6, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Ambigu; dipertahankan utuh tanpa split/merge'],
        ['nama' => 'DEPO OK', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KUMALA Lt 5', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'POLI ESWL', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu konfirmasi klasifikasi lokasi'],
        ['nama' => 'RADIOLOGI', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu konfirmasi klasifikasi lokasi'],
        ['nama' => 'LABORATORIUM', 'target_apar' => 3, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu konfirmasi klasifikasi lokasi'],
        ['nama' => 'MUTIARA Lt 1 HD', 'target_apar' => 7, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama mengikuti data K3RS'],
        ['nama' => 'MUTIARA Lt 2 Bayi', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama mengikuti data K3RS'],
        ['nama' => 'MUTIARA Lt 3 ICU', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama mengikuti data K3RS'],
        ['nama' => 'LAUNDRY LT 1', 'target_apar' => 4, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'CSSD LT 2', 'target_apar' => 3, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate CSD2 (Candidate only)'],
        ['nama' => 'BURN CENTER', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu konfirmasi klasifikasi lokasi'],
        ['nama' => 'INSTALASI GIZI LT 1', 'target_apar' => 5, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate GIZ1 (Candidate only)'],
        ['nama' => 'INSTALASI GIZI LT 2', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate GIZ2 (Candidate only)'],
        ['nama' => 'IT DALAM', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate ITD (Area dalam ruang IT - TBD)'],
        ['nama' => 'IT LUAR', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Candidate ITL (Area luar ruang IT / koridor - TBD)'],
        ['nama' => 'GUDANG LIMBAH B3', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'C S FREEZEER (SANITASI)', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Nama dipertahankan sesuai sumber K3RS sampai konfirmasi'],
        ['nama' => 'INSTALASI SANITASI', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'INCENERATOR', 'target_apar' => 3, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'GUDANG LOGISTIK', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'K3RS', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'IPSRS', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'GENZET', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'KAMAR JENAZAH', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
        ['nama' => 'LIFT', 'target_apar' => 8, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Ambigu; 8 APAR dipertahankan tanpa asumsi sebaran gedung'],
        ['nama' => 'KANTIN', 'target_apar' => 0, 'status' => Lokasi::STATUS_KOSONG, 'keterangan' => '0 APAR; lokasi kosong sah tanpa dummy'],
        ['nama' => 'RESEPCIONIST Lt 2', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Ambigu; dipertahankan tanpa asumsi gedung/area'],
        ['nama' => 'GAS MEDIS (BELAKANG CSSD)', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Area utilitas'],
        ['nama' => '119', 'target_apar' => 1, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Ambigu; nama dipertahankan tanpa asumsi IGD'],
        ['nama' => 'GUDANG K3RS', 'target_apar' => 0, 'status' => Lokasi::STATUS_STOK, 'keterangan' => '64 unit stok sediaan (sumber K3RS) - bukan target inspeksi aktif'],
        ['nama' => 'OK CITO', 'target_apar' => 2, 'status' => Lokasi::STATUS_AKTIF, 'keterangan' => 'Perlu finalisasi kode'],
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
                    'kode' => null, // Candidate codes tetap TBD; kode resmi belum final
                    'status' => $item['status'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}
