<?php

namespace App\Livewire\Pengawas;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SuratIzinTelat extends Component
{   

    #[Layout("layouts.auth")]
    public function render()
    {
        return view('livewire.pengawas.surat-izin-telat');
    }
}
