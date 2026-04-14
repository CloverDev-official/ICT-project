<?php

namespace App\Livewire\Murid;

use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public $showDelete = false;
    public $listRombel;
    public $tingkatRombel;

    public $filteredRombel;
    public $filteredJurusan;
    public $listMurid;
    
    public ?string $search = null;
    public ?int $filterRombel = null;
    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;

    public function mount(): void
    {
        $this->listRombel = $this->getAllRombel();
        $this->tingkatRombel = $this->getTingkatRombel();

        $this->refreshData();
    }

    private function getAllRombel()
    {
        return Rombel::with(['tingkat', 'jurusan', 'indeks'])->get();
    }

    private function getTingkatRombel()
    {
        return $this->listRombel
            ->pluck('tingkat')
            ->filter()
            ->unique('id');
    }

    private function baseRombelFilter()
    {
        return $this->listRombel
            ->when($this->filterTingkat, fn ($q) =>
                $q->where('tingkat_id', $this->filterTingkat)
            );
    }

    private function getFilteredJurusan()
    {
        return $this->baseRombelFilter()
            ->pluck('jurusan')
            ->filter()
            ->unique('id');
    }

    private function getFilteredRombel()
    {
        return $this->baseRombelFilter()
            ->when($this->filterJurusan, fn ($q) =>
                $q->where('jurusan_id', $this->filterJurusan)
            );
    }

    private function getMurid()
    {
        return Murid::query()
            ->with(['rombel.tingkat', 'rombel.jurusan', 'rombel.indeks'])
            ->when($this->search, fn ($q) =>
                $q->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nipd', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
                })
            )
            ->when($this->filterTingkat, fn ($q) =>
                $q->whereHas('rombel', fn ($q) =>
                    $q->where('tingkat_id', $this->filterTingkat)
                )
            )
            ->when($this->filterJurusan, fn ($q) =>
                $q->whereHas('rombel', fn ($q) =>
                    $q->where('jurusan_id', $this->filterJurusan)
                )
            )
            ->when($this->filterRombel, fn ($q) =>
                $q->where('rombel_id', $this->filterRombel)
            )
            ->orderBy('nama')
            ->get();
    }

    public function updatedFilterTingkat()
    {
        $this->filterRombel = null;
        $this->refreshData();
    }

    public function updatedFilterJurusan()
    {
        $this->filterRombel = null;
        $this->refreshData();
    }

    public function updatedFilterRombel()
    {
        $this->refreshData();
    }

    public function updatedIndeks()
    {
        $this->refreshData();
    }

    public function updatedSearch()
    {
        $this->refreshData();
    }

    #[On('murid-refresh')]
    public function refreshData()
    {
        $this->filteredJurusan = $this->getFilteredJurusan();
        $this->filteredRombel  = $this->getFilteredRombel();
        $this->listMurid       = $this->getMurid();
    }

    public function openDelete(): void
    {
        $this->showDelete = true;
        $this->dispatch('openModalDelete');
    }


    public function render()
    {
        return view('livewire.murid.data-murid');
    }
}