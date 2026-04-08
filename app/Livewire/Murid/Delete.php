<?php

namespace App\Livewire\Murid;

use App\Helpers\ValidateMagic;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel;
use Livewire\Component;

class Delete extends Component
{
    public Murid $murid;

    public function mount(int $id): void
    {
        $this->murid = Murid::findOrFail($id);
    }

    public function destroy(): void
    {
        $this->murid->delete();
    }
    
    public function render()
    {
        return view("livewire.tambah-murid");
    }
}
