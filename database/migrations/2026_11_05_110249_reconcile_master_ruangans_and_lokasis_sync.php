<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Izinkan lantai_id bernilai null pada ruangans untuk gedung/fasilitas non-lantai (seperti lift, jamrud, kantin)
        Schema::table('ruangans', function (Blueprint $table) {
            $table->foreignId('lantai_id')->nullable()->change();
        });

        // 2. Tambah kolom ruangan_id pada tabel lokasis jika belum ada
        if (! Schema::hasColumn('lokasis', 'ruangan_id')) {
            Schema::table('lokasis', function (Blueprint $table) {
                $table->foreignId('ruangan_id')->nullable()->after('lantai_id')->constrained('ruangans')->nullOnDelete();
            });
        }

        // 3. Bersihkan data debug sementara jika ada
        DB::table('lokasis')->where('kode', 'LOK-UJI-ADM-01')->orWhere('id', 65)->delete();
        DB::table('lantais')->where('id', 46)->delete();
        DB::table('gedungs')->where('kode', 'GDG-UJI-ADM')->orWhere('id', 24)->delete();

        // 4. Definisi master relasi dan kategori otoritatif 64 Lokasi K3RS ke Master Ruangan Fisik
        $masterDefinitions = [
            1 => ['ruangan_kode' => 'SEC-01', 'ruangan_nama' => 'Pos Pantau Security', 'kategori' => 'Keamanan Lingkungan', 'tipe_lokasi' => 'SERVICE_AREA'],
            2 => ['ruangan_kode' => 'IGD-01', 'ruangan_nama' => 'Ruang IGD', 'kategori' => 'Pelayanan Gawat Darurat', 'tipe_lokasi' => 'ROOM'],
            3 => ['ruangan_kode' => 'IGD-02', 'ruangan_nama' => 'Stroke Center', 'kategori' => 'Pelayanan Medis Khusus', 'tipe_lokasi' => 'ROOM'],
            4 => ['ruangan_kode' => 'IRJ-POLI', 'ruangan_nama' => 'Poliklinis', 'kategori' => 'Pelayanan Rawat Jalan', 'tipe_lokasi' => 'CORRIDOR'],
            5 => ['ruangan_kode' => 'IRJ-KAS', 'ruangan_nama' => 'Ruang Kasir', 'kategori' => 'Administrasi & Keuangan', 'tipe_lokasi' => 'ROOM'],
            6 => ['ruangan_kode' => 'IRJ-RM', 'ruangan_nama' => 'Instalasi Rekam Medis', 'kategori' => 'Penunjang Medis', 'tipe_lokasi' => 'ROOM'],
            7 => ['ruangan_kode' => 'IRJ-FIS', 'ruangan_nama' => 'Instalasi Fisioterapi', 'kategori' => 'Rehabilitasi Medik', 'tipe_lokasi' => 'ROOM'],
            8 => ['ruangan_kode' => 'IRJ-KOR2', 'ruangan_nama' => 'Selasar Rawat Jalan Lt 2', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            9 => ['ruangan_kode' => 'IRJ-KM', 'ruangan_nama' => 'Ruang Komite Medik', 'kategori' => 'Manajemen & Komite', 'tipe_lokasi' => 'ROOM'],
            10 => ['ruangan_kode' => 'IRJ-AUD', 'ruangan_nama' => 'Auditorium', 'kategori' => 'Fasilitas Pertemuan', 'tipe_lokasi' => 'ROOM'],
            11 => ['ruangan_kode' => 'IRJ-MNJ', 'ruangan_nama' => 'Manajemen & Arsip Kepegawaian', 'kategori' => 'Administrasi & Manajemen', 'tipe_lokasi' => 'ROOM'],
            12 => ['ruangan_kode' => 'FRM-01', 'ruangan_nama' => 'Instalasi Farmasi', 'kategori' => 'Pelayanan Farmasi', 'tipe_lokasi' => 'ROOM'],
            13 => ['ruangan_kode' => 'EMR-KOR1', 'ruangan_nama' => 'Selasar Emerald Lt 1', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            14 => ['ruangan_kode' => 'EMR-KOR2', 'ruangan_nama' => 'Selasar Emerald Lt 2', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            15 => ['ruangan_kode' => 'EMR-KOR3', 'ruangan_nama' => 'Selasar Emerald Lt 3', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            16 => ['ruangan_kode' => 'EMR-KOR4', 'ruangan_nama' => 'Selasar Emerald Lt 4', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            17 => ['ruangan_kode' => 'EMR-KOR5', 'ruangan_nama' => 'Selasar Emerald Lt 5', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            18 => ['ruangan_kode' => 'NLM-KOR1', 'ruangan_nama' => 'Selasar Nilam Lt 1', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            19 => ['ruangan_kode' => 'NLM-KOR2', 'ruangan_nama' => 'Selasar Nilam Lt 2', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            20 => ['ruangan_kode' => 'NLM-KOR3', 'ruangan_nama' => 'Selasar Nilam Lt 3', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            21 => ['ruangan_kode' => 'NLM-DRI', 'ruangan_nama' => 'Depo Farmasi Rawat Inap', 'kategori' => 'Pelayanan Farmasi', 'tipe_lokasi' => 'ROOM'],
            22 => ['ruangan_kode' => 'ALX-KOR1', 'ruangan_nama' => 'Selasar Alexandri Lt 1', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            23 => ['ruangan_kode' => 'ALX-KOR2', 'ruangan_nama' => 'Selasar Alexandri Lt 2', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            24 => ['ruangan_kode' => 'ALX-KOR3', 'ruangan_nama' => 'Selasar Alexandri Lt 3', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            25 => ['ruangan_kode' => 'ALX-KOR4', 'ruangan_nama' => 'Selasar Alexandri Lt 4', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            26 => ['ruangan_kode' => 'JMR-01', 'ruangan_nama' => 'Gedung Jamrud Non-Aktif', 'kategori' => 'Bangunan Kosong / Non-Aktif', 'tipe_lokasi' => 'OTHER'],
            27 => ['ruangan_kode' => 'YKT-01', 'ruangan_nama' => 'Gedung Yakut Non-Aktif', 'kategori' => 'Bangunan Kosong / Non-Aktif', 'tipe_lokasi' => 'OTHER'],
            28 => ['ruangan_kode' => 'GIK-01', 'ruangan_nama' => 'Gedung Giok Non-Aktif', 'kategori' => 'Bangunan Kosong / Non-Aktif', 'tipe_lokasi' => 'OTHER'],
            29 => ['ruangan_kode' => 'IRJ-JIW', 'ruangan_nama' => 'Poli Jiwa & Psikologi', 'kategori' => 'Pelayanan Rawat Jalan', 'tipe_lokasi' => 'ROOM'],
            30 => ['ruangan_kode' => 'KML-KOR1', 'ruangan_nama' => 'Selasar Kumala Lt 1', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            31 => ['ruangan_kode' => 'KML-KOR2', 'ruangan_nama' => 'Selasar Kumala Lt 2', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            32 => ['ruangan_kode' => 'KML-KOR3', 'ruangan_nama' => 'Selasar Kumala Lt 3', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            33 => ['ruangan_kode' => 'KML-OK', 'ruangan_nama' => 'Instalasi Bedah Sentral (OK) Lt 4', 'kategori' => 'Pelayanan Bedah Sentral', 'tipe_lokasi' => 'ROOM'],
            34 => ['ruangan_kode' => 'KML-DOK', 'ruangan_nama' => 'Depo Farmasi OK Lt 4', 'kategori' => 'Pelayanan Farmasi Khusus', 'tipe_lokasi' => 'ROOM'],
            35 => ['ruangan_kode' => 'KML-KOR5', 'ruangan_nama' => 'Selasar Kumala Lt 5', 'kategori' => 'Selasar / Koridor', 'tipe_lokasi' => 'CORRIDOR'],
            36 => ['ruangan_kode' => 'DGN-ESWL', 'ruangan_nama' => 'Poliklinik ESWL', 'kategori' => 'Pelayanan Rawat Jalan', 'tipe_lokasi' => 'ROOM'],
            37 => ['ruangan_kode' => 'DGN-RAD', 'ruangan_nama' => 'Radiologi', 'kategori' => 'Penunjang Medis', 'tipe_lokasi' => 'ROOM'],
            38 => ['ruangan_kode' => 'DGN-LAB', 'ruangan_nama' => 'Laboratorium', 'kategori' => 'Penunjang Medis', 'tipe_lokasi' => 'ROOM'],
            39 => ['ruangan_kode' => 'MTR-HD', 'ruangan_nama' => 'Unit Pelayanan Hemodialisa (HD)', 'kategori' => 'Pelayanan Medis Khusus', 'tipe_lokasi' => 'ROOM'],
            40 => ['ruangan_kode' => 'MTR-PERI', 'ruangan_nama' => 'Ruang Perawatan Bayi (Perinatologi)', 'kategori' => 'Pelayanan Medis Khusus', 'tipe_lokasi' => 'ROOM'],
            41 => ['ruangan_kode' => 'MTR-ICU', 'ruangan_nama' => 'Intensive Care Unit (ICU)', 'kategori' => 'Pelayanan Medis Khusus', 'tipe_lokasi' => 'ROOM'],
            42 => ['ruangan_kode' => 'CSD-LND', 'ruangan_nama' => 'Instalasi Laundry Lt 1', 'kategori' => 'Pelayanan Laundry', 'tipe_lokasi' => 'ROOM'],
            43 => ['ruangan_kode' => 'CSD-STRL', 'ruangan_nama' => 'Instalasi Sterilisasi CSSD Lt 2', 'kategori' => 'Sterilisasi CSSD', 'tipe_lokasi' => 'ROOM'],
            44 => ['ruangan_kode' => 'KML-BRN', 'ruangan_nama' => 'Burn Center (Unit Rawat Luka Bakar)', 'kategori' => 'Pelayanan Medis Khusus', 'tipe_lokasi' => 'ROOM'],
            45 => ['ruangan_kode' => 'GIZ-DPR', 'ruangan_nama' => 'Dapur Utama Pelayanan Gizi Lt 1', 'kategori' => 'Pelayanan Gizi', 'tipe_lokasi' => 'ROOM'],
            46 => ['ruangan_kode' => 'GIZ-ADM', 'ruangan_nama' => 'Ruang Administrasi Gizi Lt 2', 'kategori' => 'Pelayanan Gizi', 'tipe_lokasi' => 'ROOM'],
            47 => ['ruangan_kode' => 'FRM-ITD', 'ruangan_nama' => 'Ruang Server & IT Dalam', 'kategori' => 'Sistem Informasi & IT', 'tipe_lokasi' => 'ROOM'],
            48 => ['ruangan_kode' => 'FRM-ITL', 'ruangan_nama' => 'Area Kerja IT Luar', 'kategori' => 'Sistem Informasi & IT', 'tipe_lokasi' => 'CORRIDOR'],
            49 => ['ruangan_kode' => 'INS-LB3', 'ruangan_nama' => 'Gudang Limbah B3', 'kategori' => 'Pengolahan Limbah & B3', 'tipe_lokasi' => 'WAREHOUSE'],
            50 => ['ruangan_kode' => 'INS-CSF', 'ruangan_nama' => 'Cold Storage Freezer Sanitasi', 'kategori' => 'Sanitasi & K3', 'tipe_lokasi' => 'SERVICE_AREA'],
            51 => ['ruangan_kode' => 'INS-SAN', 'ruangan_nama' => 'Instalasi Sanitasi', 'kategori' => 'Sanitasi & K3', 'tipe_lokasi' => 'ROOM'],
            52 => ['ruangan_kode' => 'INS-INC', 'ruangan_nama' => 'Area Insinerator Pengolahan Limbah', 'kategori' => 'Pengolahan Limbah & B3', 'tipe_lokasi' => 'SERVICE_AREA'],
            53 => ['ruangan_kode' => 'LOG-01', 'ruangan_nama' => 'Gudang Logistik & Perbekalan', 'kategori' => 'Logistik & Penyimpanan', 'tipe_lokasi' => 'WAREHOUSE'],
            54 => ['ruangan_kode' => 'FRM-K3', 'ruangan_nama' => 'Kantor K3RS', 'kategori' => 'Keselamatan Kerja (K3RS)', 'tipe_lokasi' => 'ROOM'],
            55 => ['ruangan_kode' => 'IPS-WKS', 'ruangan_nama' => 'Bengkel Pemeliharaan Sarana IPSRS', 'kategori' => 'Pemeliharaan Sarana', 'tipe_lokasi' => 'SERVICE_AREA'],
            56 => ['ruangan_kode' => 'IPS-GEN', 'ruangan_nama' => 'Ruang Gardu & Pembangkit Genset', 'kategori' => 'Utilitas Listrik & Genset', 'tipe_lokasi' => 'SERVICE_AREA'],
            57 => ['ruangan_kode' => 'JNZ-01', 'ruangan_nama' => 'Instalasi Pemulasaraan Kamar Jenazah', 'kategori' => 'Pelayanan Pemulasaraan', 'tipe_lokasi' => 'SERVICE_AREA'],
            58 => ['ruangan_kode' => 'LFT-ALL', 'ruangan_nama' => 'Sangkar Fasilitas Transportasi Lift', 'kategori' => 'Fasilitas Transportasi Vertikal', 'tipe_lokasi' => 'ELEVATOR'],
            59 => ['ruangan_kode' => 'KTN-01', 'ruangan_nama' => 'Fasilitas Kantin', 'kategori' => 'Fasilitas Umum & Kantin', 'tipe_lokasi' => 'OTHER'],
            60 => ['ruangan_kode' => 'IRJ-RSP2', 'ruangan_nama' => 'Meja Resepsionis Lt 2', 'kategori' => 'Pelayanan Pelanggan', 'tipe_lokasi' => 'LOBBY'],
            61 => ['ruangan_kode' => 'CSD-GAS', 'ruangan_nama' => 'Sentral Tabung Gas Medis', 'kategori' => 'Utilitas Gas Medis', 'tipe_lokasi' => 'SERVICE_AREA'],
            62 => ['ruangan_kode' => 'IGD-119', 'ruangan_nama' => 'Ruang 119', 'kategori' => 'Pelayanan Gawat Darurat', 'tipe_lokasi' => 'ROOM'],
            63 => ['ruangan_kode' => 'FRM-GK3', 'ruangan_nama' => 'Gudang Stok K3RS', 'kategori' => 'Gudang Stok K3RS', 'tipe_lokasi' => 'WAREHOUSE'],
            64 => ['ruangan_kode' => 'KML-OKC', 'ruangan_nama' => 'Kamar Operasi Cito (Emergency OK)', 'kategori' => 'Pelayanan Bedah Cito', 'tipe_lokasi' => 'ROOM'],
        ];

        foreach ($masterDefinitions as $lokasiId => $def) {
            $lokasi = DB::table('lokasis')->where('id', $lokasiId)->first();
            if (! $lokasi) {
                continue;
            }

            $ruangan = null;
            if (isset($def['ruangan_kode'])) {
                $ruangan = DB::table('ruangans')->where('kode', $def['ruangan_kode'])->first();
            }
            if (! $ruangan && isset($def['ruangan_nama'])) {
                $ruanganQuery = DB::table('ruangans')->where('nama', $def['ruangan_nama']);
                if ($lokasi->lantai_id) {
                    $ruanganQuery->where('lantai_id', $lokasi->lantai_id);
                }
                $ruangan = $ruanganQuery->first();
            }

            if ($ruangan) {
                $ruanganId = $ruangan->id;
                DB::table('ruangans')->where('id', $ruanganId)->update([
                    'kategori' => $def['kategori'],
                    'updated_at' => now(),
                ]);
            } else {
                $ruanganId = DB::table('ruangans')->insertGetId([
                    'lantai_id' => $lokasi->lantai_id,
                    'kode' => $def['ruangan_kode'] ?? null,
                    'nama' => $def['ruangan_nama'],
                    'kategori' => $def['kategori'],
                    'keterangan' => $lokasi->keterangan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Hubungkan Lokasi ke Ruangan Master
            DB::table('lokasis')->where('id', $lokasiId)->update([
                'ruangan_id' => $ruanganId,
                'kategori' => $def['kategori'],
                'tipe_lokasi' => $def['tipe_lokasi'],
                'updated_at' => now(),
            ]);
        }

        // 5. Tambahkan Ruang Triase ke Lokasi agar sinkron 1:1 dengan master ruang fisik IGD
        $gedungIgd = DB::table('gedungs')->where('kode', 'GDG-IGD')->first();
        if ($gedungIgd) {
            $lt1 = DB::table('lantais')->where('gedung_id', $gedungIgd->id)->where('nomor_lantai', 1)->first();
            if ($lt1) {
                $ruanganTriase = DB::table('ruangans')->where('kode', 'IGD-TRS')->first();
                if (! $ruanganTriase) {
                    $triaseId = DB::table('ruangans')->insertGetId([
                        'lantai_id' => $lt1->id,
                        'kode' => 'IGD-TRS',
                        'nama' => 'Ruang Triase',
                        'kategori' => 'Pelayanan Gawat Darurat',
                        'keterangan' => 'Ruang Triase Gawat Darurat',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $triaseId = $ruanganTriase->id;
                    DB::table('ruangans')->where('id', $triaseId)->update([
                        'kategori' => 'Pelayanan Gawat Darurat',
                        'updated_at' => now(),
                    ]);
                }

                $triaseLokasi = DB::table('lokasis')->where('kode', 'TRS')->orWhere('nama', 'Ruang Triase')->first();
                if (! $triaseLokasi) {
                    DB::table('lokasis')->insert([
                        'gedung_id' => $gedungIgd->id,
                        'lantai_id' => $lt1->id,
                        'ruangan_id' => $triaseId,
                        'kode' => 'TRS',
                        'nama' => 'Ruang Triase',
                        'kategori' => 'Pelayanan Gawat Darurat',
                        'tipe_lokasi' => 'ROOM',
                        'status' => 'kosong',
                        'keterangan' => 'Ruang Triase Gawat Darurat (Non-penempatan APAR / 0 APAR)',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('lokasis')->where('id', $triaseLokasi->id)->update([
                        'gedung_id' => $gedungIgd->id,
                        'lantai_id' => $lt1->id,
                        'ruangan_id' => $triaseId,
                        'kategori' => 'Pelayanan Gawat Darurat',
                        'tipe_lokasi' => 'ROOM',
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 6. Sinkronisasi seluruh 173 APAR: isi ruangan_id sesuai relasi lokasi->ruangan_id
        $lokasiMap = DB::table('lokasis')->pluck('ruangan_id', 'id');
        foreach ($lokasiMap as $lokId => $rngId) {
            if ($rngId) {
                DB::table('apars')->where('lokasi_id', $lokId)->update([
                    'ruangan_id' => $rngId,
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lokasis', 'ruangan_id')) {
            Schema::table('lokasis', function (Blueprint $table) {
                $table->dropConstrainedForeignId('ruangan_id');
            });
        }
    }
};
