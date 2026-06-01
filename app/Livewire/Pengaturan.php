<?php

namespace App\Livewire;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Component;

class Pengaturan extends Component
{
    use WithFileUploads;

    public string $namaWebsite = '';

    public string $copyright = '';

    public $logo = null;

    public $loginImage = null;

    public string $logoPreview = '';

    public string $loginImagePreview = '';

    public ?string $logoPath = null;

    public ?string $loginImagePath = null;

    public function mount(): void
    {
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        $this->namaWebsite = (string) Setting::valueOf('nama_website', 'ICT Absensi');
        $this->copyright = (string) Setting::valueOf('copyright', 'SMKN 2 Banjarmasin © 2026');
        $this->logoPath = Setting::valueOf('logo', 'assets/img/logo_smkn_2.png');
        $this->loginImagePath = Setting::valueOf('login_image', 'assets/img/skenda-profil.jpeg');

        $this->logoPreview = Setting::resolveAssetUrl($this->logoPath, asset('assets/img/logo_smkn_2.png'));
        $this->loginImagePreview = Setting::resolveAssetUrl($this->loginImagePath, asset('assets/img/skenda-profil.jpeg'));
    }

    public function resetForm(): void
    {
        $this->logo = null;
        $this->loginImage = null;

        $this->loadSettings();
    }

    private function storeUploadedFile($file, string $directory, ?string $currentPath): ?string
    {
        if (!$file) {
            return $currentPath;
        }

        $newPath = $file->store($directory, 'public');

        if ($currentPath && !str_starts_with($currentPath, 'assets/')) {
            Storage::disk('public')->delete($currentPath);
        }

        return $newPath;
    }

    public function save(): void
    {
        $validated = ValidateMagic::run([
            'namaWebsite' => 'required|string|max:255',
            'copyright' => 'required|string|max:255',
            'logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:2048',
            'loginImage' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:4096',
        ], [
            'namaWebsite.required' => 'Nama website wajib diisi.',
            'namaWebsite.string' => 'Nama website harus berupa teks.',
            'namaWebsite.max' => 'Nama website tidak boleh lebih dari 255 karakter.',
            'copyright.required' => 'Copyright wajib diisi.',
            'copyright.string' => 'Copyright harus berupa teks.',
            'copyright.max' => 'Copyright tidak boleh lebih dari 255 karakter.',
            'logo.file' => 'Logo harus berupa file gambar.',
            'logo.mimes' => 'Logo harus berformat png, jpg, jpeg, webp, atau svg.',
            'loginImage.file' => 'Gambar login harus berupa file gambar.',
            'loginImage.mimes' => 'Gambar login harus berformat png, jpg, jpeg, atau webp.',
        ]);

        if (!$validated) {
            return;
        }

        $this->logoPath = $this->storeUploadedFile($this->logo, 'settings', $this->logoPath);
        $this->loginImagePath = $this->storeUploadedFile($this->loginImage, 'settings', $this->loginImagePath);

        Setting::upsertValue('nama_website', $this->namaWebsite);
        Setting::upsertValue('copyright', $this->copyright);
        Setting::upsertValue('logo', $this->logoPath ?? 'assets/img/logo_smkn_2.png');
        Setting::upsertValue('login_image', $this->loginImagePath ?? 'assets/img/skenda-profil.jpeg');

        $this->logoPreview = Setting::resolveAssetUrl($this->logoPath, asset('assets/img/logo_smkn_2.png'));
        $this->loginImagePreview = Setting::resolveAssetUrl($this->loginImagePath, asset('assets/img/skenda-profil.jpeg'));

        $this->reset(['logo', 'loginImage']);

        ToastMagic::success('Pengaturan tersimpan', 'Identitas website berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.pengaturan');
    }
}
