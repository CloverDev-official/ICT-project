<?php

namespace App\Livewire\Murid\Rombel\Kelas;

use App\Helpers\ValidateMagic;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Rombel $rombel;
    public ?int $tingkat_id = null;
    public ?int $jurusan_id = null;
    public ?int $indeks_id = null;

    public $listTingkat = [];
    public $listJurusan = [];
    public $listIndeks = [];

    public function mount(int $rombelId): void
    {
        $this->rombel = Rombel::query()
            ->with(['tingkat', 'jurusan', 'indeks'])
            ->findOrFail($rombelId);

        $this->listTingkat = Tingkat::query()->orderBy('nama')->get(['id', 'nama']);
        $this->listJurusan = Jurusan::query()->orderBy('nama')->get(['id', 'nama']);
        $this->listIndeks = Indeks::query()->orderBy('nama')->get(['id', 'nama']);

        $this->fill([
            'tingkat_id' => $this->rombel->tingkat_id,
            'jurusan_id' => $this->rombel->jurusan_id,
            'indeks_id' => $this->rombel->indeks_id,
        ]);
    }

    public function update(): void
    {
        $validate = ValidateMagic::run(
            [
                'tingkat_id' => ['required', 'exists:tingkat,id'],
                'jurusan_id' => ['required', 'exists:jurusan,id'],
                'indeks_id' => [
                    'required',
                    'exists:indeks,id',
                    Rule::unique('rombel')
                        ->where(function ($q) {
                            return $q
                                ->where('tingkat_id', $this->tingkat_id)
                                ->where('jurusan_id', $this->jurusan_id)
                                ->where('indeks_id', $this->indeks_id);
                        })
                        ->ignore($this->rombel->id),
                ],
            ],
            [
                'tingkat_id.required' => 'Tingkat wajib dipilih.',
                'tingkat_id.exists' => 'Tingkat yang dipilih tidak valid.',
                'jurusan_id.required' => 'Jurusan wajib dipilih.',
                'jurusan_id.exists' => 'Jurusan yang dipilih tidak valid.',
                'indeks_id.required' => 'Kelas wajib dipilih.',
                'indeks_id.exists' => 'Kelas yang dipilih tidak valid.',
                'indeks_id.unique' => 'Kelas tersebut sudah terdaftar.',
            ],
        );

        if (!$validate) {
            return;
        }

        $this->rombel->update([
            'tingkat_id' => $this->tingkat_id,
            'jurusan_id' => $this->jurusan_id,
            'indeks_id' => $this->indeks_id,
        ]);
    }

    public function render()
    {
        return view('livewire.murid.rombel.kelas.edit-kelas');
    }
}
