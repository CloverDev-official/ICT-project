<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;

class Dashboard extends Component
{
    #[Layout('Layouts.app')]
    public function render()
    {   
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y');

        return view('livewire.dashboard', [
            'dateNow' => $dateNow,
        ]);
    }
}
