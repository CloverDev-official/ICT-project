<?php

namespace App\Livewire\Murid\Rombel\Jurusan;

use App\Helpers\ToastMagic;
use App\Models\Murid\Rombel\Jurusan;
use Livewire\Component;

class Delete extends Component
{
    public int $jurusanId = 0;
    public ?Jurusan $jurusan = null;

    public function loadJurusan(int $id): void
    {
        $this->jurusanId = $id;
        $this->jurusan = Jurusan::query()->findOrFail($id);
    }

    public function destroy(): void
    {
        if (!$this->jurusan) {
            return;
        }

        $nama = $this->jurusan->nama;
        $this->jurusan->delete();

        ToastMagic::success(
            'Jurusan berhasil dihapus',
            "Data jurusan {$nama} berhasil dihapus"
        );

        $this->dispatch('close-modal');
        $this->dispatch('jurusan-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.jurusan.modal-hapus-jurusan');
    }
}
