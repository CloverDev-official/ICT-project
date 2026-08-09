<?php

namespace Modules\Manajemen\Livewire\MuridFoto;

use App\Helpers\ToastMagic;
use App\Helpers\UploadFileNamer;
use App\Helpers\ValidateMagic;
use App\Models\Guru\Guru;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $listMurid;
    public ?int $murid_id = null;
    public ?string $selectedLabel = null;
    public ?string $selectedImagePath = null;
    public $image;

    public bool $isWaliKelas = false;
    public ?array $waliRombelIds = null;

    public function mount(): void
    {
        $this->applyWaliKelasLock();
        $this->listMurid = $this->getMuridList();
    }

    private function applyWaliKelasLock(): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $roleSlug = Str::slug($user->role?->name ?? '');
        $this->isWaliKelas = in_array($roleSlug, ['wali-kelas', 'wali-murid'], true);

        if (!$this->isWaliKelas) {
            return;
        }

        $guru = Guru::query()->where('user_id', $user->id)->first();
        if (!$guru) {
            $this->waliRombelIds = [];
            return;
        }

        $this->waliRombelIds = Rombel::query()
            ->where('wali_guru_id', $guru->id)
            ->pluck('id')
            ->all();
    }

    private function getMuridList()
    {
        return Murid::query()
            ->aktif()
            ->with(['rombel:id,tingkat_id,jurusan_id,indeks_id'])
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                fn ($q) => $q->whereHas('rombel', fn ($q) => $q->whereIn('id', $this->waliRombelIds))
            )
            ->orderBy('nama')
            ->get(['id', 'uuid', 'nama', 'nipd', 'nisn', 'image_path', 'rombel_id']);
    }

    public function updatedMuridId($value): void
    {
        if (!$value) {
            $this->selectedLabel = null;
            $this->selectedImagePath = null;
            return;
        }

        $murid = $this->listMurid?->firstWhere('id', (int) $value);
        if (!$murid) {
            $murid = Murid::query()->find($value);
        }

        if ($murid) {
            $this->selectedLabel = trim($murid->nama . ' - ' . $murid->nipd);
            $this->selectedImagePath = $murid->image_path ?: asset('assets/img/default-avatar.png');
        }
    }

    public function store(): void
    {
        $validated = ValidateMagic::run(
            [
                'murid_id' => ['required', 'exists:murid,id'],
                'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'murid_id.required' => 'Nama murid wajib dipilih.',
                'murid_id.exists' => 'Murid yang dipilih tidak valid.',
                'image.required' => 'Gambar wajib dipilih.',
                'image.image' => 'File harus berupa gambar.',
                'image.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
                'image.max' => 'Ukuran gambar maksimal 2MB.',
            ]
        );

        if (!$validated) {
            return;
        }

        $murid = Murid::query()->find($this->murid_id);
        if (!$murid) {
            ToastMagic::error('Murid tidak ditemukan.');
            return;
        }

        $murid->loadMissing(['rombel.tingkat', 'rombel.jurusan', 'rombel.indeks']);

        $path = $this->muridPhotoPath($murid);
        $path = $this->image->storeAs(dirname($path), basename($path), 'public');
        $imageUrl = Storage::url($path);

        $this->deleteOldImage($murid->image_path);

        $murid->update([
            'image_path' => $imageUrl,
        ]);

        $this->selectedImagePath = $imageUrl;
        $this->reset('image');

        ToastMagic::success('Foto Disimpan', 'Foto murid berhasil ditambahkan.');
    }

    private function muridPhotoPath(Murid $murid): string
    {
        return UploadFileNamer::makePath($this->image, $this->muridPhotoDirectory($murid), [
            $murid->nama,
            $murid->nisn ?: $murid->nipd ?: $murid->id,
        ]);
    }

    private function muridPhotoDirectory(Murid $murid): string
    {
        $kelas = $murid->rombel?->nama_lengkap ?: 'tanpa-kelas';

        return 'murid/' . Str::slug($kelas);
    }

    private function deleteOldImage(?string $path): void
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

    public function render()
    {
        return view('manajemen::livewire.murid-foto.create');
    }
}
