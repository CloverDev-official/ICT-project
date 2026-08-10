<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Auth\Login;
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
            // The card markup is static; caching avoids a request on every run.
            'Cache-Control' => 'private, max-age=86400, stale-while-revalidate=604800',
            ]);
            })->name('card-template');    
});
