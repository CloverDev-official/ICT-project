<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

Route::get('/manifest.webmanifest', function () {
    $settings = View::shared('siteSettings', []);

    return response()->json([
        'id' => './',
        'name' => $settings['nama_website'] ?? config('app.name'),
        'short_name' => $settings['nama_website'] ?? config('app.name'),
        'lang' => 'id',
        'start_url' => './',
        'scope' => './',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#ffffff',
        'icons' => [
            ['src' => 'assets/img/pwa/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => 'assets/img/pwa/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png'],
        ],
    ], 200, [
        'Content-Type' => 'application/manifest+json',
        'Cache-Control' => 'no-cache',
    ]);
})->name('pwa.manifest');

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login-page');
});

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
