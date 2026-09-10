<?php

use Illuminate\Support\Facades\Route;

Route::match(['HEAD'], '/scan-qrcode/ping', static fn () => response()->noContent())
    ->middleware([
        'web',
        'auth',
        'access:scan-qrcode',
        'signed:relative',
        'throttle:scan-qrcode-ping',
    ])
    ->name('scan-qrcode.ping');
