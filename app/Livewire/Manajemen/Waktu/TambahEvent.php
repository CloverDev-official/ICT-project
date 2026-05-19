<?php

namespace App\Livewire\Manajemen\Waktu;

use Livewire\Component;

class TambahEvent extends Component
{
    public function mount()
    {
        return redirect()->route('manajemen-waktu');
    }

    public function render()
    {
        return view('livewire.manajemen.waktu.tambah-event');
    }
}
