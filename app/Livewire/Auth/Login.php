<?php

namespace App\Livewire\Auth;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public $email, $password;

    public function login()
    {
        $validated = ValidateMagic::run(
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'password.required' => 'Password wajib diisi.',
            ]
        );

        if (!$validated) {
            return;
        }

        if(Auth::attempt($validated)) {
            ToastMagic::success('Login berhasil!', 'Selamat datang kembali.');
        } else {
            ToastMagic::error('Login gagal!', 'Pastikan email dan password benar.');
            return;
        }

        $routeName = auth()->user()?->defaultRouteName() ?? 'dashboard';

        $this->redirectRoute($routeName, navigate: true);
    }


    #[Layout("layouts.auth")]
    public function render()
    {
        return view("livewire.auth.login");
    }
}
