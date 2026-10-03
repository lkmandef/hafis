<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

use App\Http\Controllers\AparController;
use App\Http\Controllers\PenggantianAparController;
use App\Models\Apar;

Route::prefix('apar')->name('apar.')->group(function () {
    Route::get('/', [AparController::class, 'index'])->name('index');
    Route::get('/create', [AparController::class, 'create'])->name('create');
    Route::post('/', [AparController::class, 'store'])->name('store');

    // Kompatibilitas link statis lama (/apar/detail dan /apar/edit)
    Route::get('/detail', function () {
        $first = Apar::first();

        return $first ? redirect()->route('apar.show', $first->id) : redirect()->route('apar.index');
    });
    Route::get('/edit', function () {
        $first = Apar::first();

        return $first ? redirect()->route('apar.edit', $first->id) : redirect()->route('apar.index');
    });

    Route::get('/{id}', [AparController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [AparController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AparController::class, 'update'])->name('update');
    Route::delete('/{id}', [AparController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/cetak-qr', [AparController::class, 'cetakQr'])->name('cetak_qr');
    Route::get('/{id}/download-qr', [AparController::class, 'downloadQr'])->name('download_qr');

    // Penggantian Unit APAR (Tahap 8)
    Route::get('/{id}/ganti', [PenggantianAparController::class, 'create'])->name('ganti');
    Route::post('/{id}/ganti', [PenggantianAparController::class, 'store'])->name('ganti.store');
});

use App\Http\Controllers\JadwalController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\ScanController;

// Modul Jadwal Inspeksi APAR (Tahap 4)
Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/', [JadwalController::class, 'index'])->name('index');
    Route::get('/create', [JadwalController::class, 'create'])->name('create');
    Route::post('/', [JadwalController::class, 'store'])->name('store');
    Route::get('/{jadwal}', [JadwalController::class, 'show'])->name('show');
    Route::get('/{jadwal}/edit', [JadwalController::class, 'edit'])->name('edit');
    Route::put('/{jadwal}', [JadwalController::class, 'update'])->name('update');
    Route::post('/{jadwal}/batalkan', [JadwalController::class, 'batalkan'])->name('batalkan');
    Route::delete('/{jadwal}', [JadwalController::class, 'destroy'])->name('destroy');
});

// Modul Scan QR APAR (Tahap 5)
Route::prefix('scan')->name('scan.')->group(function () {
    Route::get('/', [ScanController::class, 'index'])->name('index');
    Route::get('/cari', [ScanController::class, 'cari'])->name('cari');
    Route::post('/cari', [ScanController::class, 'cari'])->name('cari.post');
});

// Modul Pemeriksaan APAR (Tahap 5 & 6)
Route::prefix('pemeriksaan')->name('pemeriksaan.')->group(function () {
    Route::get('/', [PemeriksaanController::class, 'index'])->name('index');
    Route::get('/mulai', [PemeriksaanController::class, 'mulai'])->name('mulai');
    Route::get('/create', [PemeriksaanController::class, 'create'])->name('create');
    Route::post('/', [PemeriksaanController::class, 'store'])->name('store');
    Route::get('/{pemeriksaan}', [PemeriksaanController::class, 'show'])->name('show');
});

use App\Http\Controllers\TindakLanjutController;

// Modul Tindak Lanjut & Pemeliharaan APAR (Tahap 7)
Route::prefix('tindak-lanjut')->name('tindak-lanjut.')->group(function () {
    Route::get('/', [TindakLanjutController::class, 'index'])->name('index');
    Route::get('/create', [TindakLanjutController::class, 'create'])->name('create');
    Route::post('/', [TindakLanjutController::class, 'store'])->name('store');
    Route::get('/{tindakLanjut}', [TindakLanjutController::class, 'show'])->name('show');
    Route::get('/{tindakLanjut}/edit', [TindakLanjutController::class, 'edit'])->name('edit');
    Route::put('/{tindakLanjut}', [TindakLanjutController::class, 'update'])->name('update');
    Route::patch('/{tindakLanjut}/status', [TindakLanjutController::class, 'updateStatus'])->name('status');
    Route::delete('/{tindakLanjut}', [TindakLanjutController::class, 'destroy'])->name('destroy');
});

// Modul Penggantian & Riwayat Penggantian APAR (Tahap 8)
Route::prefix('penggantian')->name('penggantian.')->group(function () {
    Route::get('/', [PenggantianAparController::class, 'index'])->name('index');
    Route::get('/{penggantian}', [PenggantianAparController::class, 'show'])->name('show');
});

use App\Http\Controllers\LaporanController;

// Modul Laporan & Rekapitulasi (Tahap 9)
Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/apar', [LaporanController::class, 'apar'])->name('apar');
    Route::get('/pemeriksaan', [LaporanController::class, 'pemeriksaan'])->name('pemeriksaan');
    Route::get('/temuan', [LaporanController::class, 'temuan'])->name('temuan');
    Route::get('/tindak-lanjut', [LaporanController::class, 'tindakLanjut'])->name('tindak_lanjut');
    Route::get('/tindak-lanjut-alias', [LaporanController::class, 'tindakLanjut'])->name('tindak-lanjut');
    Route::get('/penggantian', [LaporanController::class, 'penggantian'])->name('penggantian');
});

// Kompatibilitas link lama /riwayat -> /laporan
Route::get('/riwayat', function () {
    return redirect()->route('laporan.index');
})->name('riwayat.index');

Route::get('/riwayat/detail', function () {
    return redirect()->route('laporan.pemeriksaan');
});

Route::get('/riwayat/{id}', function () {
    return redirect()->route('laporan.pemeriksaan');
});

use App\Http\Controllers\DenahController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PenempatanController;

// Modul Titik Penempatan APAR pada Denah (Gedung -> Lantai -> Denah -> Marker APAR)
Route::prefix('penempatan')->name('penempatan.')->group(function () {
    Route::get('/', [PenempatanController::class, 'index'])->name('index');
    Route::post('/apar/{apar}/titik', [PenempatanController::class, 'simpanTitik'])->name('titik.simpan');
    Route::delete('/apar/{apar}/titik', [PenempatanController::class, 'hapusTitik'])->name('titik.hapus');

    // Kompatibilitas link atur lama
    Route::get('/atur', function () {
        return redirect()->route('penempatan.index');
    });
    Route::get('/atur/{id}', function ($id = 'APAR-IGD-01') {
        return redirect()->route('penempatan.index', ['highlight' => $id]);
    });
});

// Modul Struktur Lokasi Rumah Sakit (Gedung -> Lantai -> Ruangan/Area)
Route::prefix('lokasi')->name('lokasi.')->group(function () {
    Route::get('/', [LokasiController::class, 'index'])->name('index');
    Route::get('/gedung/{gedung}', [LokasiController::class, 'showGedung'])->name('gedung.show');

    // Gedung
    Route::post('/gedung', [LokasiController::class, 'storeGedung'])->name('gedung.store');
    Route::put('/gedung/{gedung}', [LokasiController::class, 'updateGedung'])->name('gedung.update');
    Route::delete('/gedung/{gedung}', [LokasiController::class, 'destroyGedung'])->name('gedung.destroy');

    // Lantai
    Route::post('/lantai', [LokasiController::class, 'storeLantai'])->name('lantai.store');
    Route::put('/lantai/{lantai}', [LokasiController::class, 'updateLantai'])->name('lantai.update');
    Route::delete('/lantai/{lantai}', [LokasiController::class, 'destroyLantai'])->name('lantai.destroy');

    // Denah Lantai (Upload & Hapus)
    Route::post('/lantai/{lantai}/denah', [DenahController::class, 'store'])->name('lantai.denah.store');
    Route::delete('/lantai/{lantai}/denah', [DenahController::class, 'destroy'])->name('lantai.denah.destroy');

    // Ruangan / Area
    Route::post('/ruangan', [LokasiController::class, 'storeRuangan'])->name('ruangan.store');
    Route::put('/ruangan/{ruangan}', [LokasiController::class, 'updateRuangan'])->name('ruangan.update');
    Route::delete('/ruangan/{ruangan}', [LokasiController::class, 'destroyRuangan'])->name('ruangan.destroy');
});
