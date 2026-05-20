<?php

namespace App\Livewire\Murid\Rekap;

use App\Models\Murid\AbsenMurid;
use App\Models\Guru\Guru;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Exports\Murid\RekapKehadiranExport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

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
    public ?string $filterTanggalDari = null;
    public ?string $filterTanggalSampai = null;
    public ?string $exportTanggalDari = null;
    public ?string $exportTanggalSampai = null;
    public ?int $exportRombelId = null;

    public bool $isWaliKelas = false;
    public ?array $waliRombelIds = null;
    private ?int $lockedTingkatId = null;
    private ?int $lockedJurusanId = null;
    private ?int $lockedIndeksId = null;

    public function mount(): void
    {
        $this->applyWaliKelasLock();
        $this->filterTanggalDari = now()->startOfMonth()->toDateString();
        $this->filterTanggalSampai = now()->toDateString();
        $this->exportTanggalDari = now()->startOfMonth()->toDateString();
        $this->exportTanggalSampai = now()->endOfMonth()->toDateString();
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
                $this->exportRombelId = $rombel->id;
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
                $q->from('rombel')->select('indeks_id');

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

    private function getTanggalDari(): string
    {
        return $this->filterTanggalDari ?: now()->startOfMonth()->toDateString();
    }

    private function getTanggalSampai(): string
    {
        return $this->filterTanggalSampai ?: now()->toDateString();
    }

    private function getTanggalRange(): array
    {
        $start = Carbon::parse($this->getTanggalDari());
        $end = Carbon::parse($this->getTanggalSampai());

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        return [$start->toDateString(), $end->toDateString()];
    }

    private function getYearFilter(): int
    {
        return (int) Carbon::parse($this->getTanggalDari())->format('Y');
    }

    private function getTanggalRangeLabel(): string
    {
        [$start, $end] = $this->getTanggalRange();

        if ($start === $end) {
            return $start;
        }

        return "{$start} - {$end}";
    }

    private function absenFilterBaseQuery()
    {
        return AbsenMurid::query()
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
            )
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                function ($q) {
                    $q->whereHas(
                        'murid.rombel',
                        fn($q) => $q->whereIn('id', $this->waliRombelIds),
                    );
                },
            );
    }

    private function absenBaseQuery()
    {
        return $this->absenFilterBaseQuery()->whereBetween(
            'tanggal',
            $this->getTanggalRange(),
        );
    }

    private function getRekapKehadiranBulanan(): array
    {
        $year = $this->getYearFilter();
        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "CAST(strftime('%m', tanggal) AS INTEGER)"
            : 'MONTH(tanggal)';

        $rows = $this->absenFilterBaseQuery()
            ->selectRaw("{$monthExpr} as month, COUNT(*) as total")
            ->whereYear('tanggal', $year)
            ->whereBetween('tanggal', $this->getTanggalRange())
            ->where('status', 'Hadir')
            ->groupBy(DB::raw($monthExpr))
            ->pluck('total', 'month');

        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $data[] = (int) ($rows[$month] ?? 0);
        }

        return [
            'year' => $this->getTanggalRangeLabel(),
            'data' => $data,
        ];
    }

    private function dispatchChartRekap(): void
    {
        $this->dispatch('chart-rekap-updated', chart: $this->getRekapKehadiranBulanan());
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
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                fn($q) => $q->whereIn('rombel_id', $this->waliRombelIds),
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
            ->fastPaginate($this->perPage);
    }

    private function refreshFilterOptions(): void
    {
        $this->filteredJurusan = $this->getAvailableJurusan();
        $this->filteredIndeks = $this->getAvailableIndeks();
    }

    public function updatedFilterTingkat(): void
    {
        if ($this->isWaliKelas) {
            $this->applyLockedFilters();
            return;
        }

        $this->resetPage();
        $this->refreshFilterOptions();
        $this->dispatchChartRekap();
    }

    public function updatedFilterJurusan(): void
    {
        if ($this->isWaliKelas) {
            $this->applyLockedFilters();
            return;
        }

        $this->resetPage();
        $this->refreshFilterOptions();
        $this->dispatchChartRekap();
    }

    public function updatedFilterIndeks(): void
    {
        if ($this->isWaliKelas) {
            $this->applyLockedFilters();
            return;
        }

        $this->resetPage();
        $this->refreshFilterOptions();
        $this->dispatchChartRekap();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggalDari(): void
    {
        $this->resetPage();
        $this->dispatchChartRekap();
    }

    public function updatedFilterTanggalSampai(): void
    {
        $this->resetPage();
        $this->dispatchChartRekap();
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
            'chartRekap' => $this->getRekapKehadiranBulanan(),
        ]);
    }

    public function exportExcel()
    {
        if ($this->isWaliKelas && $this->waliRombelIds !== null) {
            if (!$this->exportRombelId || !in_array($this->exportRombelId, $this->waliRombelIds, true)) {
                $this->exportRombelId = $this->waliRombelIds[0] ?? null;
            }
        }

        $tanggalDari = $this->exportTanggalDari ?: now()->startOfMonth()->toDateString();
        $tanggalSampai = $this->exportTanggalSampai ?: now()->endOfMonth()->toDateString();

        $start = Carbon::parse($tanggalDari);
        $end = Carbon::parse($tanggalSampai);

        if ($end->lt($start)) {
            [$tanggalDari, $tanggalSampai] = [$tanggalSampai, $tanggalDari];
        }

        $filename = $tanggalDari === $tanggalSampai
            ? "rekap-kehadiran-{$tanggalDari}.xlsx"
            : "rekap-kehadiran-{$tanggalDari}-sampai-{$tanggalSampai}.xlsx";

        return Excel::download(
            new RekapKehadiranExport(
                $tanggalDari,
                $tanggalSampai,
                $this->exportRombelId,
            ),
            $filename,
        );
    }
}
