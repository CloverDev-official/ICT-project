<?php

use Illuminate\Support\Facades\Route;
use Modules\Profil\Livewire\Staff\Profil;

// profil
Route::get('/profil', Profil::class)->middleware('access:profil')->name('profil');