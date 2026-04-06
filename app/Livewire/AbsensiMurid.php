<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

class AbsensiMurid extends Component
{   
    #[Layout('Layouts.app')]
    public function render()
    {
        return view('livewire.absensi-murid');
    }
}
