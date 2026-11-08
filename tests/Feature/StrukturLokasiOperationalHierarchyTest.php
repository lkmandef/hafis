<?php

namespace Tests\Feature;

use App\Models\Apar;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Lokasi;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StrukturLokasiOperationalHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    /**
     * Verifikasi bahwa halaman /lokasi menampilkan titik lokasi operasional pada setiap lantai
     */
    public function test_lokasi_index_displays_operational_locations_under_floors(): void
    {
        $gedung = Gedung::where('kode', 'GDG-FRM')->first();
        if (! $gedung) {
            $gedung = Gedung::create([
                'nama' => 'Gedung Farmasi & Business Center',
                'kode' => 'GDG-FRM',
                'status' => 'aktif',
            ]);
        }

        $lantai1 = Lantai::firstOrCreate(
            ['gedung_id' => $gedung->id, 'nomor_lantai' => 1],
            ['nama' => 'Lantai 1']
        );

        $lantai2 = Lantai::firstOrCreate(
            ['gedung_id' => $gedung->id, 'nomor_lantai' => 2],
            ['nama' => 'Lantai 2']
        );

        // Lokasi Lantai 1
        $lokasiFarmasi = Lokasi::create([
            'kode' => 'LOK-FBC-01',
            'nama' => 'Instalasi Farmasi',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai1->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'aktif',
        ]);

        // Lokasi Lantai 2
        $lokasiItDalam = Lokasi::create([
            'kode' => 'LOK-FBC-02',
            'nama' => 'IT Dalam',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai2->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'aktif',
        ]);

        $lokasiItLuar = Lokasi::create([
            'kode' => 'LOK-FBC-03',
            'nama' => 'IT Luar',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai2->id,
            'tipe_lokasi' => Lokasi::TIPE_CORRIDOR,
            'status' => 'aktif',
        ]);

        $lokasiK3rs = Lokasi::create([
            'kode' => 'LOK-FBC-04',
            'nama' => 'K3RS',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai2->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'aktif',
        ]);

        $lokasiGudangK3rs = Lokasi::create([
            'kode' => 'LOK-FBC-05',
            'nama' => 'Gudang K3RS',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai2->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'stok',
        ]);

        // Pasang 1 APAR operasional di Instalasi Farmasi
        Apar::create([
            'kode' => 'APAR-FBC-001',
            'qr_code' => 'HAFIS-QR-FBC001',
            'lokasi_id' => $lokasiFarmasi->id,
            'posisi_detail' => 'Dinding Dekat Pintu Masuk Farmasi',
            'jenis_media' => 'Dry Chemical Powder',
            'kapasitas' => '6 kg',
            'status' => Apar::STATUS_BAIK,
        ]);

        $response = $this->get(route('lokasi.index'));
        $response->assertOk();

        // Verifikasi variabel diteruskan ke view
        $response->assertViewHas('totalLokasi');
        $response->assertViewHas('allLokasis');

        // Verifikasi nama lokasi operasional tampil di halaman
        $response->assertSee('Instalasi Farmasi');
        $response->assertSee('IT Dalam');
        $response->assertSee('IT Luar');
        $response->assertSee('K3RS');
        $response->assertSee('Gudang K3RS');

        // Verifikasi tipe lokasi dan status stok
        $response->assertSee('Koridor / Selasar');
        $response->assertSee('Stok Sediaan');

        // Verifikasi tidak ada pesan lantai kosong pada lantai yang memiliki lokasi operasional
        $response->assertDontSee('Belum ada ruangan/area terdaftar pada lantai ini');
    }

    /**
     * Verifikasi bahwa halaman detail gedung /lokasi/gedung/{id} menampilkan titik lokasi operasional
     */
    public function test_lokasi_show_gedung_displays_operational_locations(): void
    {
        $gedung = Gedung::where('kode', 'GDG-FRM')->first();
        if (! $gedung) {
            $gedung = Gedung::create([
                'nama' => 'Gedung Farmasi & Business Center',
                'kode' => 'GDG-FRM',
                'status' => 'aktif',
            ]);
        }

        $lantai1 = Lantai::firstOrCreate(
            ['gedung_id' => $gedung->id, 'nomor_lantai' => 1],
            ['nama' => 'Lantai 1']
        );

        Lokasi::create([
            'kode' => 'LOK-FBC-01',
            'nama' => 'Instalasi Farmasi',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai1->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'aktif',
        ]);

        $response = $this->get(route('lokasi.gedung.show', $gedung->id));
        $response->assertOk();

        $response->assertSee('Gedung Farmasi &amp; Business Center', false);
        $response->assertSee('Instalasi Farmasi');
        $response->assertSee('Titik Lokasi APAR');
    }

    /**
     * Verifikasi bahwa pencarian ?q= mencakup nama dan kode titik lokasi operasional
     */
    public function test_lokasi_index_search_filters_by_operational_location_name_or_code(): void
    {
        $gedungA = Gedung::create([
            'nama' => 'Gedung Uji Laboratorium Farmasi Khusus',
            'kode' => 'GDG-UJI-FRM',
            'status' => 'aktif',
        ]);

        $lantaiA = Lantai::create([
            'gedung_id' => $gedungA->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        Lokasi::create([
            'kode' => 'LOK-UJI-FRM-01',
            'nama' => 'Instalasi Uji Farmasi Langka',
            'gedung_id' => $gedungA->id,
            'lantai_id' => $lantaiA->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'aktif',
        ]);

        $gedungB = Gedung::create([
            'nama' => 'Gedung Arsip Administrasi Umum',
            'kode' => 'GDG-UJI-ADM',
            'status' => 'aktif',
        ]);

        $lantaiB = Lantai::create([
            'gedung_id' => $gedungB->id,
            'nomor_lantai' => 1,
            'nama' => 'Lantai 1',
        ]);

        Lokasi::create([
            'kode' => 'LOK-UJI-ADM-01',
            'nama' => 'Ruang Berkas Kepegawaian',
            'gedung_id' => $gedungB->id,
            'lantai_id' => $lantaiB->id,
            'tipe_lokasi' => Lokasi::TIPE_ROOM,
            'status' => 'aktif',
        ]);

        // Cari berdasarkan nama unik lokasi: "Uji Farmasi Langka"
        $resFarmasi = $this->get(route('lokasi.index', ['q' => 'Uji Farmasi Langka']));
        $resFarmasi->assertOk();
        $gedungsFoundFarmasi = $resFarmasi->viewData('gedungs');
        $this->assertTrue($gedungsFoundFarmasi->contains('id', $gedungA->id));
        $this->assertFalse($gedungsFoundFarmasi->contains('id', $gedungB->id));

        // Cari berdasarkan kode unik lokasi: "LOK-UJI-ADM-01"
        $resAdm = $this->get(route('lokasi.index', ['q' => 'LOK-UJI-ADM-01']));
        $resAdm->assertOk();
        $gedungsFoundAdm = $resAdm->viewData('gedungs');
        $this->assertTrue($gedungsFoundAdm->contains('id', $gedungB->id));
        $this->assertFalse($gedungsFoundAdm->contains('id', $gedungA->id));
    }

    /**
     * Verifikasi bahwa dualitas Master Ruangan dan Titik Lokasi APAR tidak tercampur/bertabrakan
     */
    public function test_lokasi_index_distinguishes_between_operational_locations_and_physical_rooms(): void
    {
        $gedung = Gedung::create([
            'nama' => 'Gedung Rawat Inap Terpadu Uji',
            'kode' => 'GDG-RIT-UJI',
            'status' => 'aktif',
        ]);

        $lantai = Lantai::create([
            'gedung_id' => $gedung->id,
            'nomor_lantai' => 3,
            'nama' => 'Lantai 3',
        ]);

        // Titik lokasi operasional APAR
        Lokasi::create([
            'kode' => 'LOK-RIT-SELASAR',
            'nama' => 'Selasar Depan Nurse Station',
            'gedung_id' => $gedung->id,
            'lantai_id' => $lantai->id,
            'tipe_lokasi' => Lokasi::TIPE_CORRIDOR,
            'status' => 'aktif',
        ]);

        // Master ruangan fisik
        Ruangan::create([
            'lantai_id' => $lantai->id,
            'kode' => 'RNG-VIP-301',
            'nama' => 'Kamar Rawat VIP 301',
            'kategori' => 'rawat_inap',
        ]);

        $response = $this->get(route('lokasi.index'));
        $response->assertOk();

        // Keduanya tampil di bawah lantai yang sama
        $response->assertSee('Selasar Depan Nurse Station');
        $response->assertSee('Kamar Rawat VIP 301');

        // Memuat badge dan sub-bagian master ruangan fisik
        $response->assertSee('Master Ruangan Fisik');
        $response->assertSee('RNG-VIP-301');
    }
}
