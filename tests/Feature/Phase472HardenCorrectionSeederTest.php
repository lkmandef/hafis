<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Database\Seeders\AparAtributKoreksiSeeder;
use Database\Seeders\LokasiAktualSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class Phase472HardenCorrectionSeederTest extends TestCase
{
    use RefreshDatabase;

    private Ruangan $legacyRuangan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->legacyRuangan = $this->createLegacyRuangan();
    }

    private function createLegacyRuangan(): Ruangan
    {
        $gedung = Gedung::create([
            'kode' => 'GDG-LEGACY-TEST',
            'nama' => 'Gedung Legacy Test',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        return Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruang IGD',
            'kode' => 'R-IGD',
        ]);
    }

    /**
     * Helper untuk membuat 173 APAR dalam kondisi baseline sebelum koreksi.
     */
    private function populateBaselineApars(): void
    {
        $this->seed(LokasiAktualSeeder::class);

        $lokasiList = Lokasi::where('status', Lokasi::STATUS_AKTIF)->get();
        foreach ($lokasiList as $loc) {
            $locDef = collect(LokasiAktualSeeder::LOCATIONS_DATA)->firstWhere('nama', $loc->nama);
            for ($i = 1; $i <= $locDef['target_apar']; $i++) {
                Apar::create([
                    'kode' => sprintf('APAR-%s-%02d', $loc->kode, $i),
                    'qr_code' => sprintf('HAFIS-QR-APAR-%s-%02d', $loc->kode, $i),
                    'lokasi_id' => $loc->id,
                    'ruangan_id' => $this->legacyRuangan->id,
                    'jenis_media' => 'Dry Chemical Powder',
                    'kapasitas' => '6 kg',
                    'merek' => 'Servvo',
                    'status' => Apar::STATUS_BAIK,
                ]);
            }
        }
    }

    /**
     * 1. Test fail-fast: Lokasi tidak dikenal di database melempar RuntimeException.
     */
    public function test_validation_fails_when_location_is_unknown(): void
    {
        $this->populateBaselineApars();

        $customData = LokasiAktualSeeder::LOCATIONS_DATA;
        // Ubah satu lokasi menjadi nama yang tidak ada di tabel lokasis
        $customData[0]['nama'] = 'LOKASI TIDAK DIKENAL K3RS';

        $seeder = new AparAtributKoreksiSeeder;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Validasi gagal: Lokasi master 'LOKASI TIDAK DIKENAL K3RS' tidak ditemukan di database (tabel lokasis).");

        $seeder->validateAndBuildCorrectionPlan($customData);
    }

    /**
     * 2. Test fail-fast: Jumlah APAR di database kurang dari matriks melempar RuntimeException.
     */
    public function test_validation_fails_when_apar_count_is_less_than_matrix(): void
    {
        $this->populateBaselineApars();

        // Hapus 1 APAR pada lokasi EMERALD Lt 1 (harus 5, menjadi 4)
        $emerald = Lokasi::where('nama', 'EMERALD Lt 1')->firstOrFail();
        Apar::where('lokasi_id', $emerald->id)->firstOrFail()->delete();

        $seeder = new AparAtributKoreksiSeeder;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Ketidaksesuaian jumlah APAR pada lokasi \'EMERALD Lt 1\'/');

        $seeder->run();
    }

    /**
     * 3. Test fail-fast: Jumlah APAR di database lebih dari matriks melempar RuntimeException.
     */
    public function test_validation_fails_when_apar_count_is_greater_than_matrix(): void
    {
        $this->populateBaselineApars();

        // Tambah 1 APAR ekstra pada lokasi EMERALD Lt 1 (harus 5, menjadi 6)
        $emerald = Lokasi::where('nama', 'EMERALD Lt 1')->firstOrFail();
        Apar::create([
            'kode' => 'APAR-EMR1-99',
            'qr_code' => 'HAFIS-QR-APAR-EMR1-99',
            'lokasi_id' => $emerald->id,
            'ruangan_id' => $this->legacyRuangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Servvo',
            'status' => Apar::STATUS_BAIK,
        ]);

        $seeder = new AparAtributKoreksiSeeder;

        $this->expectException(RuntimeException::class);
        // Bisa tertangkap di validasi total APAR (174 != 173) atau validasi per lokasi
        $this->expectExceptionMessageMatches('/Validasi gagal|Ketidaksesuaian jumlah APAR/');

        $seeder->run();
    }

    /**
     * 4. Test fail-fast: Kategori media tidak valid melempar RuntimeException.
     */
    public function test_validation_fails_when_media_category_is_invalid(): void
    {
        $this->populateBaselineApars();

        $customData = LokasiAktualSeeder::LOCATIONS_DATA;
        $customData[0]['items'][0]['media'] = 'Water Foam Invalid';

        $seeder = new AparAtributKoreksiSeeder;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Jenis media \'Water Foam Invalid\' pada lokasi \'[^\']+\' \(item indeks ke-0\) tidak valid\./');

        $seeder->validateAndBuildCorrectionPlan($customData);
    }

    /**
     * 5. Test fail-fast: Kategori kapasitas tidak valid melempar RuntimeException.
     */
    public function test_validation_fails_when_capacity_category_is_invalid(): void
    {
        $this->populateBaselineApars();

        $customData = LokasiAktualSeeder::LOCATIONS_DATA;
        $customData[0]['items'][0]['kapasitas'] = '50 kg';

        $seeder = new AparAtributKoreksiSeeder;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Kapasitas \'50 kg\' pada lokasi \'[^\']+\' \(item indeks ke-0\) tidak valid\./');

        $seeder->validateAndBuildCorrectionPlan($customData);
    }

    /**
     * 6. Test keamanan: Validasi gagal tanpa meninggalkan perubahan parsial pada database.
     */
    public function test_validation_failure_prevents_partial_updates(): void
    {
        $this->populateBaselineApars();

        // Rekam status awal database
        $initialServvoCount = Apar::where('merek', 'Servvo')->count();
        $this->assertEquals(173, $initialServvoCount);

        // Buat anomali pada lokasi terakhir (OK CITO) dengan menghapus 1 unit
        $okCito = Lokasi::where('nama', 'OK CITO')->firstOrFail();
        Apar::where('lokasi_id', $okCito->id)->firstOrFail()->delete();

        $seeder = new AparAtributKoreksiSeeder;

        try {
            $seeder->run();
            $this->fail('Seeder harusnya melempar RuntimeException karena anomali data.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Ketidaksesuaian jumlah APAR pada lokasi', $e->getMessage());
        }

        // Pastikan tidak ada 1 record pun yang sempat diubah menjadi NULL atau CO2
        $this->assertEquals(172, Apar::where('merek', 'Servvo')->count());
        $this->assertEquals(0, Apar::whereNull('merek')->count());
        $this->assertEquals(0, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->count());
    }

    /**
     * 7. Test sukses dan idempotent: Koreksi atribut berhasil 100% dan aman dijalankan berulang kali.
     */
    public function test_successful_execution_and_idempotency(): void
    {
        $this->populateBaselineApars();

        $seeder = new AparAtributKoreksiSeeder;

        // Eksekusi pertama
        $seeder->run();

        $this->assertEquals(173, Apar::count());
        $this->assertEquals(115, Apar::where('jenis_media', 'Dry Chemical Powder')->count());
        $this->assertEquals(58, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->count());
        $this->assertEquals(173, Apar::whereNull('merek')->count());

        // Breakdown kapasitas spesifik
        $this->assertEquals(3, Apar::where('kapasitas', '25 kg')->count());
        $this->assertEquals(108, Apar::where('kapasitas', '6 kg')->count());
        $this->assertEquals(38, Apar::where('kapasitas', '3 kg')->count());
        $this->assertEquals(17, Apar::where('kapasitas', '7 kg')->count());
        $this->assertEquals(7, Apar::where('kapasitas', '5 kg')->count());

        // Eksekusi kedua (idempotency check)
        $seeder->run();

        $this->assertEquals(173, Apar::count());
        $this->assertEquals(115, Apar::where('jenis_media', 'Dry Chemical Powder')->count());
        $this->assertEquals(58, Apar::where('jenis_media', 'CO2 (Carbon Dioxide)')->count());
        $this->assertEquals(173, Apar::whereNull('merek')->count());
    }
}
