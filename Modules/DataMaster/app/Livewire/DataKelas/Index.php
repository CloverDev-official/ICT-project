<?php

namespace Modules\DataMaster\Livewire\DataKelas;

use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Services\Rombel\RombelFilterService;
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
            ->get([
                'id',
                'nama',
            ]);

        $this->refreshFilterOptions();
    }

    private function rombelFilterService(): RombelFilterService
    {
        return app(RombelFilterService::class);
    }

    /**
     * Ambil daftar Rombel berdasarkan filter aktif.
     */
    private function getRombel()
    {
        return Rombel::query()
            ->with([
                'tingkat:id,nama',
                'jurusan:id,nama',
                'indeks:id,nama',
            ])
            ->when(
                $this->filterTingkat !== null,
                fn ($q) => $q->where(
                    'tingkat_id',
                    $this->filterTingkat
                )
            )
            ->when(
                $this->filterJurusan !== null,
                fn ($q) => $q->where(
                    'jurusan_id',
                    $this->filterJurusan
                )
            )
            ->when(
                $this->filterIndeks !== null,
                fn ($q) => $q->where(
                    'indeks_id',
                    $this->filterIndeks
                )
            )
            ->orderBy('tingkat_id')
            ->orderBy('jurusan_id')
            ->orderBy('indeks_id')
            ->orderBy('id')
            ->fastPaginate($this->perPage);
    }

    /**
     * Refresh option Jurusan dan Indeks
     * berdasarkan filter yang sedang aktif.
     */
    private function refreshFilterOptions(): void
    {
        $service = $this->rombelFilterService();

        $this->filteredJurusan = $service->getAvailableJurusan(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            null,
        );

        $this->filteredIndeks = $service->getAvailableIndeks(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            null,
        );
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
        return view('datamaster::livewire.data-kelas.data', [
            'listRombel' => $this->getRombel(),
        ]);
    }
}