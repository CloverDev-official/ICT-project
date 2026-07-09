<?php

namespace App\Livewire\Pengawas;

use App\Models\Murid\IzinMurid;
use Livewire\Attributes\Layout;
use Livewire\Component;

class SuratIzin extends Component
{  
    public $izin;
    public $qrCode;

    public function mount($id)
    {
        $this->izin = IzinMurid::find($id);
        
        $muriduuid = $this->izin->murid->uuid;
        $this->qrCode = $muriduuid . '>' . $this->izin->uuid;
    }

    #[Layout("layouts.auth")]
    public function render()
    {
        return view('livewire.pengawas.surat-izin');
    }
}
