<?php

namespace App\Livewire\Murid\Absen;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Murid\AbsenMurid;
use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Indeks;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public $listRombel;
    public $filteredJurusan;
    public $filteredIndeks;

    public ?string $search = null;
    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;
    public ?int $filterIndeks = null;
    public ?string $filterTanggal = null;

    public bool $isWaliKelas = false;
    public ?array $waliRombelIds = null;
    private ?int $lockedTingkatId = null;
    private ?int $lockedJurusanId = null;
    private ?int $lockedIndeksId = null;

    public function mount(): void
    {
        $this->applyWaliKelasLock();
        $this->listRombel = $this->getRombel();
        $this->refreshFilterOptions();
    }

    private function applyWaliKelasLock(): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $roleSlug = Str::slug($user->role?->name ?? '');
        $this->isWaliKelas = in_array($roleSlug, ['wali-kelas', 'wali-murid'], true);

        if (!$this->isWaliKelas) {
            return;
        }

        $guru = Guru::query()->where('user_id', $user->id)->first();
        if (!$guru) {
            $this->waliRombelIds = [];
            return;
        }

        $this->waliRombelIds = Rombel::query()
            ->where('wali_guru_id', $guru->id)
            ->pluck('id')
            ->all();

        if (count($this->waliRombelIds) === 1) {
            $rombel = Rombel::query()->find($this->waliRombelIds[0]);
            if ($rombel) {
                $this->lockedTingkatId = $rombel->tingkat_id;
                $this->lockedJurusanId = $rombel->jurusan_id;
                $this->lockedIndeksId = $rombel->indeks_id;
                $this->filterTingkat = $this->lockedTingkatId;
                $this->filterJurusan = $this->lockedJurusanId;
                $this->filterIndeks = $this->lockedIndeksId;
            }
        }
    }

    private function applyLockedFilters(): void
    {
        if (!$this->isWaliKelas) {
            return;
        }

        $this->filterTingkat = $this->lockedTingkatId;
        $this->filterJurusan = $this->lockedJurusanId;
        $this->filterIndeks = $this->lockedIndeksId;
    }

    private function applyRombelFilter($q)
    {
        if ($this->isWaliKelas && $this->waliRombelIds !== null) {
            $q->whereIn('id', $this->waliRombelIds);
        }

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
            ->get();
    }

    private function getAvailableJurusan()
    {
        return Jurusan::query()
            ->whereIn('id', function ($q) {
                $q->from('rombel')
                  ->select('jurusan_id');

                if ($this->isWaliKelas && $this->waliRombelIds !== null) {
                    $q->whereIn('id', $this->waliRombelIds);
                }
                if ($this->filterTingkat) {
                    $q->where('tingkat_id', $this->filterTingkat);
                }
                if ($this->filterIndeks) {
                    $q->where('indeks_id', $this->filterIndeks);
                }
            })
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    private function getAvailableIndeks()
    {
        return Indeks::query()
            ->whereIn('id', function ($q) {
                $q->from('rombel')
                  ->select('indeks_id');

                if ($this->isWaliKelas && $this->waliRombelIds !== null) {
                    $q->whereIn('id', $this->waliRombelIds);
                }
                if ($this->filterTingkat) {
                    $q->where('tingkat_id', $this->filterTingkat);
                }
                if ($this->filterJurusan) {
                    $q->where('jurusan_id', $this->filterJurusan);
                }
            })
            ->orderBy('nama')
            ->get(['id', 'nama']);
    }

    private function getAbsen()
    {
        return AbsenMurid::query()
            ->with([
                'murid:id,nama,rombel_id',
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
            ->when($this->filterTanggal, fn ($q) =>
                $q->whereDate('tanggal', $this->filterTanggal)
            )
            ->when(
                $this->filterTingkat || $this->filterJurusan || $this->filterIndeks,
                function ($q) {
                    $q->whereHas('murid.rombel', fn ($q) => $this->applyRombelFilter($q));
                }
            )
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                function ($q) {
                    $q->whereHas('murid.rombel', fn ($q) => $q->whereIn('id', $this->waliRombelIds));
                }
            )
            ->orderByDesc('tanggal')
            ->fastPaginate($this->perPage);
    }

    private function refreshFilterOptions(): void
    {
        $this->filteredJurusan = $this->getAvailableJurusan();
        $this->filteredIndeks  = $this->getAvailableIndeks();
    }

    public function updatedFilterTingkat(): void
    {
        if ($this->isWaliKelas) {
            $this->applyLockedFilters();
            return;
        }

        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function updatedFilterJurusan(): void
    {
        if ($this->isWaliKelas) {
            $this->applyLockedFilters();
            return;
        }

        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function updatedFilterIndeks(): void
    {
        if ($this->isWaliKelas) {
            $this->applyLockedFilters();
            return;
        }

        $this->resetPage();
        $this->refreshFilterOptions();
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
        return view('livewire.murid.absen.absensi-murid', [
            'listAbsen' => $this->getAbsen(),
        ]);
    }
}