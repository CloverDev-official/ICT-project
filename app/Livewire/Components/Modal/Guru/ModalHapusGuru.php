<?php

namespace App\Livewire\Components\Modal\Guru;

use App\Helpers\ToastMagic;
use App\Models\Guru\Guru;
use Livewire\Component;

class ModalHapusGuru extends Component
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
        return view('livewire.components.modal.guru.modal-hapus-guru');
    }
}
