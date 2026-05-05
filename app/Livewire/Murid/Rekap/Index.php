<?php

namespace App\Livewire\Murid\Rekap;

use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public $listRombel;
    public $filteredJurusan;
    public $filteredIndeks;

    public array $statusOptions = ['Sakit', 'Izin', 'Alpa'];

    public ?string $search = null;
    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;
    public ?int $filterIndeks = null;
    public ?string $filterStatus = null;
    public ?string $filterTanggal = null;

    public function mount(): void
    {
        $this->filterTanggal = now()->toDateString();
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

    private function getTanggalFilter(): string
    {
        return $this->filterTanggal ?: now()->toDateString();
    }

    private function absenBaseQuery()
    {
        return AbsenMurid::query()
            ->whereDate('tanggal', $this->getTanggalFilter())
            ->when(
                $this->filterTingkat ||
                    $this->filterJurusan ||
                    $this->filterIndeks,
                function ($q) {
                    $q->whereHas(
                        'murid.rombel',
                        fn($q) => $this->applyRombelFilter($q),
                    );
                },
            );
    }

    private function getStatistik(): array
    {
        $totalMurid = Murid::query()
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
            ->count();

        $baseQuery = $this->absenBaseQuery();

        $hadir = (clone $baseQuery)->where('status', 'Hadir')->count();
        $sakit = (clone $baseQuery)->where('status', 'Sakit')->count();
        $izin = (clone $baseQuery)->where('status', 'Izin')->count();

        $persentase =
            $totalMurid > 0 ? (int) round(($hadir / $totalMurid) * 100) : 0;

        return [
            'totalMurid' => $totalMurid,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'persentase' => $persentase,
        ];
    }

    private function muridRombelIdSubquery()
    {
        return Murid::select('rombel_id')
            ->whereColumn('murid.id', 'absen_murid.murid_id')
            ->limit(1);
    }

    private function rombelFieldSubquery(string $field)
    {
        return Rombel::select($field)
            ->where('rombel.id', $this->muridRombelIdSubquery())
            ->limit(1);
    }

    private function tingkatIdSubquery()
    {
        return $this->rombelFieldSubquery('tingkat_id');
    }

    private function jurusanIdSubquery()
    {
        return $this->rombelFieldSubquery('jurusan_id');
    }

    private function indeksIdSubquery()
    {
        return $this->rombelFieldSubquery('indeks_id');
    }

    private function getTidakHadir()
    {
        return $this->absenBaseQuery()
            ->with([
                'murid:id,nama,rombel_id,nipd,nisn',
                'murid.rombel:id,tingkat_id,jurusan_id,indeks_id',
                'murid.rombel.tingkat:id,nama',
                'murid.rombel.jurusan:id,nama',
                'murid.rombel.indeks:id,nama',
            ])
            ->when($this->search, function ($q) {
                $search = trim($this->search);

                $q->whereHas('murid', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nipd', $search)
                        ->orWhere('nisn', $search);
                });
            })
            ->when(
                $this->filterStatus,
                fn($q) => $q->where('status', $this->filterStatus),
                fn($q) => $q->whereIn('status', $this->statusOptions),
            )
            ->orderBy($this->tingkatIdSubquery())
            ->orderBy($this->jurusanIdSubquery())
            ->orderBy($this->indeksIdSubquery())
            ->fastPaginate($this->perPage);
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

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.murid.rekap.rekap-absen-murid', [
            'listAbsen' => $this->getTidakHadir(),
            'statistik' => $this->getStatistik(),
        ]);
    }
}
