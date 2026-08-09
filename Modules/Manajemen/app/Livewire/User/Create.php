<?php

namespace Modules\Manajemen\Livewire\User;

use App\Actions\Fortify\CreateNewUser;
use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Admin;
use App\Models\Guru\Guru;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $roleId = null;
    public $guruId = null;

    public function getRoles()
    {
        return Role::select('id', 'name')->distinct()->get();
    }

    public function getGuru()
    {
        return Guru::select('id', 'nama')->whereNull('user_id')->distinct()->get();
    }

    public function create()
    {
        $validated = ValidateMagic::run([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
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
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
            'roleId.required' => 'Role wajib dipilih.',
            'roleId.exists' => 'Role yang dipilih tidak valid.',
            'guruId.exists' => 'Guru yang dipilih tidak valid.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'password_confirmation.same' => 'Konfirmasi password harus sama dengan password.',
        ]);

        if(!$validated) return;

        $user = app(CreateNewUser::class)->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'role_id' => $this->roleId,
            'is_active' => true,
        ]);

        if($this->guruId) {
            Guru::find($this->guruId)->update([
                'user_id' => $user->id,
            ]);
        } else {
            Admin::create([
                'name' => $this->name,
                'email' => $this->email,
                'user_id' => $user->id,
            ]);
        }

        ToastMagic::success('Success', 'User berhasil dibuat.');

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'roleId', 'guruId']);
    }


    public function render()
    {
        return view('manajemen::livewire.user.create', [
            'roles' => $this->getRoles(),
            'guru' => $this->getGuru(),
        ]);
    }
}
