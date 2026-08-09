<?php

namespace Modules\Manajemen\Livewire\Role\Components\Modal;

use App\Helpers\ToastMagic;
use App\Models\Role;
use Livewire\Component;

class Hapus extends Component
{   
    public $role;

    public function loadRole($roleId)
    {       
        $this->role = Role::findOrFail($roleId);
    }

    public function destroy()
    {
        if (!$this->role) {
            ToastMagic::warning('Role tidak ditemukan');
            return;
        }

        $this->role->delete();

        ToastMagic::success('Role berhasil dihapus');
        $this->dispatch('close-delete-modal');
        $this->dispatch('manajemen-role-refresh');
    }

    public function render()
    {
        return view('manajemen::livewire.role.components.modal.hapus');
    }
}
