<?php

namespace App\Livewire\Murid\Rombel\Kelas;

use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public $listTingkat;
    public $filteredJurusan;
    public $filteredIndeks;

    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;
    public ?int $filterIndeks = null;

    public function mount(): void
    {
        $this->listTingkat = Tingkat::query()
            ->orderBy('nama')
            ->orderBy('id')
            ->get(['id', 'nama']);

        $this->refreshFilterOptions();
    }

    private function applyRombelFilter($q): void
    {
        $q->when($this->filterTingkat, fn ($q) => $q->where('tingkat_id', $this->filterTingkat))
            ->when($this->filterJurusan, fn ($q) => $q->where('jurusan_id', $this->filterJurusan))
            ->when($this->filterIndeks, fn ($q) => $q->where('indeks_id', $this->filterIndeks));
    }

    private function rombelBaseQuery()
    {
        return Rombel::query()
            ->tap(fn ($q) => $this->applyRombelFilter($q));
    }

    private function getRombel()
    {
        return $this->rombelBaseQuery()
            ->with(['tingkat:id,nama', 'jurusan:id,nama', 'indeks:id,nama'])
            ->orderBy('tingkat_id')
            ->orderBy('jurusan_id')
            ->orderBy('indeks_id')
            ->fastPaginate($this->perPage);
    }

    private function getAvailableJurusan()
    {
        return Jurusan::query()
            ->whereIn('id', function ($q) {
                $q->from('rombel')
                    ->select('jurusan_id');
                if ($this->filterTingkat) {
                    $q->where('tingkat_id', $this->filterTingkat);
                }
                if ($this->filterIndeks) {
                    $q->where('indeks_id', $this->filterIndeks);
                }
            })
            ->orderBy('nama')
            ->orderBy('id')
            ->get(['id', 'nama']);
    }

    private function getAvailableIndeks()
    {
        return Indeks::query()
            ->whereIn('id', function ($q) {
                $q->from('rombel')
                    ->select('indeks_id');
                if ($this->filterTingkat) {
                    $q->where('tingkat_id', $this->filterTingkat);
                }
                if ($this->filterJurusan) {
                    $q->where('jurusan_id', $this->filterJurusan);
                }
            })
            ->orderBy('nama')
            ->orderBy('id')
            ->get(['id', 'nama']);
    }

    private function refreshFilterOptions(): void
    {
        $this->filteredJurusan = $this->getAvailableJurusan();
        $this->filteredIndeks = $this->getAvailableIndeks();
    }

    public function updatedFilterTingkat(): void
    {
        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function updatedFilterJurusan(): void
    {
        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function updatedFilterIndeks(): void
    {
        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[On('kelas-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function render()
    {
        return view('livewire.murid.rombel.kelas.data-kelas', [
            'listRombel' => $this->getRombel(),
        ]);
    }
}
