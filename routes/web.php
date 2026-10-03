<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/apar', function () {
    return view('apar.index');
});

Route::get('/apar/create', function () {
    return view('apar.create');
});

Route::get('/apar/edit', function () {
    return view('apar.edit');
});

Route::get('/apar/{id}/edit', function ($id = 'APAR-IGD-01') {
    return view('apar.edit', ['id' => $id]);
});

Route::get('/apar/detail', function () {
    return view('apar.show');
});

Route::get('/apar/{id}', function ($id = 'APAR-IGD-01') {
    return view('apar.show', ['id' => $id]);
});

Route::get('/jadwal', function () {
    return view('jadwal.index');
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

Route::get('/penempatan', function () {
    return view('penempatan.index');
});

Route::get('/penempatan/atur', function () {
    return view('penempatan.atur');
});

use App\Http\Controllers\LokasiController;

Route::get('/penempatan/atur/{id}', function ($id = 'APAR-IGD-01') {
    return view('penempatan.atur', ['id' => $id]);
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

    // Ruangan / Area
    Route::post('/ruangan', [LokasiController::class, 'storeRuangan'])->name('ruangan.store');
    Route::put('/ruangan/{ruangan}', [LokasiController::class, 'updateRuangan'])->name('ruangan.update');
    Route::delete('/ruangan/{ruangan}', [LokasiController::class, 'destroyRuangan'])->name('ruangan.destroy');
});
