<?php

use App\Livewire\Murid\Index as IndexMurid;
use App\Livewire\Murid\Create as CreateMurid;

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\AbsensiMurid;
use App\Livewire\AbsensiGuru;
use App\Livewire\DataGuru;
use App\Livewire\DataJurusan;
use App\Livewire\DataKelas;
use App\Livewire\EditGuru;
use App\Livewire\EditMurid;
use App\Livewire\TambahGuru;

Route::get('/', Login::class)->name('login');

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/absensi-murid', AbsensiMurid::class)->name('absensi-murid');
    Route::get('/absensi-guru', AbsensiGuru::class)->name('absensi-guru');

    route::prefix('/data-murid')->group(function () {
        Route::get('/', IndexMurid::class)->name('data-murid');
        Route::get('/create', CreateMurid::class)->name('tambah-murid');
        Route::get('/edit', EditMurid::class)->name('edit-murid');
    });

    route::prefix('/data-guru')->group(function () {
        Route::get('/', DataGuru::class)->name('data-guru');
        Route::get('/create', TambahGuru::class)->name('tambah-guru');
        Route::get('/edit', EditGuru::class)->name('edit-guru');
    });

    Route::get('/data-kelas', DataKelas::class)->name(
        'data-kelas',
    );

    Route::get('/data-jurusan', DataJurusan::class)->name('data-jurusan');
});
