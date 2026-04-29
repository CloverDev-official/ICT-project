<?php

namespace App\Livewire\Murid;

use App\Helpers\ValidateMagic;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Component;

class Edit extends Component
{
    public function render()
    {
        return view("livewire.murid.edit-murid");
    }
}
