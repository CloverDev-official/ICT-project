<?php

namespace Modules\DataMaster\Livewire\DataJurusan;

use App\Helpers\ValidateMagic;
use App\Models\Murid\Rombel\Jurusan;
use Livewire\Component;

class Edit extends Component
{
    public Jurusan $jurusan;
    public string $nama = '';

    public function mount(int $jurusanId): void
    {
        $this->jurusan = Jurusan::query()->findOrFail($jurusanId);
        $this->nama = $this->jurusan->nama ?? '';
    }

    public function update(): void
    {
        $validate = ValidateMagic::run(
            [
                'nama' => 'required|unique:jurusan,nama,' . $this->jurusan->id,
            ],
            [
                'nama.required' => 'Nama jurusan wajib diisi.',
                'nama.unique' => 'Nama jurusan sudah digunakan.',
            ],
        );

        if (!$validate) {
            return;
        }

        $this->jurusan->update([
            'nama' => $this->nama,
        ]);
    }

    public function render()
    {
        return view('datamaster::livewire.data-jurusan.edit');
    }
}
