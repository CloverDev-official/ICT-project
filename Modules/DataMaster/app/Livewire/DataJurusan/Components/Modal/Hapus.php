<?php

namespace Modules\DataMaster\Livewire\DataJurusan\Components\Modal;

use App\Helpers\ToastMagic;
use App\Models\Murid\Rombel\Jurusan;
use Livewire\Component;

class Hapus extends Component
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
        return view('datamaster::livewire.data-jurusan.components.modal.hapus');
    }
}
