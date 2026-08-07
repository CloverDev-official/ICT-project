<?php

namespace Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Surat extends Component
{   

    #[Layout("layouts.auth")]
    public function render()
    {
        return view('laporan::livewire.laporan.pengawas.izin-telat.surat');
    }
}
