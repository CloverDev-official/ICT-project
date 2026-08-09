<?php

namespace Modules\DataMaster\Livewire\DataGuru;

use App\Models\Guru\Guru;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public ?string $search = null;
    public ?string $filterStatus = null;
    public ?string $filterjenis = null;

    private function getStatusOptions(): array
    {
        return $this->getDistinctGuruOptions('status_kepegawaian');
    }

    private function getJenisOptions(): array
    {
        return $this->getDistinctGuruOptions('jenis_ptk');
    }

    private function getDistinctGuruOptions(string $column): array
    {
        return Guru::query()
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->mapWithKeys(fn ($value) => [$value => $value])
            ->all();
    }

    private function getGuru()
    {
        return Guru::query()
            ->when($this->search, function ($q) {
                $search = trim($this->search);

                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nuptk', $search)
                      ->orWhere('nip', $search)
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(filled($this->filterStatus), fn ($q) => $q->where('status_kepegawaian', $this->filterStatus))
            ->when(filled($this->filterjenis), fn ($q) => $q->where('jenis_ptk', $this->filterjenis))
            ->orderBy('nama')
            ->orderBy('id')
            ->fastPaginate($this->perPage);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterjenis(): void
    {
        $this->resetPage();
    }

    #[On('guru-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('datamaster::livewire.data-guru.data', [
            'listGuru' => $this->getGuru(),
            'statusOptions' => $this->getStatusOptions(),
            'jenisOptions' => $this->getJenisOptions(),
        ]);
    }
}
