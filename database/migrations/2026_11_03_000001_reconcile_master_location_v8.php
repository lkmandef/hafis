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
        // 1. Tambah kolom gedung_id, lantai_id, dan tipe_lokasi pada tabel lokasis jika belum ada
        if (! Schema::hasColumn('lokasis', 'gedung_id')) {
            Schema::table('lokasis', function (Blueprint $table) {
                $table->foreignId('gedung_id')->nullable()->after('id')->constrained('gedungs')->restrictOnDelete();
                $table->foreignId('lantai_id')->nullable()->after('gedung_id')->constrained('lantais')->nullOnDelete();
                $table->string('tipe_lokasi', 30)->default('SERVICE_AREA')->after('kategori');
            });
        }

        // 2. Ubah ruangan_id pada tabel apars menjadi nullable untuk mendukung penempatan non-ruangan
        Schema::table('apars', function (Blueprint $table) {
            $table->foreignId('ruangan_id')->nullable()->change();
        });

        // 3. Pastikan gedung-gedung resmi K3RS terdaftar
        $gedungDefinitions = [
            'GDG-IGD' => ['nama' => 'Gedung IGD', 'status' => 'aktif', 'keterangan' => 'Instalasi Gawat Darurat & Layanan Kritis'],
            'GDG-IRJ' => ['nama' => 'Gedung Instalasi Rawat Jalan', 'status' => 'aktif', 'keterangan' => 'Poliklinik Rawat Jalan & Administrasi Manajemen'],
            'GDG-EMR' => ['nama' => 'Gedung Emerald', 'status' => 'aktif', 'keterangan' => 'Rawat Inap Bertingkat Emerald'],
            'GDG-NLM' => ['nama' => 'Gedung Nilam', 'status' => 'aktif', 'keterangan' => 'Rawat Inap Bertingkat Nilam'],
            'GDG-ALX' => ['nama' => 'Gedung Alexandri', 'status' => 'aktif', 'keterangan' => 'Rawat Inap Bertingkat Alexandri'],
            'GDG-KML' => ['nama' => 'Gedung Kumala', 'status' => 'aktif', 'keterangan' => 'Instalasi Bedah Sentral & Rawat Inap Kumala'],
            'GDG-MTR' => ['nama' => 'Gedung Mutiara', 'status' => 'aktif', 'keterangan' => 'Gedung Pelayanan Khusus (Hemodialisa, Perinatologi, ICU)'],
            'GDG-FRM' => ['nama' => 'Gedung Farmasi & Business Center', 'status' => 'aktif', 'keterangan' => 'Instalasi Farmasi, Server IT, & Kantor K3RS'],
            'GDG-DGN' => ['nama' => 'Gedung Diagnostik', 'status' => 'aktif', 'keterangan' => 'Instalasi Radiologi & Laboratorium Patologi'],
            'GDG-CSD' => ['nama' => 'Gedung CSSD & Laundry', 'status' => 'aktif', 'keterangan' => 'Instalasi Sterilisasi CSSD, Laundry, & Gas Medis'],
            'GDG-GIZ' => ['nama' => 'Gedung Gizi', 'status' => 'aktif', 'keterangan' => 'Instalasi Dapur & Pelayanan Gizi Rumah Sakit'],
            'GDG-INS' => ['nama' => 'Gedung Sanitasi & Insinerator', 'status' => 'aktif', 'keterangan' => 'Pengolahan Limbah B3, Cold Storage, & Insinerator'],
            'GDG-IPS' => ['nama' => 'Gedung IPSRS', 'status' => 'aktif', 'keterangan' => 'Bengkel Pemeliharaan Sarana Rumah Sakit & Ruang Genset'],
            'GDG-LOG' => ['nama' => 'Gedung Gudang Logistik', 'status' => 'aktif', 'keterangan' => 'Gudang Logistik Umum Rumah Sakit'],
            'GDG-SEC' => ['nama' => 'Pos Keamanan / Security', 'status' => 'aktif', 'keterangan' => 'Pos Pantau Keamanan Gerbang Utama'],
            'GDG-JNZ' => ['nama' => 'Gedung Pemulasaraan Jenazah', 'status' => 'aktif', 'keterangan' => 'Instalasi Pemulasaraan Kamar Jenazah'],
            'FAC-LFT' => ['nama' => 'Fasilitas Transportasi Vertikal (Lift)', 'status' => 'aktif', 'keterangan' => 'Sangkar lift penumpang dan barang lintas gedung'],
            'GDG-JMR' => ['nama' => 'Gedung Jamrud', 'status' => 'nonaktif', 'keterangan' => 'Bangunan kosong / non-operasional'],
            'GDG-YKT' => ['nama' => 'Gedung Yakut', 'status' => 'nonaktif', 'keterangan' => 'Bangunan kosong / non-operasional'],
            'GDG-GIK' => ['nama' => 'Gedung Giok', 'status' => 'nonaktif', 'keterangan' => 'Bangunan kosong / non-operasional'],
            'GDG-KTN' => ['nama' => 'Gedung Kantin', 'status' => 'nonaktif', 'keterangan' => 'Fasilitas kantin rumah sakit'],
        ];

        foreach ($gedungDefinitions as $kode => $data) {
            $existing = DB::table('gedungs')->where('kode', $kode)->first();
            if ($existing) {
                DB::table('gedungs')->where('kode', $kode)->update([
                    'nama' => $data['nama'],
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('gedungs')->insert([
                    'kode' => $kode,
                    'nama' => $data['nama'],
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Peta ID Gedung
        $gedungs = DB::table('gedungs')->pluck('id', 'kode');

        // 4. Pastikan Lantai untuk setiap gedung terdaftar sesuai sumber
        // A. Gedung Diagnostik (GDG-DGN) -> Wajib Lantai 1 & Lantai 2
        $dgnId = $gedungs['GDG-DGN'] ?? null;
        if ($dgnId) {
            $lt1Dgn = DB::table('lantais')->where('gedung_id', $dgnId)->where('nomor_lantai', 1)->first();
            if (! $lt1Dgn) {
                DB::table('lantais')->insert([
                    'gedung_id' => $dgnId,
                    'nomor_lantai' => 1,
                    'nama' => 'Lantai 1',
                    'keterangan' => 'Instalasi Radiologi & Poli ESWL',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $lt2Dgn = DB::table('lantais')->where('gedung_id', $dgnId)->where('nomor_lantai', 2)->first();
            if (! $lt2Dgn) {
                DB::table('lantais')->insert([
                    'gedung_id' => $dgnId,
                    'nomor_lantai' => 2,
                    'nama' => 'Lantai 2',
                    'keterangan' => 'Laboratorium Patologi Klinik & Bank Darah',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // B. Gedung Mutiara (GDG-MTR) -> Lantai 1, 2, 3
        $mtrId = $gedungs['GDG-MTR'] ?? null;
        if ($mtrId) {
            foreach ([
                [1, 'Lantai 1', 'Unit Hemodialisa'],
                [2, 'Lantai 2', 'Ruang Perawatan Bayi'],
                [3, 'Lantai 3', 'Intensive Care Unit (ICU)'],
            ] as [$no, $nama, $ket]) {
                $exists = DB::table('lantais')->where('gedung_id', $mtrId)->where('nomor_lantai', $no)->first();
                if (! $exists) {
                    DB::table('lantais')->insert([
                        'gedung_id' => $mtrId,
                        'nomor_lantai' => $no,
                        'nama' => $nama,
                        'keterangan' => $ket,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // C. Pos Security (GDG-SEC) -> Lantai 1
        $secId = $gedungs['GDG-SEC'] ?? null;
        if ($secId) {
            $secLt1 = DB::table('lantais')->where('gedung_id', $secId)->where('nomor_lantai', 1)->first();
            if (! $secLt1) {
                DB::table('lantais')->insert([
                    'gedung_id' => $secId,
                    'nomor_lantai' => 1,
                    'nama' => 'Lantai 1',
                    'keterangan' => 'Pos Pantau Keamanan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // D. Update Ruangan Laboratorium agar terikat ke Lantai 2 Gedung Diagnostik
        if ($dgnId) {
            $lt2Dgn = DB::table('lantais')->where('gedung_id', $dgnId)->where('nomor_lantai', 2)->first();
            if ($lt2Dgn) {
                DB::table('ruangans')->where('kode', 'DGN-LAB')->orWhere('id', 6)->update([
                    'lantai_id' => $lt2Dgn->id,
                    'updated_at' => now(),
                ]);
            }
        }

        // 5. Pemetaan Otoritatif 64 Titik Lokasi K3RS ke Gedung & Lantai
        $lantais = DB::table('lantais')->get()->groupBy(fn ($l) => "{$l->gedung_id}_{$l->nomor_lantai}");

        $mappings = [
            1 => ['gedung' => 'GDG-SEC', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            2 => ['gedung' => 'GDG-IGD', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            3 => ['gedung' => 'GDG-IGD', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            4 => ['gedung' => 'GDG-IRJ', 'lantai' => 1, 'tipe' => 'CORRIDOR'],
            5 => ['gedung' => 'GDG-IRJ', 'lantai' => 1, 'tipe' => 'ROOM'],
            6 => ['gedung' => 'GDG-IRJ', 'lantai' => 1, 'tipe' => 'ROOM'],
            7 => ['gedung' => 'GDG-IRJ', 'lantai' => 1, 'tipe' => 'ROOM'],
            8 => ['gedung' => 'GDG-IRJ', 'lantai' => 2, 'tipe' => 'CORRIDOR'],
            9 => ['gedung' => 'GDG-IRJ', 'lantai' => 2, 'tipe' => 'ROOM'],
            10 => ['gedung' => 'GDG-IRJ', 'lantai' => 2, 'tipe' => 'ROOM'],
            11 => ['gedung' => 'GDG-IRJ', 'lantai' => 2, 'tipe' => 'ROOM'],
            12 => ['gedung' => 'GDG-FRM', 'lantai' => 1, 'tipe' => 'ROOM'],
            13 => ['gedung' => 'GDG-EMR', 'lantai' => 1, 'tipe' => 'CORRIDOR'],
            14 => ['gedung' => 'GDG-EMR', 'lantai' => 2, 'tipe' => 'CORRIDOR'],
            15 => ['gedung' => 'GDG-EMR', 'lantai' => 3, 'tipe' => 'CORRIDOR'],
            16 => ['gedung' => 'GDG-EMR', 'lantai' => 4, 'tipe' => 'CORRIDOR'],
            17 => ['gedung' => 'GDG-EMR', 'lantai' => 5, 'tipe' => 'CORRIDOR'],
            18 => ['gedung' => 'GDG-NLM', 'lantai' => 1, 'tipe' => 'CORRIDOR'],
            19 => ['gedung' => 'GDG-NLM', 'lantai' => 2, 'tipe' => 'CORRIDOR'],
            20 => ['gedung' => 'GDG-NLM', 'lantai' => 3, 'tipe' => 'CORRIDOR'],
            21 => ['gedung' => 'GDG-NLM', 'lantai' => 1, 'tipe' => 'ROOM'],
            22 => ['gedung' => 'GDG-ALX', 'lantai' => 1, 'tipe' => 'CORRIDOR'],
            23 => ['gedung' => 'GDG-ALX', 'lantai' => 2, 'tipe' => 'CORRIDOR'],
            24 => ['gedung' => 'GDG-ALX', 'lantai' => 3, 'tipe' => 'CORRIDOR'],
            25 => ['gedung' => 'GDG-ALX', 'lantai' => 4, 'tipe' => 'CORRIDOR'],
            26 => ['gedung' => 'GDG-JMR', 'lantai' => null, 'tipe' => 'OTHER'],
            27 => ['gedung' => 'GDG-YKT', 'lantai' => null, 'tipe' => 'OTHER'],
            28 => ['gedung' => 'GDG-GIK', 'lantai' => 1, 'tipe' => 'OTHER'],
            29 => ['gedung' => 'GDG-IRJ', 'lantai' => 1, 'tipe' => 'ROOM'],
            30 => ['gedung' => 'GDG-KML', 'lantai' => 1, 'tipe' => 'CORRIDOR'],
            31 => ['gedung' => 'GDG-KML', 'lantai' => 2, 'tipe' => 'CORRIDOR'],
            32 => ['gedung' => 'GDG-KML', 'lantai' => 3, 'tipe' => 'CORRIDOR'],
            33 => ['gedung' => 'GDG-KML', 'lantai' => 4, 'tipe' => 'CORRIDOR'],
            34 => ['gedung' => 'GDG-KML', 'lantai' => 4, 'tipe' => 'ROOM'],
            35 => ['gedung' => 'GDG-KML', 'lantai' => 5, 'tipe' => 'CORRIDOR'],
            36 => ['gedung' => 'GDG-DGN', 'lantai' => 1, 'tipe' => 'ROOM'],
            37 => ['gedung' => 'GDG-DGN', 'lantai' => 1, 'tipe' => 'ROOM'],
            38 => ['gedung' => 'GDG-DGN', 'lantai' => 2, 'tipe' => 'ROOM'],
            39 => ['gedung' => 'GDG-MTR', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            40 => ['gedung' => 'GDG-MTR', 'lantai' => 2, 'tipe' => 'SERVICE_AREA'],
            41 => ['gedung' => 'GDG-MTR', 'lantai' => 3, 'tipe' => 'SERVICE_AREA'],
            42 => ['gedung' => 'GDG-CSD', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            43 => ['gedung' => 'GDG-CSD', 'lantai' => 2, 'tipe' => 'SERVICE_AREA'],
            44 => ['gedung' => 'GDG-KML', 'lantai' => 4, 'tipe' => 'ROOM'],
            45 => ['gedung' => 'GDG-GIZ', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            46 => ['gedung' => 'GDG-GIZ', 'lantai' => 2, 'tipe' => 'SERVICE_AREA'],
            47 => ['gedung' => 'GDG-FRM', 'lantai' => 2, 'tipe' => 'ROOM'],
            48 => ['gedung' => 'GDG-FRM', 'lantai' => 2, 'tipe' => 'CORRIDOR'],
            49 => ['gedung' => 'GDG-INS', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            50 => ['gedung' => 'GDG-INS', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            51 => ['gedung' => 'GDG-INS', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            52 => ['gedung' => 'GDG-INS', 'lantai' => 1, 'tipe' => 'OPEN_AREA'],
            53 => ['gedung' => 'GDG-LOG', 'lantai' => 1, 'tipe' => 'ROOM'],
            54 => ['gedung' => 'GDG-FRM', 'lantai' => 2, 'tipe' => 'ROOM'],
            55 => ['gedung' => 'GDG-IPS', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            56 => ['gedung' => 'GDG-IPS', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            57 => ['gedung' => 'GDG-JNZ', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            58 => ['gedung' => 'FAC-LFT', 'lantai' => null, 'tipe' => 'OTHER'],
            59 => ['gedung' => 'GDG-KTN', 'lantai' => null, 'tipe' => 'OTHER'],
            60 => ['gedung' => 'GDG-IRJ', 'lantai' => 2, 'tipe' => 'LOBBY'],
            61 => ['gedung' => 'GDG-CSD', 'lantai' => 1, 'tipe' => 'OPEN_AREA'],
            62 => ['gedung' => 'GDG-IGD', 'lantai' => 1, 'tipe' => 'SERVICE_AREA'],
            63 => ['gedung' => 'GDG-FRM', 'lantai' => 2, 'tipe' => 'ROOM'],
            64 => ['gedung' => 'GDG-KML', 'lantai' => 4, 'tipe' => 'ROOM'],
        ];

        foreach ($mappings as $lokasiId => $m) {
            $gId = $gedungs[$m['gedung']] ?? null;
            $ltId = null;
            if ($gId && $m['lantai'] !== null) {
                $ltKey = "{$gId}_{$m['lantai']}";
                $ltId = isset($lantais[$ltKey]) ? $lantais[$ltKey]->first()->id : null;
            }

            DB::table('lokasis')->where('id', $lokasiId)->update([
                'gedung_id' => $gId,
                'lantai_id' => $ltId,
                'tipe_lokasi' => $m['tipe'],
                'updated_at' => now(),
            ]);
        }

        // 6. Rekonsiliasi APAR: Hubungkan ke ruangan klinis tertutup yang valid, buat null untuk APAR koridor/non-ruangan
        // Enclosed clinical rooms:
        // 4 IGD -> ruangan_id = 1 (Ruang IGD)
        DB::table('apars')->whereIn('kode', ['APAR-IGD-01', 'APAR-IGD-02', 'APAR-IGD-03', 'APAR-IGD-04'])->update(['ruangan_id' => 1]);
        // 2 Stroke Center -> ruangan_id = 2 (Stroke Center)
        DB::table('apars')->whereIn('kode', ['APAR-STR-01', 'APAR-STR-02'])->update(['ruangan_id' => 2]);
        // 5 Radiologi -> ruangan_id = 5 (Radiologi)
        DB::table('apars')->where('kode', 'like', 'APAR-RAD-%')->update(['ruangan_id' => 5]);
        // 3 Laboratorium -> ruangan_id = 6 (Laboratorium)
        DB::table('apars')->where('kode', 'like', 'APAR-LAB-%')->update(['ruangan_id' => 6]);

        // 159 APAR lainnya yang berada di selasar koridor, lift, dan area terbuka disetel ruangan_id = null
        DB::table('apars')
            ->whereNotIn('kode', ['APAR-IGD-01', 'APAR-IGD-02', 'APAR-IGD-03', 'APAR-IGD-04', 'APAR-STR-01', 'APAR-STR-02'])
            ->where('kode', 'not like', 'APAR-RAD-%')
            ->where('kode', 'not like', 'APAR-LAB-%')
            ->update(['ruangan_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe rollback: Kembalikan ruangan_id ke 1 sebelum constraint NOT NULL jika diperlukan
        DB::table('apars')->whereNull('ruangan_id')->update(['ruangan_id' => 1]);

        Schema::table('apars', function (Blueprint $table) {
            $table->foreignId('ruangan_id')->change();
        });

        if (Schema::hasColumn('lokasis', 'gedung_id')) {
            Schema::table('lokasis', function (Blueprint $table) {
                $table->dropConstrainedForeignId('lantai_id');
                $table->dropConstrainedForeignId('gedung_id');
                $table->dropColumn('tipe_lokasi');
            });
        }
    }
};
