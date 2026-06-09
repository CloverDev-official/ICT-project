<?php

namespace App\Livewire\Components\Modal\Manajemen\User;

use App\Helpers\ToastMagic;
use App\Models\User;
use Livewire\Component;

class ModalHapusUser extends Component
{
    public $user;

    public function loadUser($userId)
    {
        $this->user = User::find($userId);
    }

    public function destroy()
    {
        if (!$this->user) {
            ToastMagic::error('Data User tidak ditemukan!');
            return;
        }

        $this->user->delete();
        ToastMagic::success('Data User berhasil dihapus!');
        $this->dispatch('close-delete-modal');
        $this->dispatch('user-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.manajemen.user.modal-hapus-user');
    }
}
