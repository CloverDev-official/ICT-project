<?php

namespace App\Livewire\Murid\Rombel\Kelas;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Edit extends Component
{
    public Rombel $rombel;
    public ?int $tingkat_id = null;
    public ?int $jurusan_id = null;
    public int|string|null $indeks_id = null;
    public ?int $tahun_masuk = null;
    public ?int $guru_id = null;

    public $listTingkat = [];
    public $listJurusan = [];
    public $listIndeks = [];
    public $listGuru = [];

    public function mount(int $rombelId): void
    {
        $this->rombel = Rombel::query()
            ->with(['tingkat', 'jurusan', 'indeks', 'waliGuru'])
            ->findOrFail($rombelId);

        $this->listTingkat = Tingkat::query()->orderBy('nama')->get(['id', 'nama']);
        $this->listJurusan = Jurusan::query()->orderBy('nama')->get(['id', 'nama']);
        $this->listIndeks = Indeks::options();
        $this->listGuru = Guru::query()->orderBy('nama')->get(['id', 'nama', 'email', 'user_id']);

        $this->fill([
            'tingkat_id' => $this->rombel->tingkat_id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'indeks_id' => $this->rombel->indeks_id,
            'tahun_masuk' => $this->rombel->tahun_masuk,
            'guru_id' => $this->rombel->wali_guru_id,
        ]);
    }

    public function update(): void
    {
        $validate = ValidateMagic::run(
            [
                'tingkat_id' => ['required', 'exists:tingkat,id'],
                'jurusan_id' => ['required', 'exists:jurusan,id'],
                'indeks_id' => ['required'],
                'tahun_masuk' => ['required', 'integer', 'min:1900'],
                'guru_id' => ['nullable', 'exists:guru,id'],
            ],
            [
                'tingkat_id.required' => 'Tingkat wajib dipilih.',
                'tingkat_id.exists' => 'Tingkat yang dipilih tidak valid.',
                'jurusan_id.required' => 'Jurusan wajib dipilih.',
                'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
                'indeks_id.required' => 'Kelas wajib dipilih.',
                'tahun_masuk.required' => 'Tahun masuk wajib dipilih.',
                'tahun_masuk.integer' => 'Tahun masuk tidak valid.',
                'guru_id.exists' => 'Wali kelas yang dipilih tidak valid.',
            ],
        );

        if (!$validate) {
            return;
        }

        if ($this->guru_id && !$this->ensureWaliKelasUser($this->guru_id)) {
            return;
        }

        $indeks = Indeks::resolveSelection($this->indeks_id);

        if (!$indeks) {
            ToastMagic::error('Kelas yang dipilih tidak valid.');
            return;
        }

        $exists = Rombel::query()
            ->where('tingkat_id', $this->tingkat_id)
            ->where('jurusan_id', $this->jurusan_id)
            ->where('indeks_id', $indeks->id)
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
            'indeks_id' => $indeks->id,
            'wali_guru_id' => $this->guru_id,
        ]);

        ToastMagic::success('Mengedit Kelas','Kelas berhasil diperbarui.');
    }

    private function ensureWaliKelasUser(int $guruId): bool
    {
        $guru = Guru::find($guruId);
        if (!$guru) {
            ToastMagic::error('Wali kelas tidak ditemukan.');
            return false;
        }

        $roleId = Role::query()->where('name', 'Wali Kelas')->value('id');
        if (!$roleId) {
            ToastMagic::error('Role wali kelas belum tersedia.');
            return false;
        }

        $user = $guru->user_id ? User::find($guru->user_id) : null;

        if (!$user && $guru->email) {
            $user = User::query()->where('email', $guru->email)->first();
        }

        if ($user) {
            $user->update([
                'role_id' => $roleId,
                'is_active' => true,
            ]);

            if (!$guru->user_id) {
                $guru->update(['user_id' => $user->id]);
            }

            return true;
        }

        if (!$guru->email) {
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
        return view('livewire.murid.rombel.kelas.edit-kelas');
    }
}
