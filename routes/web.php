<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen\ExportController;
use App\Http\Controllers\Mahasiswa\DashboardController;
use App\Http\Controllers\Mahasiswa\KuisController;
use App\Http\Controllers\Mahasiswa\MateriController;
use App\Http\Controllers\Mahasiswa\ProgresController;
use App\Http\Controllers\Mahasiswa\ProfilController;
use App\Http\Controllers\Mahasiswa\PretestController;
use App\Http\Controllers\Mahasiswa\PostTestController;
use App\Http\Controllers\Mahasiswa\ReviewController;
use Illuminate\Support\Facades\Route;

// ── Auth ─────────────────────────────────────────────────────────────────────

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Mahasiswa (auth + role + pretest check) ───────────────────────────────────

Route::middleware(['auth', 'role:mahasiswa', 'mahasiswa.pretest'])
    ->group(function () {
        Route::get('/mahasiswa', [DashboardController::class, 'index'])
            ->name('mahasiswa.dashboard');

        Route::get('/pretest', [PretestController::class, 'show'])
            ->name('mahasiswa.pretest');

        Route::post('/pretest', [PretestController::class, 'submit'])
            ->name('mahasiswa.pretest.submit');

        Route::get('/pretest/hasil', [PretestController::class, 'hasil'])
            ->name('mahasiswa.pretest.hasil');

        Route::get('/materi/{topic}', [MateriController::class, 'show'])
            ->name('mahasiswa.materi');

        Route::get('/kuis/{topic}', [KuisController::class, 'show'])
            ->name('mahasiswa.kuis');

        Route::post('/kuis/{topic}', [KuisController::class, 'answer'])
            ->name('mahasiswa.kuis.answer');

        Route::get('/kuis/{topic}/hasil', [KuisController::class, 'hasil'])
            ->name('mahasiswa.kuis.hasil');

        Route::get('/review/{topic}', [ReviewController::class, 'show'])
            ->name('mahasiswa.review');

        Route::get('/posttest', [PostTestController::class, 'show'])
            ->name('mahasiswa.posttest');

        Route::post('/posttest', [PostTestController::class, 'answer'])
            ->name('mahasiswa.posttest.answer');

        Route::get('/posttest/hasil', [PostTestController::class, 'hasil'])
            ->name('mahasiswa.posttest.hasil');

        Route::get('/progres', [ProgresController::class, 'index'])
            ->name('mahasiswa.progres');

        Route::get('/profil', [ProfilController::class, 'index'])
            ->name('mahasiswa.profil');
    });

// ── Dosen Exports ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'role:dosen'])
    ->prefix('dosen/export')
    ->name('dosen.export.')
    ->group(function () {
        Route::get('mahasiswa/csv', [ExportController::class, 'csv'])->name('mahasiswa.csv');
        Route::get('mahasiswa/pdf', [ExportController::class, 'pdf'])->name('mahasiswa.pdf');
    });

// ── Redirect root → login ─────────────────────────────────────────────────────
Route::redirect('/', '/login');
