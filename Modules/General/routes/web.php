<?php

use Illuminate\Support\Facades\Route;

use Modules\General\Livewire\Dashboard\Index as Dashboard;
use Modules\General\Livewire\PilihAbsen\Index as PilihAbsen;
use Modules\General\Livewire\Pengaturan\Index as Pengaturan;
use Modules\General\Livewire\RefreshAllBrowsers\Index as RefreshAllBrowsers;

// dashboard
Route::get('/dashboard', Dashboard::class)->middleware('access:dashboard')->name('dashboard');

// pilih absen
Route::get('/pilih-absen', PilihAbsen::class)->middleware('access:pilih-absen')->name('pilih-absen');

// pengaturan
Route::get('/pengaturan', Pengaturan::class)->middleware('access:pengaturan')->name('pengaturan');

// Hanya Super Admin yang dapat membuka halaman dan mengirim perintah refresh.
Route::get('/refresh-semua-browser', RefreshAllBrowsers::class)->middleware('super-admin')->name('refresh-all-browsers');
