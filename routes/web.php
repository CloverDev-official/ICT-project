<?php
use App\Livewire\Murid\Index as IndexMurid;
use App\Livewire\Murid\Create as CreateMurid;
use App\Livewire\Murid\Edit as EditMurid;

use App\Livewire\Murid\Absen\Index as IndexAbsen;
use App\Livewire\Murid\Absen\Edit as EditAbsen;
use App\Livewire\Murid\Absen\ScanQRCode;

use App\Livewire\Pengawas\CetakIzin;
use App\Livewire\Pengawas\Laporan\LaporanIzin;
use App\Livewire\Murid\Rekap\Index as IndexRekapMurid;

use App\Livewire\Murid\Rombel\Jurusan\Index as IndexJurusan;
use App\Livewire\Murid\Rombel\Jurusan\Create as CreateJurusan;
use App\Livewire\Murid\Rombel\Jurusan\Edit as EditJurusanRombel;

use App\Livewire\Murid\Rombel\Kelas\Index as IndexKelas;
use App\Livewire\Murid\Rombel\Kelas\Create as CreateKelas;
use App\Livewire\Murid\Rombel\Kelas\Edit as EditKelasRombel;

use App\Livewire\Manajemen\User\Index as IndexUser;
use App\Livewire\Manajemen\User\Create as CreateUser;
use App\Livewire\Manajemen\User\Edit as EditUser;

use App\Livewire\Pengawas\SuratIzin;
use App\Livewire\Pengawas\SuratIzinTelat;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\AbsensiGuru;
use App\Livewire\Guru\Index as IndexGuru;
use App\Livewire\Guru\Create as CreateGuru;
use App\Livewire\Guru\Edit as EditGuru;

use App\Livewire\Manajemen\GenerateQR;
use App\Livewire\Manajemen\Role\Index as IndexRole;
use App\Livewire\Manajemen\TahunAjaran;
use App\Livewire\Manajemen\Waktu\ManajemenWaktu;
use App\Livewire\Manajemen\Waktu\TambahEvent;
use App\Livewire\Murid\Riwayat\DetailMurid;
use App\Livewire\Murid\Riwayat\RiwayatMurid;
use App\Livewire\PilihAbsen;
use App\Livewire\RekapAbsenGuru;
use App\Exports\Murid\MuridTemplateExport;
use App\Exports\Guru\GuruTemplateExport;
use App\Livewire\Manajemen\Murid\EditFoto;
use App\Livewire\Manajemen\Murid\ManajemenMurid;
use App\Livewire\Manajemen\Murid\TambahFoto;
use App\Livewire\Manajemen\Murid\TambahMurid;

use App\Livewire\Pengaturan;
use App\Livewire\Pengawas\Laporan\EditIzin;
use App\Livewire\Pengawas\Laporan\IzinTelat\CetakIzinTelat;
use App\Livewire\Profil;
use Maatwebsite\Excel\Facades\Excel;

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login-page');
});

Route::prefix('mpanel')->middleware('auth')->group(function () {

    Route::get('/card-template/{orientation}', function (string $orientation) {
        if (!in_array($orientation, ['horizontal', 'vertical'], true)) {
            abort(404);
        }
        
        $fileName = $orientation === 'vertical'
        ? 'card/kartu-pelajar-vertical.html'
        : 'card/kartu-pelajar-horizontal.html';

        return response(Storage::disk('local')->get($fileName), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
            })->name('card-template');
    
    // profil
    Route::get('/profil', Profil::class)->middleware('access:profil')->name('profil');
    
});
