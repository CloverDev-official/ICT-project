<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use App\Models\Guru\Guru;   

class DataGuru extends Component
{   
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    #[Computed]
    public function gurus()
    {
        return Guru::paginate(1);
    }

    public function render()
    {   
        return view('livewire.guru.data-guru');
    }
}
