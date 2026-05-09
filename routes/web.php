<?php

use App\Http\Controllers\Dosen\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
