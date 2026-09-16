<?php

use App\Livewire\Auth\Login;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login-page');
});

// Endpoint ini hanya tersedia untuk sesi login dan hanya mengirim UUID pada header.
Route::match(['HEAD'], '/browser-refresh-version', function () {
    return response()
        ->noContent()
        ->header('X-Browser-Refresh-Version', Setting::valueOf('system.browser_refresh_version', ''))
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
})->middleware(['auth', 'throttle:browser-refresh-version'])->name('browser-refresh-version');

Route::get('/mpanel', function () {
    return to_route(request()->user()->defaultRouteName());
})->middleware('auth')->name('home');

Route::prefix('mpanel')->middleware('auth')->group(function () {

    Route::get('/card-template/{orientation}', function (string $orientation) {
        if (! in_array($orientation, ['horizontal', 'vertical'], true)) {
            abort(404);
        }

        $fileName = $orientation === 'vertical'
        ? 'card/kartu-pelajar-vertical.html'
        : 'card/kartu-pelajar-horizontal.html';

        return response(Storage::disk('local')->get($fileName), 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            // The card markup is static; caching avoids a request on every run.
            'Cache-Control' => 'private, max-age=86400, stale-while-revalidate=604800',
        ]);
    })->name('card-template');
});
