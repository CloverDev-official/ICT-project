<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class TambahMurid extends Component
{   #[Layout('Layouts.app')]
    public function render()
    {
        return view('livewire.tambah-murid');
    }
}
