<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class EditGuru extends Component
{   
    #[Layouts('layouts.app')]
    public function render()
    {
        return view('livewire.edit-guru');
    }
}
