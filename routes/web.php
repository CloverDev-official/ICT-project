<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\AbsensiMurid;
use App\Livewire\AbsensiGuru;
use App\Livewire\DataGuru;
use App\Livewire\DataKelasJurusan;
use App\Livewire\DataMurid;
use App\Livewire\TambahMurid;

Route::get('/', Login::class)->name('login');

Route::prefix('admin')->group( function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/absensi-murid',  AbsensiMurid::class)->name('absensi-murid');
    Route::get('/absensi-guru',  AbsensiGuru::class)->name('absensi-guru');
    Route::get('/data-murid',  DataMurid::class)->name('data-murid');
    Route::get('/data-murid/create',  TambahMurid::class)->name('tambah-murid');
    Route::get('/data-guru',  DataGuru::class)->name('data-guru');
    Route::get('/data-kelas-jurusan',  DataKelasJurusan::class)->name('data-kelas-jurusan');
});