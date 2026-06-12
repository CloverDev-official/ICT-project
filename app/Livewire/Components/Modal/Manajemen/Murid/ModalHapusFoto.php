<?php

namespace App\Livewire\Components\Modal\Manajemen\Murid;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            $this->deleteImage($murid->image_path);

            $murid->image_path = null;
            $murid->save();
        }

        ToastMagic::success('Foto berhasil dihapus');
        $this->dispatch('close-delete-modal');
        $this->dispatch('manajemen-murid-refresh');
    }

    private function deleteImage(?string $path): void
    {
        if (!$path || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        if (Str::startsWith($path, '/storage/')) {
            $path = ltrim(Str::replaceFirst('/storage/', '', $path), '/');
        }

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::replaceFirst('storage/', '', $path);
        }

        if ($path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    public function render()
    {
        return view('livewire.components.modal.manajemen.murid.modal-hapus-foto');
    }
}
