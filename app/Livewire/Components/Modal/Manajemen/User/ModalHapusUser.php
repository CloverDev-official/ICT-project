<?php

namespace App\Livewire\Components\Modal\Manajemen\User;

use App\Helpers\ToastMagic;
use App\Models\User;
use Livewire\Component;

class ModalHapusUser extends Component
{
    public $userId;
    public $userName;

    public function mount($userId, $userName)
    {
        $this->userId = $userId;
        $this->userName = $userName;
    }

    public function destroy()
    {
        User::where('id', $this->userId)->delete();
        ToastMagic::success('Data User berhasil dihapus!');
        $this->dispatch('close-delete-modal');
        $this->dispatch('user-refresh');
    }

    public function render()
    {
        return view('livewire.components.modal.manajemen.user.modal-hapus-user');
    }
}
