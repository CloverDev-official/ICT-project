<?php

namespace Modules\Absensi\Livewire\Absen\Murid;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Murid\AbsenMurid;
use App\Models\Guru\Guru;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Support\Str;
use App\Services\Rombel\RombelFilterService;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public $listRombel;
    public $filteredJurusan;
    public $filteredIndeks;
    public $status;

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

    public const STATUS = [
        1 => 'Hadir',
        2 => 'Izin',
        3 => 'Sakit',
        4 => 'Alpa',
        5 => 'Terlambat',
    ];

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

    private function rombelFilterService(): RombelFilterService
    {
        return app(RombelFilterService::class);
    }

    private function getRombel()
    {
        return $this->rombelFilterService()->getRombelList(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            $this->waliRombelIds,
        );
    }

    private function getAbsen()
    {
        return AbsenMurid::query()
            ->with([
                'murid:id,nama,nipd,nisn,rombel_id',
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
            ->when($this->status, fn ($q) =>
                $q->where('status', self::STATUS[$this->status] ?? null)
            )
            ->tap(fn ($q) => $this->rombelFilterService()->applyRombelFiltersToRelation(
                $q,
                'murid.rombel',
                $this->filterTingkat,
                $this->filterJurusan,
                $this->filterIndeks,
                $this->waliRombelIds,
            ))
            ->orderByDesc('tanggal')
            ->fastPaginate($this->perPage);
    }

    private function refreshFilterOptions(): void
    {
        $this->filteredJurusan = $this->rombelFilterService()->getAvailableJurusan(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            $this->waliRombelIds,
        );
        $this->filteredIndeks = $this->rombelFilterService()->getAvailableIndeks(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            $this->waliRombelIds,
        );
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

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('absensi::livewire.absen.murid.absensi', [
            'listAbsen' => $this->getAbsen(),
        ]);
    }
}