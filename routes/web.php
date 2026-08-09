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
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]);
            })->name('card-template');    
});
