<?php

use Illuminate\Support\Facades\Route;
use Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar\Laporan as LaporanIzin;
use Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar\Edit as EditIzin;
use Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar\Cetak as CetakIzin;
use Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar\Surat as SuratIzin;

use Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat\Cetak as CetakIzinTelat;

use Modules\Laporan\Livewire\Laporan\RekapAbsen\Murid\Rekap as RekapMurid;

use Modules\Laporan\Livewire\Laporan\RiwayatAbsen\Murid\Riwayat as RiwayatMurid;
use Modules\Laporan\Livewire\Laporan\RiwayatAbsen\Murid\Detail as DetailMurid;

// laporan
Route::prefix('/laporan-pengawas')->middleware('access:laporan-pengawas')->group(function () {
    // izin keluar
    Route::prefix('/izin-keluar')->middleware('access:izin-keluar')->group(function () {
        Route::get('/izin', LaporanIzin::class)->name('laporan-izin-keluar');
        Route::get('/edit-izin/{id}', EditIzin::class)->name('edit-izin-keluar');
        Route::get('/cetak-izin', CetakIzin::class)->name('cetak-izin-keluar');
        Route::get('/surat-izin/{id}', SuratIzin::class)->name('surat-izin-keluar');
    });
    // izin telat
    Route::prefix('/izin-telat')->middleware('access:izin-telat')->group(function (){
        Route::get('/cetak-izin', CetakIzinTelat::class)->name('cetak-izin-telat');
    });
});

Route::prefix('/rekap')->group(function () {
    Route::get('/absen-murid', RekapMurid::class)->middleware('access:rekap-absen-murid')->name('rekap-absen-murid');
    // Route::get('/absen-guru', RekapAbsenGuru::class)->middleware('access:rekap-absen-guru')->name('rekap-absen-guru');
});

Route::prefix('/riwayat')->group(function () {
    Route::middleware('access:riwayat-murid')->group(function () {
        Route::get('/absen-murid', RiwayatMurid::class)->name('riwayat-absen-murid');
        Route::get('/detail-absen-murid/{muriduuid}', DetailMurid::class)->name('riwayat-detail-absen-murid');
    });
});