<?php

namespace Modules\DataMaster\Livewire\DataKelas;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Edit extends Component
{
    public Rombel $rombel;

    public int|string|null $tingkat_id = null;

    public ?int $jurusan_id = null;

    public int|string|null $indeks_id = null;

    public ?int $tahun_masuk = null;

    public ?int $guru_id = null;

    public int $tingkatAdditionalOptions = 0;

    public bool $hasMoreTingkat = false;

    public int $indeksAdditionalOptions = 0;

    public bool $hasMoreIndeks = false;

    public $listTingkat = [];

    public $listJurusan = [];

    public $listIndeks = [];

    public $listGuru = [];

    public function mount(int $rombelId): void
    {
        $this->rombel = Rombel::query()
            ->with(['tingkat', 'jurusan', 'indeks', 'waliGuru'])
            ->findOrFail($rombelId);

        $this->refreshTingkatOptions();
        $this->listJurusan = Jurusan::query()->orderBy('nama')->get(['id', 'nama']);
        $this->refreshIndeksOptions();
        $this->listGuru = Guru::query()->orderBy('nama')->get(['id', 'nama', 'email', 'user_id']);

        $this->fill([
            'tingkat_id' => $this->rombel->tingkat_id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'indeks_id' => $this->rombel->indeks_id ?? null,
            'tahun_masuk' => $this->rombel->tahun_masuk,
            'guru_id' => $this->rombel->wali_guru_id,
        ]);
    }

    public function loadMoreTingkat(): void
    {
        $this->tingkatAdditionalOptions++;

        $this->refreshTingkatOptions();
    }

    private function refreshTingkatOptions(): void
    {
        $this->listTingkat = Tingkat::options($this->tingkatAdditionalOptions);
        $this->hasMoreTingkat = Tingkat::hasMoreOptions($this->tingkatAdditionalOptions);
    }

    public function loadMoreIndeks(): void
    {
        $this->indeksAdditionalOptions += 3;

        $this->refreshIndeksOptions();
    }

    private function refreshIndeksOptions(): void
    {
        $this->listIndeks = Indeks::options($this->indeksAdditionalOptions);
        $this->hasMoreIndeks = Indeks::hasMoreOptions($this->indeksAdditionalOptions);
    }

    public function clearIndeks(): void
    {
        $this->indeks_id = null;
    }

    public function update(): void
    {
        $tingkatSelection = $this->tingkat_id;
        $indeksSelection = $this->indeks_id;
        $tingkat = Tingkat::resolveSelection($tingkatSelection);
        $indeks = Indeks::resolveSelection($indeksSelection);

        if ($tingkatSelection !== null && $tingkatSelection !== '' && ! $tingkat) {
            ToastMagic::error('Tingkat yang dipilih tidak valid.');

            return;
        }

        if ($indeksSelection !== null && $indeksSelection !== '' && ! $indeks) {
            ToastMagic::error('Indeks yang dipilih tidak valid.');

            return;
        }

        $this->tingkat_id = $tingkat?->id;
        $this->indeks_id = $indeks?->id;

        $validate = ValidateMagic::run(
            [
                'tingkat_id' => ['required', 'exists:tingkat,id'],
                'jurusan_id' => ['required', 'exists:jurusan,id'],
                'indeks_id' => ['nullable', 'exists:indeks,id'],
                'tahun_masuk' => ['required', 'integer', 'min:1900'],
                'guru_id' => ['nullable', 'exists:guru,id'],
            ],
            [
                'tingkat_id.required' => 'Tingkat wajib dipilih.',
                'tingkat_id.exists' => 'Tingkat yang dipilih tidak valid.',
                'jurusan_id.required' => 'Jurusan wajib dipilih.',
                'indeks_id.exists' => 'Indeks yang dipilih tidak valid.',
                'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
                'tahun_masuk.required' => 'Tahun masuk wajib dipilih.',
                'tahun_masuk.integer' => 'Tahun masuk tidak valid.',
                'guru_id.exists' => 'Wali kelas yang dipilih tidak valid.',
            ],
        );

        if (! $validate) {
            return;
        }

        if ($this->guru_id && ! $this->ensureWaliKelasUser($this->guru_id)) {
            return;
        }

        $exists = Rombel::query()
            ->where('tingkat_id', $this->tingkat_id)
            ->where('jurusan_id', $this->jurusan_id)
            ->where('indeks_id', $indeks?->id)
            ->whereKeyNot($this->rombel->id)
            ->exists();

        if ($exists) {
            ToastMagic::error('Kelas tersebut sudah terdaftar.');

            return;
        }

        $this->rombel->update([
            'tahun_masuk' => $this->tahun_masuk,
            'tingkat_id' => $this->tingkat_id,
            'jurusan_id' => $this->jurusan_id,
            'indeks_id' => $indeks?->id,
            'wali_guru_id' => $this->guru_id,
        ]);

        ToastMagic::success('Mengedit Kelas', 'Kelas berhasil diperbarui.');
    }

    private function ensureWaliKelasUser(int $guruId): bool
    {
        $guru = Guru::find($guruId);
        if (! $guru) {
            ToastMagic::error('Wali kelas tidak ditemukan.');

            return false;
        }

        $roleId = Role::query()->where('name', 'Wali Kelas')->value('id');
        if (! $roleId) {
            ToastMagic::error('Role wali kelas belum tersedia.');

            return false;
        }

        $user = $guru->user_id ? User::find($guru->user_id) : null;

        if (! $user && $guru->email) {
            $user = User::query()->where('email', $guru->email)->first();
        }

        if ($user) {
            $user->update([
                'role_id' => $roleId,
                'is_active' => true,
            ]);

            if (! $guru->user_id) {
                $guru->update(['user_id' => $user->id]);
            }

            return true;
        }

        if (! $guru->email) {
            ToastMagic::warning(
                'Email guru kosong',
                'Isi email guru agar akun wali kelas bisa dibuat.'
            );

            return false;
        }

        $user = User::create([
            'name' => $guru->nama,
            'email' => $guru->email,
            'password' => Hash::make('gurusmkn2bjm'),
            'role_id' => $roleId,
            'is_active' => true,
        ]);

        $guru->update(['user_id' => $user->id]);

        return true;
    }

    public function render()
    {
        return view('datamaster::livewire.data-kelas.edit');
    }
}
