<?php

namespace App\Livewire\Murid;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Livewire\Component;

class Delete extends Component
{
    public string $muridUuid = '';
    public ?Murid $murid = null;

    public function loadMurid(string $uuid): void
    {
        $this->muridUuid = $uuid;

        $this->murid = Murid::query()->aktif()->where('uuid', $uuid)->firstOrFail();
    }

    public function destroy(): void
    {
        if (!$this->murid) return;

        $this->murid->delete();

        ToastMagic::success(
            "Murid berhasil dihapus",
            "Data murid berhasil {$this->murid->nama} dihapus"
        );

        $this->dispatch('close-modal');
        $this->dispatch('murid-refresh');   
    }
    
    public function render()
    {
        return view('livewire.components.modal.murid.modal-hapus-murid');
    }
}
