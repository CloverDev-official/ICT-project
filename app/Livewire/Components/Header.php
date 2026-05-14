<?php

namespace App\Livewire\Components;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return $this->redirectRoute('login', navigate: true);
    }


    public function render()
    {
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y, l');

        return view('livewire.components.header', [
            'dateNow' => $dateNow,
        ]);
    }
}
