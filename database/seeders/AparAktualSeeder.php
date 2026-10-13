<?php

namespace Database\Seeders;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AparAktualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengimpor master data APAR aktual K3RS berdasarkan LOCATION_APAR_MASTER.md
     * dan LOCATION_CODE_MAPPING.md dengan format definitif APAR-{KODE_LOKASI}-{NOMOR_URUT}.
     */
    public function run(): void
    {
        // 1. Pastikan seluruh 64 master lokasi terdaftar beserta kode definitifnya
        $this->call(LokasiAktualSeeder::class);

        // 2. Bersihkan record dummy legacy lama secara aman dan permanen jika masih ada
        $this->purgeLegacyDummyData();

        // 3. Siapkan fallback ruangan legacy untuk integritas constraint ruangan_id
        $legacyRuangan = $this->resolveFallbackLegacyRuangan();

        // 4. Impor tepat 173 APAR terpasang/aktif dengan identifikasi deterministik
        foreach (LokasiAktualSeeder::LOCATIONS_DATA as $locationDef) {
            $targetCount = $locationDef['target_apar'];

            // Lokasi kosong (JAMRUD, YAKUT, GIOK, KANTIN) dan gudang stok (GUDANG K3RS) tidak dibuatkan APAR
            if ($targetCount <= 0) {
                continue;
            }

            $lokasi = Lokasi::where('nama', $locationDef['nama'])->firstOrFail();

            for ($i = 1; $i <= $targetCount; $i++) {
                $kode = sprintf('APAR-%s-%02d', $lokasi->kode, $i);
                $qrCode = sprintf('HAFIS-QR-%s', $kode);

                // Cari APAR yang sudah ada untuk posisi ini
                $apar = Apar::where('kode', $kode)->first();

                if (! $apar) {
                    // Cari jika masih menggunakan kode sementara TEMP-APAR-xxx
                    $apar = Apar::where('lokasi_id', $lokasi->id)
                        ->where('kode', 'like', 'TEMP-APAR-%')
                        ->orderBy('id')
                        ->skip($i - 1)
                        ->first();
                }

                if (! $apar) {
                    // Cari berdasarkan urutan ID di lokasi tersebut
                    $apar = Apar::where('lokasi_id', $lokasi->id)
                        ->orderBy('id')
                        ->skip($i - 1)
                        ->first();
                }

                $itemSpec = $locationDef['items'][$i - 1] ?? [
                    'media' => 'Dry Chemical Powder',
                    'kapasitas' => '6 kg',
                ];

                if ($apar) {
                    // Update kode & identitas struktural serta atribut fisik terverifikasi,
                    // TANPA menimpa status operasional, tanggal periksa, atau koordinat
                    $apar->update([
                        'kode' => $kode,
                        'qr_code' => $qrCode,
                        'lokasi_id' => $lokasi->id,
                        'ruangan_id' => $legacyRuangan->id,
                        'jenis_media' => $itemSpec['media'],
                        'kapasitas' => $itemSpec['kapasitas'],
                        'merek' => null, // Dikosongkan karena tidak ada data merek resmi di sumber
                    ]);
                } else {
                    // Buat baru jika belum ada record sama sekali
                    Apar::create([
                        'kode' => $kode,
                        'qr_code' => $qrCode,
                        'lokasi_id' => $lokasi->id,
                        'ruangan_id' => $legacyRuangan->id,
                        'posisi_detail' => null,
                        'jenis_media' => $itemSpec['media'],
                        'kapasitas' => $itemSpec['kapasitas'],
                        'merek' => null,
                        'tipe_model' => null,
                        'status' => Apar::STATUS_BAIK,
                        'tanggal_pemasangan' => null,
                        'tanggal_kadaluarsa' => null,
                        'tanggal_pemeriksaan_terakhir' => null,
                        'keterangan' => 'Master data K3RS APAR terpasang',
                    ]);
                }
            }
        }
    }

    /**
     * Bersihkan secara permanen record dummy lama beserta seluruh riwayat dependennya
     * agar tidak terjadi resurrect dummy APAR saat seeder dijalankan ulang.
     */
    private function purgeLegacyDummyData(): void
    {
        $legacyCodes = [
            'APAR-IGD-01',
            'APAR-IGD-02',
            'APAR-NONAKTIF-01',
            'APAR-TEST-E',
            'APAR-IRJ-001',
            'Emerald-001',
        ];

        $legacyApars = Apar::whereIn('kode', $legacyCodes)->get();
        if ($legacyApars->isEmpty()) {
            return;
        }

        $dummyIds = $legacyApars->pluck('id')->toArray();

        DB::transaction(function () use ($dummyIds) {
            $pemeriksaanIds = DB::table('pemeriksaans')->whereIn('apar_id', $dummyIds)->pluck('id')->toArray();
            if (! empty($pemeriksaanIds)) {
                DB::table('pemeriksaan_details')->whereIn('pemeriksaan_id', $pemeriksaanIds)->delete();
                DB::table('pemeriksaans')->whereIn('id', $pemeriksaanIds)->delete();
            }

            DB::table('jadwal_apars')->whereIn('apar_id', $dummyIds)->delete();
            DB::table('tindak_lanjuts')->whereIn('apar_id', $dummyIds)->delete();
            DB::table('penggantian_apars')
                ->whereIn('apar_lama_id', $dummyIds)
                ->orWhereIn('apar_baru_id', $dummyIds)
                ->delete();

            Apar::whereIn('id', $dummyIds)->forceDelete();
        });
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
