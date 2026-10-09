<?php

namespace Database\Seeders;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AparAktualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengimpor master data APAR aktual K3RS berdasarkan LOCATION_APAR_MASTER.md.
     */
    public function run(): void
    {
        // 1. Pastikan seluruh 64 master lokasi terdaftar
        $this->call(LokasiAktualSeeder::class);

        // 2. Tangani data dummy / uji coba lama secara aman tanpa merusak riwayat
        $this->handleLegacyDummyData();

        // 3. Siapkan fallback ruangan legacy untuk integritas constraint ruangan_id
        $legacyRuangan = $this->resolveFallbackLegacyRuangan();

        // 4. Impor tepat 173 APAR terpasang/aktif dengan identifikasi deterministik
        $aparIndex = 1;

        foreach (LokasiAktualSeeder::LOCATIONS_DATA as $locationDef) {
            $targetCount = $locationDef['target_apar'];

            // Lokasi kosong (JAMRUD, YAKUT, GIOK, KANTIN) dan gudang stok (GUDANG K3RS) tidak dibuatkan APAR
            if ($targetCount <= 0) {
                continue;
            }

            $lokasi = Lokasi::where('nama', $locationDef['nama'])->firstOrFail();

            for ($i = 1; $i <= $targetCount; $i++) {
                $kode = sprintf('TEMP-APAR-%03d', $aparIndex);
                $qrCode = sprintf('HAFIS-QR-TEMP-APAR-%03d', $aparIndex);

                Apar::updateOrCreate(
                    ['kode' => $kode],
                    [
                        'nomor_register' => null,
                        'qr_code' => $qrCode,
                        'lokasi_id' => $lokasi->id,
                        'ruangan_id' => $legacyRuangan->id,
                        'posisi_detail' => null,
                        'jenis_media' => 'Dry Chemical Powder',
                        'kapasitas' => '6 kg',
                        'merek' => 'Servvo',
                        'tipe_model' => null,
                        'status' => Apar::STATUS_BAIK,
                        'tanggal_pemasangan' => null,
                        'tanggal_kadaluarsa' => null,
                        'tanggal_pemeriksaan_terakhir' => null,
                        'keterangan' => 'Master data K3RS - Phase 2 import (kode sementara)',
                    ]
                );

                $aparIndex++;
            }
        }
    }

    /**
     * Tangani data APAR dummy / pengujian lama:
     * - Record terisolasi tanpa dependensi (APAR-NONAKTIF-01) dihapus secara aman.
     * - Record yang memiliki relasi historis (pemeriksaan, detail, jadwal) diproteksi sebagai arsip legacy.
     */
    private function handleLegacyDummyData(): void
    {
        // List kode APAR dummy yang ada sebelum Phase 2
        $legacyCodes = [
            'APAR-IGD-01',
            'APAR-IGD-02',
            'APAR-NONAKTIF-01',
            'APAR-TEST-E',
            'APAR-IRJ-001',
            'Emerald-001',
        ];

        foreach ($legacyCodes as $kode) {
            $legacyApar = Apar::where('kode', $kode)->first();
            if (! $legacyApar) {
                continue;
            }

            // Hitung dependensi historis
            $hasPemeriksaan = Pemeriksaan::where('apar_id', $legacyApar->id)->exists();
            $hasJadwal = DB::table('jadwal_apars')->where('apar_id', $legacyApar->id)->exists();
            $hasPenggantian = DB::table('penggantian_apars')
                ->where('apar_lama_id', $legacyApar->id)
                ->orWhere('apar_baru_id', $legacyApar->id)
                ->exists();
            $hasTindakLanjut = DB::table('tindak_lanjuts')->where('apar_id', $legacyApar->id)->exists();

            if (! $hasPemeriksaan && ! $hasJadwal && ! $hasPenggantian && ! $hasTindakLanjut) {
                // Record uji coba terisolasi tanpa ketergantungan relasi apapun
                $legacyApar->forceDelete();
            } else {
                // Record yang memiliki riwayat pemeriksaan/jadwal diproteksi sebagai legacy
                $legacyApar->update([
                    'status' => Apar::STATUS_TIDAK_AKTIF,
                    'lokasi_id' => null,
                    'keterangan' => 'Arsip pengujian lama (legacy pre-Phase 2) - diproteksi demi integritas riwayat',
                ]);
            }
        }
    }

    /**
     * Sediakan ruangan legacy fallback untuk memenuhi constraint database ruangan_id.
     */
    private function resolveFallbackLegacyRuangan(): Ruangan
    {
        $ruangan = Ruangan::first();

        if (! $ruangan) {
            $gedung = Gedung::firstOrCreate(
                ['kode' => 'GDG-LEGACY'],
                ['nama' => 'Gedung Transisi / Legacy', 'status' => 'aktif']
            );

            $lantai = Lantai::firstOrCreate(
                ['gedung_id' => $gedung->id, 'nomor_lantai' => 1],
                ['nama' => 'Lantai 1 Legacy']
            );

            $ruangan = Ruangan::firstOrCreate(
                ['kode' => 'R-LEGACY'],
                ['lantai_id' => $lantai->id, 'nama' => 'Area Belum Terklasifikasi', 'kategori' => 'Legacy']
            );
        }

        return $ruangan;
    }
}
