<?php

namespace Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar;

use App\Models\Murid\IzinMurid;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Surat extends Component
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
        return view('laporan::livewire.laporan.pengawas.izin-keluar.surat');
    }
}
