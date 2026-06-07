<?php

namespace App\Livewire\Components\Modal\Manajemen\Murid;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Livewire\Component;

class ModalHapusFoto extends Component
{
    public $muridId;
    public $muridName;
    public $muridImagePath;

    public function mount($muridId, $muridName, $muridImagePath = null)
    {
        $this->muridId = $muridId;
        $this->muridName = $muridName;
        $this->muridImagePath = $muridImagePath ?? null;
    }

    public function destroy()
    {
        $murid = Murid::where('id', $this->muridId)->first();

        if ($murid && $murid->image_path) {
            // Hapus file foto dari storage
            \Storage::delete($murid->image_path);

            // Update kolom foto menjadi null
            $murid->image_path = null;
            $murid->save();
        }

        ToastMagic::success('Foto berhasil dihapus');
        $this->dispatch('close-delete-modal');
        $this->dispatch('manajemen-murid-refresh');
    }

    

    public function render()
    {
        return view('livewire.components.modal.manajemen.murid.modal-hapus-foto');
    }
}
