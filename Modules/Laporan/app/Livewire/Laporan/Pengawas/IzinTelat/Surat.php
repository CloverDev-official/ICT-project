<?php

namespace Modules\Laporan\Livewire\Laporan\Pengawas\IzinTelat;

use App\Enums\AttendanceStatus;
use App\Models\Murid\AbsenMurid;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Surat extends Component
{
    #[Locked]
    public AbsenMurid $absen;

    #[Locked]
    public bool $siapCetak = false;

    public function mount($id): void
    {
        $this->absen = AbsenMurid::query()
            ->where('status', AttendanceStatus::Terlambat->value)
            ->with('murid.rombel')
            ->findOrFail($id);
        if (trim($this->absen->keterangan ?? '') === '') {
            $this->redirectRoute('cetak-izin-telat', navigate: true);

            return;
        }

        $this->siapCetak = true;
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('laporan::livewire.laporan.pengawas.izin-telat.surat');
    }
}
