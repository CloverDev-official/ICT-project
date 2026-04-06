<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\auth\Login;
use App\Livewire\Dashboard;

Route::get('/', action:Login::class)->name('login');

Route::prefix('admin')->group( function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
});