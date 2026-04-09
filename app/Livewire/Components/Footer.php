<?php

namespace App\Livewire\Components;
use Carbon\Carbon;
use Livewire\Component;

class Footer extends Component
{   
    public function render()
    {
        Carbon::setLocale('id');
        $yearNow = Carbon::now()->translatedFormat('Y');

        return view('livewire.components.footer', [
            'yearNow' => $yearNow,
        ]);
    }
}
