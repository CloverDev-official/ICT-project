<?php

namespace Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat;
use App\Models\Murid\AbsenMurid;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Surat extends Component
{   
    public $absen;

    public function mount($id)
    {
        $this->absen = AbsenMurid::find($id);
    }

    #[Layout("layouts.auth")]
    public function render()
    {
        return view('laporan::livewire.laporan.pengawas.izin-telat.surat');
    }
}
