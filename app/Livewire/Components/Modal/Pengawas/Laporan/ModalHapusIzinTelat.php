<?php

namespace App\Livewire\Components\Modal\Pengawas\Laporan;

use App\Helpers\ToastMagic;
use App\Models\Murid\AbsenMurid;
use Livewire\Component;

class ModalHapusIzinTelat extends Component
{
    public ?AbsenMurid $listMuridTerlambat = null;

    public function loadMuridTerlambat($id): void
    {
        $this->listMuridTerlambat = AbsenMurid::query()->where('status', 'Terlambat')->with('murid')->findOrFail($id);
    }

    public function destroy(): void
    {
        if (!$this->listMuridTerlambat) {
            ToastMagic::error('Data izin tidak ditemukan.');
            return;
        }

        $nama = $this->listMuridTerlambat->murid?->nama ?? '-';
        $this->listMuridTerlambat->delete();

        ToastMagic::success('Data izin berhasil dihapus.', "Data izin murid {$nama} berhasil dihapus");
        $this->dispatch('close-delete-modal');
        $this->dispatch('izin-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.pengawas.laporan.modal-hapus-izin-telat');
    }
}
