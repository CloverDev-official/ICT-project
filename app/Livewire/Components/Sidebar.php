<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Carbon\Carbon;

class Sidebar extends Component
{
    public function render()
    {   
        Carbon::setLocale('id');
        $yearNow = Carbon::now()->translatedFormat('Y');
        return view('livewire.components.sidebar', [
            'yearNow' => $yearNow,
        ]);
    }
}
