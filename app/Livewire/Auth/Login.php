<?php

namespace App\Livewire\Auth;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

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

        if (! $validated) {
            return;
        }

        $validated['email'] = Str::lower($validated['email']);
        $throttleKey = $this->throttleKey($validated['email']);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            ToastMagic::warning(
                'Terlalu banyak percobaan login.',
                "Silakan coba lagi dalam {$seconds} detik."
            );

            return;
        }

        if (! Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ], $this->remember)) {
            RateLimiter::hit($throttleKey, 60);
            ToastMagic::error('Login gagal!', 'Pastikan email dan password benar.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        ToastMagic::success('Login berhasil!', 'Selamat datang kembali.');

        $routeName = auth()->user()?->defaultRouteName() ?? 'dashboard';

        $this->redirectRoute($routeName, navigate: true);
    }

    private function throttleKey(string $email): string
    {
        return Str::transliterate($email.'|'.request()->ip());
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
