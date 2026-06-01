<?php

namespace App\Livewire\Components\Modal\Manajemen\TahunAjaran;

use Livewire\Component;

class ModalVerifikasiTahunAjaran extends Component
{
    public function lanjutkan(): void
    {
        $this->dispatch('proses-kenaikan-kelas');
    }

    public function render()
    {
        return view('livewire.components.modal.manajemen.tahun-ajaran.modal-verifikasi-tahun-ajaran');
    }
}
