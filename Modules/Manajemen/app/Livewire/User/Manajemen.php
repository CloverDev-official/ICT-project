<?php

namespace Modules\Manajemen\Livewire\User;

use App\Models\Role;
use App\Models\User;
use App\Helpers\ToastMagic;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Manajemen extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 20;

    public $roleId = null;


    public function filterRole($query)
    {
        if ($this->roleId) {
            $query->whereHas('roles', fn ($roles) => $roles->whereKey($this->roleId));
        }

        return $query;
    }

    public function getUsers()
    {
        $query = User::with(['role', 'roles'])
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });

        $query = $this->filterRole($query);

        return $query->fastPaginate($this->perPage);
    }

    public function getRoles()
    {
        return Role::select('id', 'name')->distinct()->get();
    }

    public function updatedRoleId()
    {
        $this->resetPage();
    }

    #[On('user-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::query()->with(['role', 'roles'])->findOrFail($userId);

        if ($user->isSuperAdmin()) {
            ToastMagic::warning('User super-admin tidak bisa dinonaktifkan.');
            return;
        }

        $user->update([
            'is_active' => ! $user->is_active,
        ]);

        ToastMagic::success('Status user berhasil diperbarui.');
    }

    public function render()
    {
        return view('manajemen::livewire.user.manajemen', [
            'users' => $this->getUsers(),
            'roles' => $this->getRoles(),
        ]);
    }
}
