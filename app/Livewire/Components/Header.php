<?php

namespace App\Livewire\Components;

use Carbon\Carbon;
use Livewire\Component;

class Header extends Component
{
    public function render()
    {
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y, l');

        return view('livewire.components.header', [
            'dateNow' => $dateNow,
        ]);
    }
}
