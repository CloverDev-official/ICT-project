<?php

namespace Modules\DataMaster\Livewire\DataGuru\Components\Modal;

use App\Helpers\ToastMagic;
use App\Models\Guru\Guru;
use Livewire\Component;

class Hapus extends Component
{
    public ?Guru $guru = null;

    public function loadGuru(int $id): void
    {
        $this->guru = Guru::findOrFail($id);
    }

    public function destroy(): void
    {
        if (!$this->guru) {
            return;
        }

        $nama = $this->guru->nama;
        $this->guru->delete();

        ToastMagic::success('Guru berhasil dihapus', "Data guru {$nama} berhasil dihapus.");

        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('datamaster::livewire.data-guru.components.modal.hapus');
    }
}
