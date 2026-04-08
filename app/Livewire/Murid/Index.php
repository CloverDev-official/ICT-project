<?php

namespace App\Livewire\Murid;

use App\Models\Murid\Murid;
use App\Models\Murid\Rombel;
use Livewire\Component;

class Index extends Component
{
    public $listMurid = [];
    public $listRombel = [];
    public $tingkatRombel = [];

    public function mount(): void
    {
        $this->listMurid = Murid::orderBy("nama")->get();

        $this->listRombel = Rombel::with([
            "tingkatKelas",
            "jurusan",
            "indeksRombel",
        ])->get();

        // dd($this->listRombel);

        $this->tingkatRombel = $this->listRombel->pluck("tingkatKelas");
    }

    public function render()
    {
        return view("livewire.data-murid");
    }
}
