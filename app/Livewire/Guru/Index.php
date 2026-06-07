<?php

namespace App\Livewire\Guru;

use App\Models\Guru\Guru;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public ?string $search = null;
    public ?int $filterStatus = null;
    public ?int $filterjenis = null;

    private function getStatusOptions(): array
    {
        return [
            0 => 'PNS',
            1 => 'Honorer',
        ];
    }

    private function getJenisOptions(): array
    {
        return [
            0 => 'Guru Mapel',
            1 => 'Guru BK',
        ];
    }

    private function getGuru()
    {
        $statusOptions = $this->getStatusOptions();
        $jenisOptions = $this->getJenisOptions();

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
            ->when($this->filterStatus !== null, function ($q) use ($statusOptions) {
                $status = $statusOptions[$this->filterStatus] ?? null;

                if ($status) {
                    $q->where('status_kepegawaian', $status);
                }
            })
            ->when($this->filterjenis !== null, function ($q) use ($jenisOptions) {
                $jenis = $jenisOptions[$this->filterjenis] ?? null;

                if ($jenis) {
                    $q->where('jenis_ptk', $jenis);
                }
            })
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
        return view('livewire.guru.data-guru', [
            'listGuru' => $this->getGuru(),
            'statusOptions' => $this->getStatusOptions(),
            'jenisOptions' => $this->getJenisOptions(),
        ]);
    }
}
