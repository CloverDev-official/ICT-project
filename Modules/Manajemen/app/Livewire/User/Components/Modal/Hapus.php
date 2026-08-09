<?php

namespace Modules\Manajemen\Livewire\User\Components\Modal;

use App\Helpers\ToastMagic;
use App\Models\User;
use Livewire\Component;

class Hapus extends Component
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
        return view('manajemen::livewire.user.components.modal.hapus');
    }
}
