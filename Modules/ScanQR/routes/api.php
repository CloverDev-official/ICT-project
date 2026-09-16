<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Route;

Route::match(['HEAD'], '/scan-qrcode/ping', static fn () => response()
    ->noContent()
    ->header('Cache-Control', 'no-store, private')
    ->header('X-Browser-Refresh-Version', Setting::valueOf('system.browser_refresh_version', '')))
    ->middleware([
        'web',
        'auth',
        'access:scan-qrcode',
        'signed:relative',
        'throttle:scan-qrcode-ping',
    ])
    ->name('scan-qrcode.ping');
