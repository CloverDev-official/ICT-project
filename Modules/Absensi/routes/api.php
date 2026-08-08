<?php

use Illuminate\Support\Facades\Route;
use Modules\Absensi\Http\Controllers\AbsensiController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('absensis', AbsensiController::class)->names('absensi');
});
