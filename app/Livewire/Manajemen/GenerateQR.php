<?php

namespace App\Livewire\Manajemen;

use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Component;

class GenerateQR extends Component
{
    public $listRombel;
    public $filteredJurusan;
    public $filteredIndeks;

    public ?int $filterIndeks = null;
    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;

    public function mount(): void
    {
        $this->listRombel = $this->getRombel();
        $this->refreshFilterOptions();
    }

    private function applyRombelFilter($q)
    {
        $q->when(
            $this->filterTingkat,
            fn($q) => $q->where('tingkat_id', $this->filterTingkat),
        )
            ->when(
                $this->filterJurusan,
                fn($q) => $q->where('jurusan_id', $this->filterJurusan),
            )
            ->when(
                $this->filterIndeks,
                fn($q) => $q->where('indeks_id', $this->filterIndeks),
            );
    }

    private function rombelBaseQuery()
    {
        return Rombel::query()->tap(fn($q) => $this->applyRombelFilter($q));
    }

    private function getRombel()
    {
        return $this->rombelBaseQuery()
            ->with(['tingkat:id,nama', 'jurusan:id,nama', 'indeks:id,nama'])
            ->get();
    }

    private function getAvailableJurusan()
    {
        return Jurusan::query()
            ->whereIn('id', function ($q) {
                $q->from('rombel')->select('jurusan_id');
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
                $q->from('rombel')->select('indeks_id');
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

    private function getMurid()
    {
        return Murid::query()
            ->with([
                'rombel:id,tingkat_id,jurusan_id,indeks_id,nama',
                'rombel.tingkat:id,nama',
                'rombel.jurusan:id,nama',
                'rombel.indeks:id,nama',
            ])
            ->when(
                $this->filterTingkat ||
                    $this->filterJurusan ||
                    $this->filterIndeks,
                function ($q) {
                    $q->whereHas(
                        'rombel',
                        fn($q) => $this->applyRombelFilter($q),
                    );
                },
            )
            ->orderBy('nama')
            ->orderBy('id')
            ->get();
    }

    private function refreshFilterOptions(): void
    {
        $this->filteredJurusan = $this->getAvailableJurusan();
        $this->filteredIndeks = $this->getAvailableIndeks();
    }

    public function updatedFilterTingkat(): void
    {
        $this->refreshFilterOptions();
    }

    public function updatedFilterJurusan(): void
    {
        $this->refreshFilterOptions();
    }

    public function updatedFilterIndeks(): void
    {
        $this->refreshFilterOptions();
    }

    public function render()
    {
        return view('livewire.manajemen.generate-qr', [
            'listMurid' => $this->getMurid(),
        ]);
    }
}
