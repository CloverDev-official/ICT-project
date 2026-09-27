<?php

namespace Modules\Profil\Livewire\Staff;

use App\Helpers\ToastMagic;
use App\Helpers\UploadFileNamer;
use App\Helpers\ValidateMagic;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class Profil extends Component
{   
    use WithFileUploads;

    public ?User $user = null;

    public ?string $roleName = null;

    public ?string $profilePhotoPreview = null;

    public $photo = null;

    public ?string $current_password = null;

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $this->loadUser();
    }
    
    private function loadUser(): void
    {
        $this->user = auth()->user();

        if (!$this->user) {
            $this->roleName = null;
            $this->profilePhotoPreview = asset('assets/img/default-avatar.png');
            return;
        }

        $this->roleName = $this->user->assignedRoles()->pluck('name')->implode(', ');
        $this->profilePhotoPreview = $this->user->profile_photo_url;
    }

    public function updateProfilePhoto(): void
    {
        $validated = ValidateMagic::run([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.required' => 'Foto profil wajib diunggah.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if (!$validated) {
            return;
        }

        $user = $this->user ?? auth()->user();
        if (!$user) {
            ToastMagic::error('Pengguna tidak ditemukan.');
            return;
        }

        $path = $this->photo->storeAs(
            'profile-photos',
            basename(UploadFileNamer::makePath($this->photo, 'profile-photos', [
                $user->name,
                $user->email,
            ])),
            'public',
        );

        $this->deleteOldProfilePhoto($user->profile_photo_path ?? null);

        $user->forceFill([
            'profile_photo_path' => $path,
        ])->save();

        $this->user = $user->refresh();
        $this->profilePhotoPreview = $this->user->profile_photo_url;
        $this->reset('photo');

        ToastMagic::success('Foto Profil Disimpan', 'Foto profil berhasil diperbarui.');
    }

    private function deleteOldProfilePhoto(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        if (Str::startsWith($path, '/storage/')) {
            $path = ltrim(Str::replaceFirst('/storage/', '', $path), '/');
        }

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::replaceFirst('storage/', '', $path);
        }

        if ($path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    public function resetProfilePhoto(): void
    {
        $this->reset('photo');
        $this->profilePhotoPreview = $this->user?->profile_photo_url ?? asset('assets/img/default-avatar.png');
    }

    public function updatePassword(): void
    {
        $validated = ValidateMagic::run([
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!$validated) {
            return;
        }

        $user = $this->user ?? auth()->user();
        if (!$user) {
            ToastMagic::error('Pengguna tidak ditemukan.');
            return;
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $this->resetPasswordForm();
        ToastMagic::success('Password Disimpan', 'Password berhasil diperbarui.');
    }

    public function resetPasswordForm(): void
    {
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->resetErrorBag();
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return $this->redirectRoute('login', navigate: true);
    }

    public function render()
    {
        return view('profil::livewire.staff.profil');
    }
}
