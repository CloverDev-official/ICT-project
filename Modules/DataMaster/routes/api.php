<?php

use Illuminate\Support\Facades\Route;
use Modules\DataMaster\Http\Controllers\DataMasterController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('datamasters', DataMasterController::class)->names('datamaster');
});
