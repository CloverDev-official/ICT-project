<?php

namespace Modules\DataMaster\Livewire\DataMurid\Components\Modal;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Livewire\Component;

class Hapus extends Component
{
    public $murid;

    public function loadMurid($muridId)
    {
        $this->murid = Murid::findOrFail($muridId);
    }

    public function destroy()
    {
        if(!$this->murid) {
            ToastMagic::error('Gagal Menghapus Murid', 'Data murid tidak ditemukan');
            return;
        }

        $this->murid->delete();

        ToastMagic::success('Berhasil Menghapus Murid', "Murid {$this->murid->nama} berhasil dihapus");
        $this->dispatch('close-delete-modal');
        $this->dispatch('murid-refresh');
    }

    public function render()
    {
        return view('datamaster::livewire.data-murid.components.modal.hapus');
    }
}
