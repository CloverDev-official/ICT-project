<?php

namespace App\Livewire\Components\Modal\Manajemen\Murid;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Livewire\Component;

class ModalHapusFoto extends Component
{
    public $murid;

    public function loadMuridFoto($muridId)
    {
        $this->murid = Murid::findOrFail($muridId);
    }

    public function destroy()
    {
        $murid = $this->murid;

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
