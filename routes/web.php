<?php

use App\Helpers\downloadFile;
use App\Helpers\generateQRCode;

use App\Livewire\Murid\Index as IndexMurid;
use App\Livewire\Murid\Create as CreateMurid;
use App\Livewire\Murid\Edit as EditMurid;

use App\Livewire\Murid\Absen\Index as IndexAbsen;
use App\Livewire\Murid\Absen\ScanQRCode;

use App\Livewire\Murid\Rekap\Index as IndexRekapMurid;



use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\AbsensiMurid;
use App\Livewire\AbsensiGuru;
use App\Livewire\DataGuru;
use App\Livewire\DataJurusan;
use App\Livewire\DataKelas;
use App\Livewire\EditGuru;
use App\Livewire\EditJurusan;
use App\Livewire\EditKelas;
use App\Livewire\Manajemen\Waktu;
use App\Livewire\Manajemen\GenerateQR;
use App\Livewire\Manajemen\ManajemenUser;
use App\Livewire\TambahKelas;
use App\Livewire\RekapAbsenGuru;
use App\Livewire\RekapAbsenMurid;
use App\Livewire\TambahGuru;
use App\Livewire\TambahJurusan;

Route::get('/', Login::class)->name('login');
Route::get('/scan-qrcode', ScanQRCode::class)->name('scan-qrcode');


Route::prefix('admin')->group(function () {

    // dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // laporan
    route::prefix('/rekap')->group(function () {
        Route::get('/absen-murid', IndexRekapMurid::class)->name('rekap-absen-murid');
        Route::get('/absen-guru', RekapAbsenGuru::class)->name('rekap-absen-guru');
    });

    
    // absensi
    route::prefix('/absensi')->group(function () {
        Route::get('/murid', IndexAbsen::class)->name('absensi-murid');
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
        Route::get('/edit', EditGuru::class)->name('edit-guru');
    });

    route::prefix('/data-kelas')->group(function () {
        route::get('/',  DataKelas::class)->name('data-kelas');
        route::get('/create',  TambahKelas::class)->name('tambah-kelas');
        route::get('/edit',  EditKelas::class)->name('edit-kelas');
    });

    route::prefix('/data-jurusan')->group( function () {
        route::get('/', DataJurusan::class)->name('data-jurusan');
        route::get('/create', TambahJurusan::class)->name('tambah-jurusan');
        route::get('/edit', EditJurusan::class)->name('edit-jurusan');
    });

    // manajemen group
    route::prefix('/manajemen')->group( function () {
        route::get('/waktu', Waktu::class)->name('waktu');
        route::get('/generate-qr', GenerateQR::class)->name('generate-QR');
        route::get('/manajemen-user', ManajemenUser::class)->name('manajemen-user');
    });


});
