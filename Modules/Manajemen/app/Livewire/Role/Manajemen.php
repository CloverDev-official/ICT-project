<?php

namespace Modules\Manajemen\Livewire\Role;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Manajemen extends Component
{
    public ?int $editingRoleId = null;

    public string $name = '';

    public array $permissions = [];

    public string $search = '';

    private array $permissionGroups = [
        'Umum' => [
            'dashboard',
            'pilih-absen',
            'profil',
        ],
        'Laporan' => [
            'laporan',
            'rekap-absen',
            'rekap-absen-murid',
            'rekap-absen-guru',
            'riwayat',
            'riwayat-murid',
        ],
        'Data Master' => [
            'data-master',
            'data-murid',
            'data-guru',
            'data-kelas',
            'data-jurusan',
        ],
        'Absensi' => [
            'absensi',
            'absensi-murid',
            'absensi-guru',
        ],
        'Manajemen' => [
            'manajemen',
            'generate-qr',
            'manajemen-waktu',
            'manajemen-murid',
            'manajemen-lainnya',
            'manajemen-role',
            'manajemen-tahun-ajaran',
        ],
        'Pengaturan' => [
            'pengaturan',
        ],
    ];

    public function getPermissionGroupsProperty(): array
    {
        return $this->permissionGroups;
    }

    public function getAvailablePermissionsProperty(): array
    {
        return collect($this->permissionGroups)
            ->flatten()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getRolesProperty()
    {
        return Role::query()
            ->withCount('users')
            ->when($this->search !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id')
            ->get();
    }

    public function editRole(int $roleId): void
    {
        $role = Role::query()->findOrFail($roleId);

        if ($role->id === 1) {
            ToastMagic::warning('Role super admin tidak bisa diubah dari halaman ini.');
            return;
        }

        $this->editingRoleId = $role->id;
        $this->name = $role->name;
        $this->permissions = $role->permissions ?? [];
    }

    public function resetForm(): void
    {
        $this->reset(['editingRoleId', 'name', 'permissions']);
    }

    public function save(): void
    {
        $validated = ValidateMagic::run(
            [
                'name' => ['required', 'string', 'max:255', Rule::unique('role', 'name')->ignore($this->editingRoleId)],
                'permissions' => ['array'],
                'permissions.*' => ['string'],
            ],
            [
                'name.required' => 'Nama role wajib diisi.',
                'name.string' => 'Nama role harus berupa teks.',
                'name.max' => 'Nama role tidak boleh lebih dari 255 karakter.',
                'name.unique' => 'Nama role sudah digunakan.',
            ]
        );

        if (! $validated) {
            return;
        }

        $payload = [
            'name' => $this->name,
            'permissions' => collect($this->permissions)
                ->intersect($this->availablePermissions)
                ->values()
                ->all(),
        ];

        if ($this->editingRoleId) {
            $role = Role::query()->findOrFail($this->editingRoleId);

            if ($role->id === 1) {
                ToastMagic::warning('Role super admin tidak bisa diubah dari halaman ini.');
                return;
            }

            $role->update($payload);
            ToastMagic::success('Role berhasil diperbarui.');
        } else {
            Role::create($payload);
            ToastMagic::success('Role berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    #[On('manajemen-role-refresh')]
    public function refreshTable(): void
    {
        unset($this->roles);
    }

    public function deleteRole(int $roleId): void
    {
        $role = Role::query()->withCount('users')->findOrFail($roleId);

        if ($role->id === 1) {
            ToastMagic::warning('Role super admin tidak bisa dihapus.');
            return;
        }

        if ($role->users_count > 0) {
            ToastMagic::warning('Role ini masih dipakai user, jadi belum bisa dihapus.');
            return;
        }

        $role->delete();

        if ($this->editingRoleId === $roleId) {
            $this->resetForm();
        }

        ToastMagic::success('Role berhasil dihapus.');
    }

    public function render()
    {
        return view('manajemen::livewire.role.manajemen', [
            'roles' => $this->roles,
            'permissionGroups' => $this->permissionGroups,
            'availablePermissions' => $this->availablePermissions,
        ]);
    }
}