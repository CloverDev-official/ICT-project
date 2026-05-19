<?php

namespace App\Livewire\Manajemen\User;

use App\Models\Role;
use App\Models\User;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 20;

    public $roleId = null;

    public function mount()
    {
        Debugbar::info('Mounting User Index Component');
    }

    public function filterRole($query)
    {
        if ($this->roleId) {
            $query->where('role_id', $this->roleId);
        }

        return $query;
    }

    public function getUsers()
    {
        $query = User::with('role')
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

    public function render()
    {
        return view('livewire.manajemen.user.manajemen-user', [
            'users' => $this->getUsers(),
            'roles' => $this->getRoles(),
        ]);
    }
}
