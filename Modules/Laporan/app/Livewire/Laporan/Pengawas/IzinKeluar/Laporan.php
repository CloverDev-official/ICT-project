<?php

namespace Modules\Laporan\Livewire\Laporan\Pengawas\IzinKeluar;

use App\Models\Guru\Guru;
use App\Models\Murid\IzinMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use App\Services\Rombel\RombelFilterService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Str;

class Laporan extends Component
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
        $this->filterTanggal = now()->format('Y-m-d');
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

    public function generateQRCode($muridData)
    {
        $murid = Murid::query()
            ->aktif()
            ->with(['rombel.jurusan'])
            ->where('uuid', $muridData['uuid'] ?? null)
            ->first();

        if (!$murid) {
            return;
        }

        $className = $murid->rombel?->nama_lengkap ?? 'Kelas';
        $rawFilename = trim(sprintf('%s-%s-%s', $className, $murid->nama ?? 'Murid', $murid->nisn ?? 'NISN'));
        $filename = preg_replace('/[\/\\\\?%*:|"<>]/', '-', $rawFilename);
        $filename = preg_replace('/\s+/', '-', $filename);
        $filename = preg_replace('/-+/', '-', $filename);
        $filename = trim($filename, '-.');
        $filename = $filename . '.pdf';

        $this->dispatch('generateStudentCardPdf', murid: $murid->toArray(), filename: $filename);
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

    private function getIzinMurid()
    {
        return IzinMurid::query()
            ->with([
                'murid:id,nama,nipd,rombel_id',
                'murid.rombel',
            ])
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->where('alasan', 'like', '%' . $search . '%')
                    ->orWhereHas('murid', function ($muridQuery) use ($search) {
                        $muridQuery->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('nipd', 'like', '%' . $search . '%');
                    });
            })
            ->when($this->filterTanggal, function ($query) {
                $query->whereDate('tanggal', $this->filterTanggal);
            })
            ->tap(fn ($q) => $this->rombelFilterService()->applyRombelFiltersToRelation(
                $q,
                'murid.rombel',
                $this->filterTingkat,
                $this->filterJurusan,
                $this->filterIndeks,
                $this->waliRombelIds,
            ))
            ->latest('tanggal')
            ->latest('id')
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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[On('izin-refresh')]
    public function refreshData(): void
    {
        $this->resetPage();
        $this->refreshFilterOptions();
    }

    public function render()
    {
        return view('laporan::livewire.laporan.pengawas.izin-keluar.laporan', [
            'dataIzin' => $this->getIzinMurid(),
        ]);
    }
}
