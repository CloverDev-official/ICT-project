<?php

use Illuminate\Support\Facades\Route;

Route::match(['HEAD'], '/scan-qrcode/ping', static fn () => response()->noContent())
    ->middleware(['signed:relative', 'throttle:scan-qrcode-ping'])
    ->name('scan-qrcode.ping');
