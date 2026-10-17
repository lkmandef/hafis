<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Jadwal;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Pemeriksaan;
use App\Models\PemeriksaanDetail;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase1DomainFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();
    }

    private function createLegacyRuangan(): Ruangan
    {
        $uid = uniqid();
        $gedung = Gedung::create([
            'kode' => 'GDG-'.$uid,
            'nama' => 'Gedung Pengujian '.$uid,
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1 Test',
        ]);

        return Ruangan::create([
            'lantai_id' => $lantai->id,
            'nama' => 'Ruangan Test '.$uid,
            'kode' => 'R-'.$uid,
        ]);
    }

    private function createApar(array $attributes = []): Apar
    {
        $ruangan = $this->createLegacyRuangan();

        return Apar::create(array_merge([
            'kode' => 'APAR-TEST-'.uniqid(),
            'qr_code' => 'HAFIS-QR-TEST-'.uniqid(),
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'merek' => 'Servvo',
            'status' => Apar::STATUS_BAIK,
        ], $attributes));
    }

    /**
     * 1. Lokasi can have many APAR.
     */
    public function test_lokasi_can_have_many_apar(): void
    {
        $lokasi = Lokasi::create([
            'nama' => 'EMERALD Lt 1',
            'kode' => 'EMR1',
            'kategori' => 'Rawat Inap',
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $apar1 = $this->createApar(['lokasi_id' => $lokasi->id]);
        $apar2 = $this->createApar(['lokasi_id' => $lokasi->id]);

        $this->assertCount(2, $lokasi->apars);
        $this->assertTrue($lokasi->apars->contains($apar1));
        $this->assertTrue($lokasi->apars->contains($apar2));
    }

    /**
     * 2. APAR can belong to a Lokasi.
     */
    public function test_apar_can_belong_to_a_lokasi(): void
    {
        $lokasi = Lokasi::create([
            'nama' => 'IT DALAM',
            'kode' => 'ITD',
            'kategori' => 'Penunjang',
            'status' => Lokasi::STATUS_AKTIF,
        ]);

        $apar = $this->createApar(['lokasi_id' => $lokasi->id]);

        $this->assertNotNull($apar->lokasi);
        $this->assertEquals($lokasi->id, $apar->lokasi->id);
        $this->assertEquals('IT DALAM', $apar->lokasi->nama);
    }

    /**
     * 3. APAR can temporarily have no Lokasi.
     */
    public function test_apar_can_temporarily_have_no_lokasi(): void
    {
        $apar = $this->createApar(['lokasi_id' => null]);

        $this->assertNull($apar->lokasi_id);
        $this->assertNull($apar->lokasi);
    }

    /**
     * 4. Existing APAR legacy location relationship remains intact.
     */
    public function test_existing_apar_legacy_location_relationship_remains_intact(): void
    {
        $gedung = Gedung::create(['kode' => 'GDG-LEGACY', 'nama' => 'Gedung Bedah Sentral']);
        $lantai = Lantai::create(['gedung_id' => $gedung->id, 'nomor_lantai' => 2, 'nama' => 'Lantai 2 Bedah']);
        $ruangan = Ruangan::create(['lantai_id' => $lantai->id, 'nama' => 'Ruang Operasi 1']);

        $apar = Apar::create([
            'kode' => 'APAR-OK-01',
            'qr_code' => 'HAFIS-QR-OK-01',
            'ruangan_id' => $ruangan->id,
            'jenis_media' => 'CO2 (Carbon Dioxide)',
            'kapasitas' => '5 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $this->assertEquals($ruangan->id, $apar->ruangan->id);
        $this->assertEquals($lantai->id, $apar->ruangan->lantai->id);
        $this->assertEquals($gedung->id, $apar->gedung->id);
        $this->assertEquals('Gedung Bedah Sentral, Lantai 2 Bedah, Ruang Operasi 1', $apar->lokasi_lengkap);
    }

    /**
     * 5. Pemeriksaan can store periode_tahun.
     */
    public function test_pemeriksaan_can_store_periode_tahun(): void
    {
        $apar = $this->createApar();
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $this->assertSame(2026, $pemeriksaan->periode_tahun);
        $this->assertDatabaseHas('pemeriksaans', [
            'id' => $pemeriksaan->id,
            'periode_tahun' => 2026,
        ]);
    }

    /**
     * 6. Pemeriksaan can store periode_bulan.
     */
    public function test_pemeriksaan_can_store_periode_bulan(): void
    {
        $apar = $this->createApar();
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 10,
            'pemeriksa_nama' => 'Petugas K3RS',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $this->assertSame(10, $pemeriksaan->periode_bulan);
        $this->assertDatabaseHas('pemeriksaans', [
            'id' => $pemeriksaan->id,
            'periode_bulan' => 10,
        ]);
    }

    /**
     * 7. Month value outside 1–12 is rejected at the domain/application validation level.
     */
    public function test_month_value_outside_1_to_12_is_rejected_at_domain_validation_level(): void
    {
        $apar = $this->createApar();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Periode bulan harus bernilai antara 1 sampai 12.');

        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 13,
            'pemeriksa_nama' => 'Petugas K3RS',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);
    }

    /**
     * 8. New monthly inspection concept supports one APAR + year + month as the intended unique business key.
     */
    public function test_new_monthly_inspection_concept_supports_one_apar_year_month_as_intended_unique_key(): void
    {
        $apar = $this->createApar();

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 6,
            'pemeriksa_nama' => 'Petugas K3RS Pertama',
            'tanggal_pemeriksaan' => '2026-06-10 09:00:00',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        // Helper lookup resolves the single inspection record
        $found = Pemeriksaan::findByAparPeriod($apar, 2026, 6);
        $this->assertNotNull($found);
        $this->assertEquals($pemeriksaan->id, $found->id);

        // Scope query resolves the record
        $this->assertEquals(1, Pemeriksaan::forPeriod(2026, 6)->where('apar_id', $apar->id)->count());

        // Invariant protection: Duplikasi inspeksi resmi pada unit dan bulan yang sama ditolak
        $this->expectException(\DomainException::class);
        Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 6,
            'pemeriksa_nama' => 'Petugas K3RS Kedua (Duplikat)',
            'tanggal_pemeriksaan' => '2026-06-12 10:00:00',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);
    }

    /**
     * 9. Existing legacy pemeriksaan records without period values are not destroyed.
     */
    public function test_existing_legacy_pemeriksaan_records_without_period_values_are_not_destroyed(): void
    {
        $apar = $this->createApar();

        $legacy1 = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Pemeriksa Legacy 1',
            'tanggal_pemeriksaan' => '2026-02-01 10:00:00',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $legacy2 = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => null,
            'periode_bulan' => null,
            'pemeriksa_nama' => 'Pemeriksa Legacy 2',
            'tanggal_pemeriksaan' => '2026-03-01 11:00:00',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $this->assertNull($legacy1->periode_tahun);
        $this->assertNull($legacy1->periode_bulan);
        $this->assertNull($legacy2->periode_tahun);
        $this->assertNull($legacy2->periode_bulan);

        $this->assertDatabaseHas('pemeriksaans', ['id' => $legacy1->id, 'periode_tahun' => null]);
        $this->assertDatabaseHas('pemeriksaans', ['id' => $legacy2->id, 'periode_tahun' => null]);
    }

    /**
     * 10. Existing Jadwal/Pemeriksaan relationship remains intact.
     */
    public function test_existing_jadwal_pemeriksaan_relationship_remains_intact(): void
    {
        $apar = $this->createApar();
        $jadwal = Jadwal::create([
            'nama' => 'Inspeksi Triwulan I 2026',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-03-31',
            'status' => 'selesai',
        ]);

        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'jadwal_id' => $jadwal->id,
            'pemeriksa_nama' => 'Petugas Inspeksi Terjadwal',
            'tanggal_pemeriksaan' => '2026-01-15 08:30:00',
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        $this->assertNotNull($pemeriksaan->jadwal);
        $this->assertEquals($jadwal->id, $pemeriksaan->jadwal->id);
        $this->assertTrue($jadwal->pemeriksaans->contains($pemeriksaan));
    }

    /**
     * 11. K3RS 5-item checklist foundation is cleanly supported.
     */
    public function test_k3rs_checklist_items_foundation_is_defined_and_persisted(): void
    {
        $expectedItems = [
            1 => 'Tekanan',
            2 => 'Pin/Segel',
            3 => 'Handle',
            4 => 'Kondisi Fisik',
            5 => 'Nozel',
        ];

        $this->assertEquals($expectedItems, Pemeriksaan::K3RS_CHECKLIST_ITEMS);

        $apar = $this->createApar();
        $pemeriksaan = Pemeriksaan::create([
            'apar_id' => $apar->id,
            'periode_tahun' => 2026,
            'periode_bulan' => 7,
            'pemeriksa_nama' => 'Petugas K3RS Checklist',
            'tanggal_pemeriksaan' => now(),
            'hasil_keseluruhan' => Pemeriksaan::HASIL_BAIK,
            'status' => 'selesai',
        ]);

        foreach (Pemeriksaan::K3RS_CHECKLIST_ITEMS as $urutan => $namaItem) {
            PemeriksaanDetail::create([
                'pemeriksaan_id' => $pemeriksaan->id,
                'item_nama' => $namaItem,
                'hasil' => PemeriksaanDetail::HASIL_BAIK,
                'urutan' => $urutan,
            ]);
        }

        $this->assertCount(5, $pemeriksaan->details);
        $this->assertEquals('Tekanan', $pemeriksaan->details->first()->item_nama);
        $this->assertEquals('Nozel', $pemeriksaan->details->last()->item_nama);
    }
}
