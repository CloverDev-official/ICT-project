<?php

namespace App\Livewire\Murid;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Livewire\Component;

class Delete extends Component
{
    public string $muridUlid = '';
    public ?Murid $murid = null;

    public function loadMurid(string $ulid): void
    {
        $this->muridUlid = $ulid;

        $this->murid = Murid::where('ulid', $ulid)->firstOrFail();
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
        return view('livewire.components.modal.modal-hapus-murid');
    }
}
