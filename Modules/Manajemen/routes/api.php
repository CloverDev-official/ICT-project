<?php

use Illuminate\Support\Facades\Route;
use Modules\Manajemen\Http\Controllers\ManajemenController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('manajemens', ManajemenController::class)->names('manajemen');
});
