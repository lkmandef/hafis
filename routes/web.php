<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

use App\Http\Controllers\AparController;
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
});

use App\Http\Controllers\JadwalController;

// Modul Jadwal Inspeksi APAR (Tahap 4)
Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/', [JadwalController::class, 'index'])->name('index');
    Route::get('/create', [JadwalController::class, 'create'])->name('create');
    Route::post('/', [JadwalController::class, 'store'])->name('store');
    Route::get('/{jadwal}', [JadwalController::class, 'show'])->name('show');
    Route::get('/{jadwal}/edit', [JadwalController::class, 'edit'])->name('edit');
    Route::put('/{jadwal}', [JadwalController::class, 'update'])->name('update');
    Route::delete('/{jadwal}', [JadwalController::class, 'destroy'])->name('destroy');
    Route::patch('/{jadwal}/batalkan', [JadwalController::class, 'batalkan'])->name('batalkan');
});

Route::get('/pemeriksaan', function () {
    return view('pemeriksaan.create');
});

Route::get('/riwayat', function () {
    return view('riwayat.index');
});

Route::get('/riwayat/detail', function () {
    return view('riwayat.show');
});

Route::get('/riwayat/{id}', function ($id = 'INS-2026-001') {
    return view('riwayat.show', ['id' => $id]);
});

Route::get('/scan', function () {
    return view('scan.index');
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
