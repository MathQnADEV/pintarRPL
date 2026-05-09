<?php

use App\Http\Controllers\Dosen\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
});

// ── Dosen Exports ────────────────────────────────────────────────────────────
// Dilindungi auth + role dosen; kelas_id divalidasi di controller (milik dosen ybs)
Route::middleware(['auth', 'role:dosen'])
    ->prefix('dosen/export')
    ->name('dosen.export.')
    ->group(function () {
        Route::get('mahasiswa/csv', [ExportController::class, 'csv'])->name('mahasiswa.csv');
        Route::get('mahasiswa/pdf', [ExportController::class, 'pdf'])->name('mahasiswa.pdf');
    });
Route::get('/mahasiswa', function () {
    return view('dashboard-mahasiswa');
});

Route::get('/dosen', function () {
    return view('dashboard-dosen');
});

Route::get('/materi', function () { return view('materi'); });
Route::get('/kuis', function () { return view('kuis'); });
Route::get('/progres', function () { return view('progres'); });
Route::get('/profil', function () { return view('profil'); });
