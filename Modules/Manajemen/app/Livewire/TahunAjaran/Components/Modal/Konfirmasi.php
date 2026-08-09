<?php

namespace Modules\Manajemen\Livewire\TahunAjaran\Components\Modal;

use Livewire\Component;

class Konfirmasi extends Component
{
    public function lanjutkan(): void
    {
        $this->dispatch('proses-kenaikan-kelas');
    }

    public function render()
    {
        return view('manajemen::livewire.tahun-ajaran.components.modal.konfirmasi');
    }
}
