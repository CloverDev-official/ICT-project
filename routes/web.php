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
use App\Livewire\EditGuru;
use App\Livewire\Murid\Rombel\Jurusan\Index as IndexJurusan;
use App\Livewire\Murid\Rombel\Jurusan\Create as CreateJurusan;
use App\Livewire\Murid\Rombel\Jurusan\Edit as EditJurusanRombel;
use App\Livewire\Murid\Rombel\Kelas\Index as IndexKelas;
use App\Livewire\Murid\Rombel\Kelas\Create as CreateKelas;
use App\Livewire\Murid\Rombel\Kelas\Edit as EditKelasRombel;
use App\Livewire\Manajemen\Waktu;
use App\Livewire\Manajemen\GenerateQR;
use App\Livewire\Manajemen\Waktu\ManajemenWaktu;
use App\Livewire\Manajemen\User\EditUser;
use App\Livewire\Manajemen\User\ManajemenUser;
use App\Livewire\Manajemen\User\TambahUser;
use App\Livewire\Manajemen\Waktu\TambahEvent;
use App\Livewire\PilihAbsen;
use App\Livewire\RekapAbsenGuru;
use App\Livewire\RekapAbsenMurid;
use App\Livewire\TambahGuru;

Route::get('/', Login::class)->name('login');
Route::get('/scan-qrcode', ScanQRCode::class)->name('scan-qrcode');


Route::prefix('admin')->group(function () {

    // dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    // pilih absen
    Route::get('/pilih-absen', PilihAbsen::class)->name('pilih-absen');
    
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
        route::get('/', IndexKelas::class)->name('data-kelas');
        route::get('/create', CreateKelas::class)->name('tambah-kelas');
        route::get('/edit/{rombelId}', EditKelasRombel::class)->name('edit-kelas');
    });

    route::prefix('/data-jurusan')->group( function () {
        route::get('/', IndexJurusan::class)->name('data-jurusan');
        route::get('/create', CreateJurusan::class)->name('tambah-jurusan');
        route::get('/edit/{jurusanId}', EditJurusanRombel::class)->name('edit-jurusan');
    });

    // manajemen group
    route::prefix('/manajemen')->group( function () {
        // manajemen waktu
        route::prefix('/manajemen-waktu')->group( function () {
            route::get('/', ManajemenWaktu::class)->name('manajemen-waktu');
            route::get('/create', TambahEvent::class)->name('tambah-event');
        
        });
        route::get('/generate-qr', GenerateQR::class)->name('generate-QR');
        // manajemen user group
        route::prefix('/manajemen-user')->group( function () {
            route::get('/', ManajemenUser::class)->name('manajemen-user');
            route::get('/create', TambahUser::class)->name('tambah-user');
            route::get('/edit', EditUser::class)->name('edit-user');
            
        });
    });


});
