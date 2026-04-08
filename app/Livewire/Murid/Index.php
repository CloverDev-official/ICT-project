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
    public $jurusanRombel = [];

    public string $filterTingkat = '';
    public string $filterJurusan = '';
    public string $filterKelas = '';

    public function mount(): void
    {
        $this->listRombel = Rombel::query()
            ->with(['tingkatKelas', 'jurusan', 'indeksRombel'])
            ->get()
            ->sortBy(fn ($r) => $r->nama)
            ->values();

        $this->tingkatRombel = $this->listRombel
            ->pluck('tingkatKelas.nama')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->jurusanRombel = $this->listRombel
            ->pluck('jurusan.nama')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function render()
    {
        $query = Murid::query()
            ->with(['rombel.tingkatKelas', 'rombel.jurusan', 'rombel.indeksRombel'])
            ->orderBy('nama');

        if ($this->filterTingkat !== '') {
            $query->whereHas('rombel.tingkatKelas', function ($q) {
                $q->where('nama', $this->filterTingkat);
            });
        }

        if ($this->filterJurusan !== '') {
            $query->whereHas('rombel.jurusan', function ($q) {
                $q->where('nama', $this->filterJurusan);
            });
        }

        if ($this->filterKelas !== '') {
            $rombelId = $this->listRombel
                ->firstWhere('nama_lengkap', $this->filterKelas)
                ?->id;

            if ($rombelId) {
                $query->where('rombel_id', $rombelId);
            }
        }

        $this->listMurid = $query->get();

        return view("livewire.data-murid");
    }
}
