<?php

use App\Livewire\Murid\Index as IndexMurid;
use App\Livewire\Murid\Create as CreateMurid;
use App\Livewire\Murid\Edit as EditMurid;

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\AbsensiMurid;
use App\Livewire\AbsensiGuru;
use App\Livewire\DataGuru;
use App\Livewire\DataJurusan;
use App\Livewire\DataKelas;
use App\Livewire\RekapAbsenGuru;
use App\Livewire\RekapAbsenMurid;
use App\Livewire\TambahGuru;

Route::get('/', Login::class)->name('login');

Route::prefix('admin')->group(function () {

    // dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    
    // laporan
    route::prefix('/rekap')->group(function () {
        Route::get('/absen-murid', RekapAbsenMurid::class)->name('rekap-absen-murid');
        Route::get('/absen-guru', RekapAbsenGuru::class)->name('rekap-absen-guru');
    });

    
    // absensi
    route::prefix('/absensi')->group(function () {
        Route::get('/murid', AbsensiMurid::class)->name('absensi-murid');
        Route::get('/guru', AbsensiGuru::class)->name('absensi-guru');
    });
    
    // data  murid
    route::prefix('/data-murid')->group(function () {
        Route::get('/', IndexMurid::class)->name('data-murid');
        Route::get('/create', CreateMurid::class)->name('tambah-murid');
        Route::get('/edit/{muridUlid}', EditMurid::class)->name('edit-murid');
    });

    // data guru
    route::prefix('/data-guru')->group(function () {
        Route::get('/', DataGuru::class)->name('data-guru');
        Route::get('/create', TambahGuru::class)->name('tambah-guru');
        Route::get('/edit', EditMurid::class)->name('edit-guru');
    });

    Route::get('/data-kelas', DataKelas::class)->name(
        'data-kelas',
    );

    Route::get('/data-jurusan', DataJurusan::class)->name('data-jurusan');
});
