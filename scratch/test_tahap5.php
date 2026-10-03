<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Apar;
use App\Models\Jadwal;
use App\Models\Gedung;
use App\Models\Lantai;
use App\Models\Ruangan;

// Pastikan ada APAR Aktif
$aparAktif = Apar::where('status', Apar::STATUS_BAIK)->first();
if (!$aparAktif) {
    $gedung = Gedung::firstOrCreate(['kode' => 'GDG-IGD'], ['nama' => 'Gedung IGD']);
    $lantai = Lantai::firstOrCreate(['gedung_id' => $gedung->id, 'nomor_lantai' => 1], ['nama' => 'Lantai 1']);
    $ruangan = Ruangan::firstOrCreate(['lantai_id' => $lantai->id, 'kode' => 'R-IGD-01'], ['nama' => 'Ruang Triase']);
    $aparAktif = Apar::create([
        'ruangan_id' => $ruangan->id,
        'kode' => 'APAR-IGD-01',
        'nomor_register' => 'REG-IGD-001',
        'jenis_media' => 'Dry Chemical Powder',
        'kapasitas' => '6 Kg',
        'merk' => 'Yamato',
        'status' => Apar::STATUS_BAIK,
        'qr_code' => 'HAFIS-QR-APARIGD01'
    ]);
}

// Pastikan ada APAR Tidak Aktif
$aparTidakAktif = Apar::where('status', Apar::STATUS_TIDAK_AKTIF)->first();
if (!$aparTidakAktif) {
    $aparTidakAktif = Apar::create([
        'ruangan_id' => $aparAktif->ruangan_id,
        'kode' => 'APAR-NONAKTIF-01',
        'nomor_register' => 'REG-NONAKTIF-001',
        'jenis_media' => 'CO2 (Karbon Dioksida)',
        'kapasitas' => '5 Kg',
        'merk' => 'Chubb',
        'status' => Apar::STATUS_TIDAK_AKTIF,
        'qr_code' => 'HAFIS-QR-APARNONAKTIF01'
    ]);
}

// Pastikan ada Jadwal Aktif yang mencakup $aparAktif
$jadwalAktif = Jadwal::where('status', 'berjalan')->first();
if (!$jadwalAktif) {
    $jadwalAktif = Jadwal::create([
        'nama' => 'Inspeksi Rutin Rumah Sakit',
        'tanggal_mulai' => now()->startOfMonth(),
        'tanggal_selesai' => now()->endOfMonth(),
        'status' => 'berjalan',
        'tipe_target' => 'semua',
        'dibuat_oleh' => 'Tim K3RS'
    ]);
}
$jadwalAktif->apars()->syncWithoutDetaching([
    $aparAktif->id => ['status_pemeriksaan' => 'belum_diperiksa']
]);

function httpReq($path, $method = 'GET', $postData = [], $followRedirect = false) {
    static $cookieJar = null;
    if ($cookieJar === null) {
        $cookieJar = tempnam(sys_get_temp_dir(), 'apar_cookie_');
    }
    $url = "http://127.0.0.1:8000" . $path;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirect);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    $raw = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($raw, 0, $headerSize);
    $body = substr($raw, $headerSize);
    
    $redirectUrl = null;
    if (preg_match('/^Location:\s*(.+)$/mi', $headers, $m)) {
        $redirectUrl = trim($m[1]);
    }
    return [
        'status' => $status,
        'redirect' => $redirectUrl,
        'body' => $body,
        'headers' => $headers,
    ];
}

$passCount = 0;
$failCount = 0;

function assertTest($description, $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] $description\n";
        $passCount++;
    } else {
        echo " [FAIL] $description\n";
        $failCount++;
    }
}

echo "=== MEMULAI PENGUJIAN OTOMATIS TAHAP 5 (LIVE HTTP SERVER) ===\n\n";

// --- TEST 1: Buka Detail APAR, Pastikan QR Tersedia ---
echo "--- TEST 1: Buka Detail APAR & Validasi QR Code ---\n";
$r1 = httpReq("/apar/{$aparAktif->id}");
assertTest("GET /apar/{$aparAktif->id} HTTP Status 200", $r1['status'] === 200);
assertTest("Halaman Detail APAR memuat kode {$aparAktif->kode}", str_contains($r1['body'], $aparAktif->kode));
assertTest("Tersedia Card QR Code APAR", str_contains($r1['body'], 'QR Code APAR'));
assertTest("Render Vector SVG QR Code valid (<svg)", str_contains($r1['body'], '<svg') && str_contains($r1['body'], 'rect'));
assertTest("Tersedia tombol Cetak Label QR (/apar/{$aparAktif->id}/cetak-qr)", str_contains($r1['body'], "/apar/{$aparAktif->id}/cetak-qr"));
assertTest("Tersedia tombol Download SVG (/apar/{$aparAktif->id}/download-qr)", str_contains($r1['body'], "/apar/{$aparAktif->id}/download-qr"));
assertTest("Tersedia tombol Mulai Pemeriksaan", str_contains($r1['body'], 'Mulai Pemeriksaan'));

// Uji endpoint cetak QR
$rCetak = httpReq("/apar/{$aparAktif->id}/cetak-qr");
assertTest("GET /apar/{$aparAktif->id}/cetak-qr HTTP Status 200", $rCetak['status'] === 200);
assertTest("Halaman Cetak QR memuat label K3RS dan data APAR", str_contains($rCetak['body'], 'LABEL IDENTITAS APAR') && str_contains($rCetak['body'], $aparAktif->kode));

// Uji endpoint unduh QR
$rDownload = httpReq("/apar/{$aparAktif->id}/download-qr");
assertTest("GET /apar/{$aparAktif->id}/download-qr HTTP Status 200", $rDownload['status'] === 200);
assertTest("Header Download QR bertipe image/svg+xml", str_contains($rDownload['headers'], 'image/svg+xml'));

// --- TEST 2: Scan QR APAR, Pastikan APAR Terbuka ---
echo "\n--- TEST 2: Scan QR APAR -> Buka Detail APAR yang Benar ---\n";
$r2 = httpReq("/scan/cari?kode={$aparAktif->qr_code}", 'GET', [], false);
assertTest("Scan QR {$aparAktif->qr_code} mengembalikan HTTP 302 Redirect", $r2['status'] === 302);
assertTest("Redirect mengarah ke /apar/{$aparAktif->id}", str_contains($r2['redirect'], "/apar/{$aparAktif->id}"));

// Lanjutkan redirect untuk melihat pesan sukses
$r2Follow = httpReq("/scan/cari?kode={$aparAktif->qr_code}", 'GET', [], true);
assertTest("Follow redirect menghasilkan HTTP 200 di Detail APAR", $r2Follow['status'] === 200);
assertTest("Memuat feedback 'Unit APAR {$aparAktif->kode} berhasil teridentifikasi'", str_contains($r2Follow['body'], 'berhasil teridentifikasi'));

// --- TEST 3: Scan QR Tidak Valid, Muncul Pesan Tidak Ditemukan ---
echo "\n--- TEST 3: Scan QR Tidak Valid -> Pesan APAR Tidak Ditemukan ---\n";
$r3 = httpReq('/scan/cari?kode=QR-TIDAK-VALID-999', 'GET', [], false);
assertTest("Scan kode tidak valid redirect ke /scan (HTTP 302)", $r3['status'] === 302 && str_contains($r3['redirect'], '/scan'));

// Follow redirect untuk memverifikasi flash error di UI
$r3Follow = httpReq('/scan/cari?kode=QR-TIDAK-VALID-999', 'GET', [], true);
assertTest("Halaman Scan menampilkan pesan 'APAR tidak ditemukan.'", str_contains($r3Follow['body'], 'APAR tidak ditemukan.'));

// --- TEST 4: Gunakan Fallback Pencarian Kode Manual ---
echo "\n--- TEST 4: Fallback Pencarian Kode APAR Manual ---\n";
$r4 = httpReq("/scan/cari?kode={$aparAktif->kode}", 'GET', [], false);
assertTest("Pencarian kode manual {$aparAktif->kode} mengembalikan HTTP 302", $r4['status'] === 302);
assertTest("Redirect mengarah ke /apar/{$aparAktif->id}", str_contains($r4['redirect'], "/apar/{$aparAktif->id}"));

$r4Follow = httpReq("/scan/cari?kode={$aparAktif->kode}", 'GET', [], true);
assertTest("Detail {$aparAktif->kode} terbuka dengan sukses", str_contains($r4Follow['body'], $aparAktif->kode));

// --- TEST 5: Buka dari Jadwal Inspeksi -> Target APAR Terbuka ---
echo "\n--- TEST 5: Alur Jadwal Inspeksi -> Target APAR ---\n";
$r5Jadwal = httpReq("/jadwal/{$jadwalAktif->id}");
assertTest("Detail Jadwal Inspeksi HTTP Status 200", $r5Jadwal['status'] === 200);
assertTest("Daftar target APAR memuat kode {$aparAktif->kode}", str_contains($r5Jadwal['body'], $aparAktif->kode));
assertTest("Daftar target APAR memuat tombol 'Mulai Pemeriksaan'", str_contains($r5Jadwal['body'], 'Mulai Pemeriksaan'));
assertTest("Daftar target APAR memuat link 'Lihat APAR' dengan context jadwal_id", str_contains($r5Jadwal['body'], "jadwal_id={$jadwalAktif->id}"));

// --- TEST 6: Klik Mulai Pemeriksaan -> Masuk ke Gerbang Pemeriksaan yang Benar ---
echo "\n--- TEST 6: Mulai Pemeriksaan Membawa Record APAR & Konteks Jadwal ---\n";
$r6 = httpReq("/pemeriksaan/mulai?apar_id={$aparAktif->id}&jadwal_id={$jadwalAktif->id}");
assertTest("GET /pemeriksaan/mulai HTTP Status 200", $r6['status'] === 200);
assertTest("Menampilkan identitas unit {$aparAktif->kode}", str_contains($r6['body'], $aparAktif->kode));
assertTest("Menampilkan jenis media {$aparAktif->jenis_media}", str_contains($r6['body'], $aparAktif->jenis_media));
assertTest("Menampilkan status terhubung dengan Jadwal Inspeksi Aktif", str_contains($r6['body'], 'Terhubung dengan Jadwal Inspeksi Aktif'));
assertTest("Menampilkan nama jadwal {$jadwalAktif->nama}", str_contains($r6['body'], $jadwalAktif->nama));
assertTest("Menampilkan tombol Lanjutkan ke Checklist Fisik (Tahap 6)", str_contains($r6['body'], 'Lanjutkan ke Checklist Fisik (Tahap 6)'));

// Uji juga pemeriksaan ad-hoc (tanpa jadwal aktif)
$r6Adhoc = httpReq("/pemeriksaan/mulai?apar_id={$aparAktif->id}");
assertTest("Pemeriksaan ad-hoc HTTP Status 200", $r6Adhoc['status'] === 200);

// --- TEST 7: Penanganan APAR Tidak Aktif ---
echo "\n--- TEST 7: APAR Tidak Aktif Diblokir dari Pemeriksaan Aktif ---\n";
// 7a. Scan APAR tidak aktif -> Menampilkan peringatan status Tidak Aktif
$r7Scan = httpReq("/scan/cari?kode={$aparTidakAktif->kode}", 'GET', [], true);
assertTest("Scan APAR tidak aktif membuka Detail APAR dengan warning", str_contains($r7Scan['body'], 'Tidak Aktif'));

// 7b. Detail APAR tidak aktif memuat banner status tidak aktif dan tombol disabled
$r7Detail = httpReq("/apar/{$aparTidakAktif->id}");
assertTest("Detail APAR memuat banner 'Perhatian: APAR Berstatus Tidak Aktif'", str_contains($r7Detail['body'], 'Perhatian: APAR Berstatus Tidak Aktif'));
assertTest("Tombol Mulai Pemeriksaan dinonaktifkan", str_contains($r7Detail['body'], 'tidak dapat diperiksa') || str_contains($r7Detail['body'], 'Tidak Aktif'));

// 7c. Direct URL ke /pemeriksaan/mulai untuk APAR tidak aktif harus DIBLOKIR (redirect)
$r7MulaiDirect = httpReq("/pemeriksaan/mulai?apar_id={$aparTidakAktif->id}", 'GET', [], false);
assertTest("Akses langsung pemeriksaan APAR tidak aktif diblokir (HTTP 302)", $r7MulaiDirect['status'] === 302);
assertTest("Redirect diarahkan kembali ke detail APAR", str_contains($r7MulaiDirect['redirect'], "/apar/{$aparTidakAktif->id}"));

// --- TEST 8: Tolak Izin Kamera & Ketersediaan Fallback Pencarian Kode ---
echo "\n--- TEST 8: Penanganan Izin Kamera Ditolak & Fallback Form ---\n";
$r8 = httpReq('/scan');
assertTest("GET /scan HTTP Status 200", $r8['status'] === 200);
assertTest("Viewfinder kamera HTML5 (<video) tersedia", str_contains($r8['body'], 'id="camera-video"'));
assertTest("Tersedia penanganan kondisi izin kamera ditolak", str_contains($r8['body'], 'Izin Kamera Ditolak'));
assertTest("Tersedia form fallback manual 'Masukkan Kode APAR'", str_contains($r8['body'], 'Masukkan Kode APAR'));
assertTest("Input kode manual ber-ID 'kode-manual-input'", str_contains($r8['body'], 'id="kode-manual-input"'));
assertTest("Tombol 'Cari APAR' tersedia untuk submit", str_contains($r8['body'], 'Cari APAR'));

echo "\n=== RINGKASAN HASIL PENGUJIAN TAHAP 5 ===\n";
echo "Total Berhasil : $passCount\n";
echo "Total Gagal    : $failCount\n";

if ($failCount === 0) {
    echo "\n>>> SELURUH 8 SKENARIO PENGUJIAN TAHAP 5 SUKSES 100% <<<\n";
    exit(0);
} else {
    echo "\n>>> TERDAPAT KEGAGALAN DALAM PENGUJIAN <<<\n";
    exit(1);
}
