<?php

namespace App\Livewire\Components\Modal\Pengawas\Laporan;

use App\Helpers\ToastMagic;
use App\Models\Murid\IzinMurid;
use Livewire\Component;

class ModalHapusIzin extends Component
{
    public ?IzinMurid $izin = null;

    public function loadIzin($id): void
    {
        $this->izin = IzinMurid::query()->with('murid')->findOrFail($id);
    }

    public function destroy(): void
    {
        if (!$this->izin) {
            ToastMagic::error('Data izin tidak ditemukan.');
            return;
        }

        $nama = $this->izin->murid?->nama ?? '-';
        $this->izin->delete();

        ToastMagic::success('Data izin berhasil dihapus.', "Data izin murid {$nama} berhasil dihapus");
        $this->dispatch('close-delete-modal');
        $this->dispatch('izin-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.pengawas.laporan.modal-hapus-izin');
    }
}
