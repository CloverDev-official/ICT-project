<?php

namespace App\Livewire\Murid\Rombel\Jurusan;

use App\Models\Murid\Rombel\Jurusan;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public ?string $search = null;

    private function getJurusan()
    {
        return Jurusan::query()
            ->when($this->search, function ($q) {
                $search = trim($this->search);

                $q->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->orderBy('id')
            ->fastPaginate($this->perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[On('jurusan-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.murid.rombel.jurusan.data-jurusan', [
            'listJurusan' => $this->getJurusan(),
        ]);
    }
}
