<?php

use Illuminate\Support\Facades\Route;

use Modules\Absensi\Livewire\Absen\Murid\Index as IndexAbsen;
use Modules\Absensi\Livewire\Absen\Murid\Edit as EditAbsen;

Route::prefix('/absensi')->group(function () { 
    Route::prefix('/murid')->middleware('access:absensi-murid')->group(function () {
        Route::get('/', IndexAbsen::class)->name('absensi-murid');
        Route::get('/edit/{absenId}', EditAbsen::class)->name('edit-absen-murid');
    });
    // Route::get('/guru', AbsensiGuru::class)->middleware('access:absensi-guru')->name('absensi-guru');
});