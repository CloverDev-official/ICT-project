<?php

use Illuminate\Support\Facades\Route;

// dashboard
Route::get('/dashboard', Dashboard::class)->middleware('access:dashboard')->name('dashboard');

// pilih absen
Route::get('/pilih-absen', PilihAbsen::class)->middleware('access:pilih-absen')->name('pilih-absen');

// pengaturan
Route::get('/pengaturan', Pengaturan::class)->middleware('access:pengaturan')->name('pengaturan');