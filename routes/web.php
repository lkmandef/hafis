<?php

use App\Http\Controllers\AparController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChecklistMasterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\PenggantianAparController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TindakLanjutController;
use App\Http\Controllers\UserController;
use App\Models\Apar;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Autentikasi Publik (Tamu)
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rute Internal Terproteksi Autentikasi (Petugas K3RS & Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Modul Master Data APAR (Operasional)
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
        Route::get('/{id}/cetak-qr', [AparController::class, 'cetakQr'])->name('cetak_qr');
        Route::get('/{id}/download-qr', [AparController::class, 'downloadQr'])->name('download_qr');

        // Penggantian Unit APAR (Tahap 8)
        Route::get('/{id}/ganti', [PenggantianAparController::class, 'create'])->name('ganti');
        Route::post('/{id}/ganti', [PenggantianAparController::class, 'store'])->name('ganti.store');
    });

    // Modul Jadwal Inspeksi APAR (Operasional)
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [JadwalController::class, 'index'])->name('index');
        Route::get('/create', [JadwalController::class, 'create'])->name('create');
        Route::post('/', [JadwalController::class, 'store'])->name('store');
        Route::get('/{jadwal}', [JadwalController::class, 'show'])->name('show');
        Route::get('/{jadwal}/edit', [JadwalController::class, 'edit'])->name('edit');
        Route::put('/{jadwal}', [JadwalController::class, 'update'])->name('update');
    });

    // Modul Scan QR APAR (Tahap 5)
    Route::prefix('scan')->name('scan.')->group(function () {
        Route::get('/', [ScanController::class, 'index'])->name('index');
        Route::get('/cari', [ScanController::class, 'cari'])->name('cari');
        Route::post('/cari', [ScanController::class, 'cari'])->name('cari.post');
    });

    // Modul Pemeriksaan Fisik APAR (Tahap 5 & 6)
    Route::prefix('pemeriksaan')->name('pemeriksaan.')->group(function () {
        Route::get('/', [PemeriksaanController::class, 'index'])->name('index');
        Route::get('/mulai', [PemeriksaanController::class, 'mulai'])->name('mulai');
        Route::get('/create', [PemeriksaanController::class, 'create'])->name('create');
        Route::post('/', [PemeriksaanController::class, 'store'])->name('store');
        Route::get('/{pemeriksaan}', [PemeriksaanController::class, 'show'])->name('show');
        Route::get('/{pemeriksaan}/edit', [PemeriksaanController::class, 'edit'])->name('edit');
        Route::put('/{pemeriksaan}', [PemeriksaanController::class, 'update'])->name('update');
    });

    // Modul Tindak Lanjut & Pemeliharaan APAR (Tahap 7 - Operasional)
    Route::prefix('tindak-lanjut')->name('tindak-lanjut.')->group(function () {
        Route::get('/', [TindakLanjutController::class, 'index'])->name('index');
        Route::get('/create', [TindakLanjutController::class, 'create'])->name('create');
        Route::post('/', [TindakLanjutController::class, 'store'])->name('store');
        Route::get('/{tindakLanjut}', [TindakLanjutController::class, 'show'])->name('show');
        Route::get('/{tindakLanjut}/edit', [TindakLanjutController::class, 'edit'])->name('edit');
        Route::put('/{tindakLanjut}', [TindakLanjutController::class, 'update'])->name('update');
        Route::patch('/{tindakLanjut}/status', [TindakLanjutController::class, 'updateStatus'])->name('status');
    });

    // Modul Penggantian & Riwayat Penggantian APAR (Tahap 8)
    Route::prefix('penggantian')->name('penggantian.')->group(function () {
        Route::get('/', [PenggantianAparController::class, 'index'])->name('index');
        Route::get('/{penggantian}', [PenggantianAparController::class, 'show'])->name('show');
    });

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

    // Modul Struktur Lokasi Rumah Sakit (Lihat Struktur)
    Route::prefix('lokasi')->name('lokasi.')->group(function () {
        Route::get('/', [LokasiController::class, 'index'])->name('index');
        Route::get('/gedung/{gedung}', [LokasiController::class, 'showGedung'])->name('gedung.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Rute Khusus Kepala K3RS (Admin: role:admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        // Manajemen Pengguna & Akses
        Route::prefix('pengguna')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/tambah', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });

        // Pengaturan Sistem: Master Checklist Pemeriksaan Fisik APAR
        Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
            Route::prefix('checklist')->name('checklist.')->group(function () {
                Route::get('/', [ChecklistMasterController::class, 'index'])->name('index');
                Route::post('/', [ChecklistMasterController::class, 'store'])->name('store');
                Route::put('/{checklist}', [ChecklistMasterController::class, 'update'])->name('update');
                Route::post('/{checklist}/toggle-status', [ChecklistMasterController::class, 'toggleStatus'])->name('toggle-status');
                Route::delete('/{checklist}', [ChecklistMasterController::class, 'destroy'])->name('destroy');
            });
        });

        // Tindakan Administratif / Destruktif
        Route::delete('/apar/{id}', [AparController::class, 'destroy'])->name('apar.destroy');
        Route::delete('/tindak-lanjut/{tindakLanjut}', [TindakLanjutController::class, 'destroy'])->name('tindak-lanjut.destroy');
        Route::post('/jadwal/{jadwal}/batalkan', [JadwalController::class, 'batalkan'])->name('jadwal.batalkan');
        Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

        // Mutasi Master Struktur Lokasi (Gedung, Lantai, Ruangan)
        Route::prefix('lokasi')->name('lokasi.')->group(function () {
            Route::post('/gedung', [LokasiController::class, 'storeGedung'])->name('gedung.store');
            Route::put('/gedung/{gedung}', [LokasiController::class, 'updateGedung'])->name('gedung.update');
            Route::delete('/gedung/{gedung}', [LokasiController::class, 'destroyGedung'])->name('gedung.destroy');

            Route::post('/lantai', [LokasiController::class, 'storeLantai'])->name('lantai.store');
            Route::put('/lantai/{lantai}', [LokasiController::class, 'updateLantai'])->name('lantai.update');
            Route::delete('/lantai/{lantai}', [LokasiController::class, 'destroyLantai'])->name('lantai.destroy');

            Route::post('/ruangan', [LokasiController::class, 'storeRuangan'])->name('ruangan.store');
            Route::put('/ruangan/{ruangan}', [LokasiController::class, 'updateRuangan'])->name('ruangan.update');
            Route::delete('/ruangan/{ruangan}', [LokasiController::class, 'destroyRuangan'])->name('ruangan.destroy');
        });
    });
});
