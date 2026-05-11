<?php

namespace App\Livewire\Murid\Rombel\Kelas;

use App\Helpers\ToastMagic;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Component;

class Delete extends Component
{
    public int $rombelId = 0;
    public ?Rombel $rombel = null;

    public function loadRombel(int $id): void
    {
        $this->rombelId = $id;
        $this->rombel = Rombel::query()
            ->with(['tingkat', 'jurusan', 'indeks'])
            ->findOrFail($id);
    }

    public function destroy(): void
    {
        if (!$this->rombel) {
            return;
        }

        $nama = $this->rombel->nama_lengkap;
        $this->rombel->delete();

        ToastMagic::success(
            'Kelas berhasil dihapus',
            "Data kelas {$nama} berhasil dihapus"
        );

        $this->dispatch('close-modal');
        $this->dispatch('kelas-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.kelas.modal-hapus-kelas');
    }
}
