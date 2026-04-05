<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\auth\Login;

Route::get('/', action:Login::class)->name('login');