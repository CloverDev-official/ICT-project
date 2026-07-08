<?php

namespace App\Livewire\Components\Modal\Murid;

use App\Models\Murid\Murid;
use Livewire\Component;

class ModalPilihQr extends Component
{
    public ?Murid $murid = null;

    public function loadMurid($muridId): void
    {
        $this->murid = Murid::query()->with(['rombel.jurusan'])->findOrFail($muridId);
    }

    public function downloadVertical(): void
    {
        if (!$this->murid) {
            return;
        }

        $this->dispatch('generateStudentCardSinglePdf',
            murid: $this->murid->load(['rombel.jurusan'])->toArray(),
            orientation: 'vertical',
            filename: null,
        );

        $this->dispatch('close-download-modal');
    }

    public function downloadHorizontal(): void
    {
        if (!$this->murid) {
            return;
        }

        $this->dispatch('generateStudentCardSinglePdf',
            murid: $this->murid->load(['rombel.jurusan'])->toArray(),
            orientation: 'horizontal',
            filename: null,
        );

        $this->dispatch('close-download-modal');
    }

    public function render()
    {
        return view('livewire.components.modal.murid.modal-pilih-qr');
    }
}
