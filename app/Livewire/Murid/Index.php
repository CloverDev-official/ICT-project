<?php

namespace App\Livewire\Murid;

use App\Helpers\DownloadFile;
use App\Helpers\QRCodeHelper;
use App\Models\Guru\Guru;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public bool $showDelete = false;

    public $listRombel;
    public $filteredJurusan;
    public $filteredIndeks;

    public ?string $search = null;
    public ?int $filterIndeks = null;
    public ?int $filterTingkat = null;
    public ?int $filterJurusan = null;

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

    public function generateQRCode($muridData)
    {
        $filename = "{$muridData['nama']} {$muridData['nipd']}.png";

        $filename = preg_replace('/\s+/', '_', $filename);
        $filename = preg_replace('/[\/\\\\?%*:|"<>]/', '-', $filename);

        $this->dispatch('generateQRPNGDownload', text: $muridData['ulid'], filename: $filename);
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
            ->orderBy('id')
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
            ->orderBy('id')
            ->get(['id', 'nama']);
    }

    private function getMurid()
    {
        return Murid::query()
            ->with([
                'rombel:id,tingkat_id,jurusan_id,indeks_id,nama',
            ])
            ->when($this->search, function ($q) {
                $search = trim($this->search);

                $q->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nipd', $search)
                      ->orWhere('nisn', $search);
                });
            })
            ->when(
                $this->filterTingkat || $this->filterJurusan || $this->filterIndeks,
                function ($q) {
                    $q->whereHas('rombel', fn ($q) => $this->applyRombelFilter($q));
                }
            )
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                function ($q) {
                    $q->whereHas('rombel', fn ($q) => $q->whereIn('id', $this->waliRombelIds));
                }
            )
            ->orderBy('nama')
            ->orderBy('id')
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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[On('murid-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function openDelete(): void
    {
        $this->showDelete = true;
        $this->dispatch('openModalDelete');
    }

    public function render()
    {
        return view('livewire.murid.data-murid', [
            'listMurid' => $this->getMurid(),
        ]);
    }
}