<?php

namespace App\Livewire\Components\Modal\Murid;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Livewire\Component;

class ModalHapusMurid extends Component
{
    public $muridId;
    public $muridName;

    public function mount($muridId, $muridName)
    {
        $this->muridId = $muridId;
        $this->muridName = $muridName;
    }

    public function destroy()
    {
        Murid::where('id', $this->muridId)->first()->delete();

        ToastMagic::success('Berhasil Menghapus Murid', "Murid $this->muridName berhasil dihapus");
        $this->dispatch('close-delete-modal');
        $this->dispatch('murid-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.murid.modal-hapus-murid');
    }
}
