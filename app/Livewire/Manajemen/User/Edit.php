<?php

namespace App\Livewire\Manajemen\User;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Admin;
use App\Models\Guru\Guru;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class Edit extends Component
{
    public User $user;
    public string $name = "";
    public string $email = "";
    public ?string $password = '';
    public ?string $password_confirmation = '';

    public ?int $roleId = null;
    public ?int $guruId = null;

    public function mount(int $userId)
    {
        $this->user = User::findOrFail($userId);
        $selectedGuru = Guru::query()->where('user_id', $this->user->id)->first();

        $this->fill([
            'name' => $this->user->name,
            'email' => $this->user->email,
            'roleId' => $this->user->role_id,
            'guruId' => $selectedGuru?->id,
        ]);
    }

    public function getRoles()
    {
        return Role::select('id', 'name')->distinct()->get();
    }

    public function getGuru()
    {
        return Guru::select('id', 'nama', 'user_id')
            ->whereNull('user_id')
            ->orWhere('user_id', $this->user->id)
            ->distinct()
            ->get();
    }

    public function update()
    {
        $validated = ValidateMagic::run([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'password' => 'nullable|string|min:8',
            'password_confirmation' => 'required_with:password|string|same:password',
            'roleId' => 'required|exists:role,id',
            'guruId' => 'nullable|exists:guru,id',
        ],
        [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email harus berupa alamat email yang valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'password_confirmation.required_with' => 'Konfirmasi password wajib diisi jika password diisi.',
            'password_confirmation.same' => 'Konfirmasi password harus sama dengan password.',
            'roleId.required' => 'Role wajib dipilih.',
            'roleId.exists' => 'Role yang dipilih tidak valid.',
            'guruId.exists' => 'Guru yang dipilih tidak valid.',
        ]);

        if (!$validated) {
            return;
        }

        $payload = [
            'name' => $this->name,
            'email' => $this->email,
            'role_id' => $this->roleId,
        ];

        if ($this->password) {
            $payload['password'] = $this->password;
        }

        $this->user->update($payload);

        $currentGuru = Guru::query()->where('user_id', $this->user->id)->first();

        if ($this->guruId) {
            if ($currentGuru && $currentGuru->id !== $this->guruId) {
                $currentGuru->update(['user_id' => null]);
            }

            Guru::find($this->guruId)?->update([
                'user_id' => $this->user->id,
            ]);

            Admin::query()->where('user_id', $this->user->id)->delete();
        } else {
            if ($currentGuru) {
                $currentGuru->update(['user_id' => null]);
            }

            Admin::query()->updateOrCreate(
                ['user_id' => $this->user->id],
                ['name' => $this->name, 'email' => $this->email],
            );
        }

        ToastMagic::success('Success', 'User berhasil diperbarui.');
    }


    public function render()
    {
        $selectedRole = Role::select('id', 'name')->find($this->roleId);
        $selectedGuru = Guru::select('id', 'nama')->find($this->guruId);

        return view('livewire.manajemen.user.edit-user', [
            'roles' => $this->getRoles(),
            'guru' => $this->getGuru(),
            'selectedRole' => $selectedRole,
            'selectedGuru' => $selectedGuru,
        ]);
    }
}
