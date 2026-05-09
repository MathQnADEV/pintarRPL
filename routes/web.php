<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
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
