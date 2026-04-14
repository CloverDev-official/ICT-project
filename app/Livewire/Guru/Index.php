<?php

namespace App\Livewire\Guru;

use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Component;

class Index extends Component
{
    public $listGuru = [];
    public $listRombel = [];

    public function mount(): void
    {
        $this->listGuru = Guru::orderBy("nama")->get();

        $this->listRombel = Rombel::with([
            "tingkat",
            "jurusan",
            "indeks",
        ])->get();
    }

    public function render()
    {
        return view("livewire.data-guru");
    }
}
