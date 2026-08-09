<?php

namespace Modules\DataMaster\Livewire\DataJurusan;

use App\Helpers\ToastMagic;
use App\Helpers\ValidateMagic;
use App\Models\Murid\Rombel\Jurusan;
use Livewire\Component;

class Create extends Component
{
    public string $nama = '';

    public function store(): void
    {
        $validate = ValidateMagic::run(
            [
                'nama' => 'required|unique:jurusan,nama',
            ],
            [
                'nama.required' => 'Nama jurusan wajib diisi.',
                'nama.unique' => 'Nama jurusan sudah digunakan.',
            ],
        );

        if (!$validate) {
            return;
        }

        Jurusan::create([
            'nama' => $this->nama,
        ]);

        ToastMagic::success(
            'Menambahkan Jurusan',
            "Berhasil menambahkan jurusan {$this->nama}."
        );

        $this->reset('nama');
    }

    public function render()
    {
        return view('datamaster::livewire.data-jurusan.create');
    }
}
