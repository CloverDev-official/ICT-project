<?php

use Illuminate\Support\Facades\Route;
use Modules\Laporan\Http\Controllers\Api\StudentAttendanceHistoryController;

Route::middleware(['web', 'auth:web', 'access:riwayat-murid'])
    ->prefix('riwayat-absensi-murid')
    ->name('api.riwayat-absensi-murid.')
    ->group(function () {
        Route::get('/summary', [StudentAttendanceHistoryController::class, 'summary'])
            ->name('summary');
        Route::get('/', [StudentAttendanceHistoryController::class, 'index'])
            ->name('index');
    });
