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

    //  scan qr code
    Route::get('/scan-qrcode', ScanQRCode::class)->middleware('access:scan-qrcode')->name('scan-qrcode');
    
    // dashboard
    Route::get('/dashboard', Dashboard::class)->middleware('access:dashboard')->name('dashboard');

    // pilih absen
    Route::get('/pilih-absen', PilihAbsen::class)->middleware('access:pilih-absen')->name('pilih-absen');

    // pengaturan
    Route::get('/pengaturan', Pengaturan::class)->middleware('access:pengaturan')->name('pengaturan');

    // profil
    Route::get('/profil', Profil::class)->middleware('access:profil')->name('profil');

    // absensi
    Route::prefix('/absensi')->group(function () {

        // absensi murid
        Route::prefix('/murid')->middleware('access:absensi-murid')->group(function () {
            Route::get('/', IndexAbsen::class)->name('absensi-murid');
            Route::get('/edit/{absenId}', EditAbsen::class)->name('edit-absen-murid');
        });
        // Route::get('/guru', AbsensiGuru::class)->middleware('access:absensi-guru')->name('absensi-guru');
    });
    
    // data  murid
    Route::prefix('/data-murid')->middleware('access:data-murid')->group(function () {
        Route::get('/', IndexMurid::class)->name('data-murid');
        Route::get('/create', CreateMurid::class)->name('tambah-murid');
        Route::get('/edit/{muriduuid}', EditMurid::class)->name('edit-murid');
        Route::get('/template', function () {
            return Excel::download(new MuridTemplateExport(), 'template-import-murid.xlsx');
        })->name('template-import-murid');
    });

    // data guru
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

    // manajemen group
    Route::prefix('/manajemen')->group( function () {
        // manajemen waktu
        Route::prefix('/waktu')->middleware('access:manajemen-waktu')->group( function () {
            Route::get('/', ManajemenWaktu::class)->name('manajemen-waktu');
            Route::get('/create', TambahEvent::class)->name('tambah-event');
        });

        // manajemen murid
        Route::prefix('/murid')->middleware('access:manajemen-murid')->group( function () {
            Route::get('/', ManajemenMurid::class)->name('manajemen-murid');
            Route::get('/create', TambahFoto::class)->name('tambah-foto');
            Route::get('/edit',  EditFoto::class)->name('edit-foto');
        });
        
        // generate
        Route::get('/generate-qr', GenerateQR::class)->middleware('access:generate-qr')->name('generate-QR');
        
        // role akses
        Route::get('/role', IndexRole::class)->middleware('access:manajemen-role')->name('manajemen-role');

        Route::middleware('access:manajemen-lainnya')->group( function () {

            // tahun ajaran
            Route::get('/tahun-ajaran', TahunAjaran::class)->name('manajemen-tahun-ajaran');

            // manajemen user group
            Route::prefix('/user')->group( function () {
                Route::get('/', IndexUser::class)->name('manajemen-user');
                Route::get('/create', CreateUser::class)->name('tambah-user');
                Route::get('/edit/{userId}', EditUser::class)->name('edit-user');
                
            });
        });
    });


});
