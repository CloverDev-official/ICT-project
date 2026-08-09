<?php

use Illuminate\Support\Facades\Route;
use App\Exports\Murid\MuridTemplateExport;
use App\Exports\Guru\GuruTemplateExport;

use Modules\DataMaster\Livewire\DataMurid\Index as IndexMurid;
use Modules\DataMaster\Livewire\DataMurid\Create as CreateMurid;
use Modules\DataMaster\Livewire\DataMurid\Edit as EditMurid;

use Modules\DataMaster\Livewire\DataGuru\Index as IndexGuru;
use Modules\DataMaster\Livewire\DataGuru\Create as CreateGuru;
use Modules\DataMaster\Livewire\DataGuru\Edit as EditGuru;

use Modules\DataMaster\Livewire\DataKelas\Index as IndexKelas;
use Modules\DataMaster\Livewire\DataKelas\Create as CreateKelas;
use Modules\DataMaster\Livewire\DataKelas\Edit as EditKelasRombel;

use Modules\DataMaster\Livewire\DataJurusan\Index as IndexJurusan;
use Modules\DataMaster\Livewire\DataJurusan\Create as CreateJurusan;
use Modules\DataMaster\Livewire\DataJurusan\Edit as EditJurusanRombel;

Route::prefix('/data-murid')->middleware('access:data-murid')->group(function () {
    Route::get('/', IndexMurid::class)->name('data-murid');
    Route::get('/create', CreateMurid::class)->name('tambah-murid');
    Route::get('/edit/{muriduuid}', EditMurid::class)->name('edit-murid');
    Route::get('/template', function () {
        return Excel::download(new MuridTemplateExport(), 'template-import-murid.xlsx');
    })->name('template-import-murid');
});

Route::prefix('/data-guru')->middleware('access:data-guru')->group(function () {
    Route::get('/', IndexGuru::class)->name('data-guru');
    Route::get('/create', CreateGuru::class)->name('tambah-guru');
    Route::get('/edit/{id}', EditGuru::class)->name('edit-guru');
    Route::get('/template', function () {
        return Excel::download(new GuruTemplateExport(), 'template-import-guru.xlsx');
    })->name('template-import-guru');
});

Route::prefix('/data-kelas')->middleware('access:data-kelas')->group(function () {
    Route::get('/', IndexKelas::class)->name('data-kelas');
    Route::get('/create', CreateKelas::class)->name('tambah-kelas');
    Route::get('/edit/{rombelId}', EditKelasRombel::class)->name('edit-kelas');
});

Route::prefix('/data-jurusan')->middleware('access:data-jurusan')->group( function () {
    Route::get('/', IndexJurusan::class)->name('data-jurusan');
    Route::get('/create', CreateJurusan::class)->name('tambah-jurusan');
    Route::get('/edit/{jurusanId}', EditJurusanRombel::class)->name('edit-jurusan');
});
