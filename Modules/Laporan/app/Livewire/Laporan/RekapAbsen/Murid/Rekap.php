<?php

namespace Modules\Laporan\Livewire\Laporan\RekapAbsen\Murid;

use App\Enums\AttendanceStatus;
use App\Exports\Murid\RekapKehadiranExport;
use App\Models\Guru\Guru;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use App\Services\Rombel\RombelFilterService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Rekap extends Component
{
    use WithPagination;

    public int $perPage = 20;

    public $listRombel;

    public $filteredJurusan;

    public $filteredIndeks;

    public array $statusOptions = [];

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
        $this->statusOptions = AttendanceStatus::rekapLowercase();
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
        if (! $user) {
            return;
        }

        $this->isWaliKelas = $user->hasOnlyRoles(['Wali Kelas', 'Wali Murid']);

        if (! $this->isWaliKelas) {
            return;
        }

        $guru = Guru::query()->where('user_id', $user->id)->first();
        if (! $guru) {
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
        if (! $this->isWaliKelas) {
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

    private function applyRombelFilter($q)
    {
        $this->rombelFilterService()->applyRombelFilters($q, $this->filterTingkat, $this->filterJurusan, $this->filterIndeks, $this->isWaliKelas ? $this->waliRombelIds : null);
    }

    private function rombelBaseQuery()
    {
        return Rombel::query()->tap(fn ($q) => $this->rombelFilterService()->applyRombelFilters($q, $this->filterTingkat, $this->filterJurusan, $this->filterIndeks, $this->isWaliKelas ? $this->waliRombelIds : null));
    }

    private function getRombel()
    {
        return $this->rombelFilterService()->getRombelList(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            $this->isWaliKelas ? $this->waliRombelIds : null,
        );
    }

    private function getAvailableJurusan()
    {
        return $this->rombelFilterService()->getAvailableJurusan(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            $this->isWaliKelas ? $this->waliRombelIds : null,
        );
    }

    private function getAvailableIndeks()
    {
        return $this->rombelFilterService()->getAvailableIndeks(
            $this->filterTingkat,
            $this->filterJurusan,
            $this->filterIndeks,
            $this->isWaliKelas ? $this->waliRombelIds : null,
        );
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
            ->tap(fn ($q) => $this->rombelFilterService()->applyRombelFiltersToRelation(
                $q,
                'murid.rombel',
                $this->filterTingkat,
                $this->filterJurusan,
                $this->filterIndeks,
                $this->isWaliKelas ? $this->waliRombelIds : null,
            ));
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
            ->whereRaw('LOWER(status) = ?', ['hadir'])
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
            ->aktif()
            ->tap(fn ($q) => $this->rombelFilterService()->applyRombelFiltersToRelation(
                $q,
                'rombel',
                $this->filterTingkat,
                $this->filterJurusan,
                $this->filterIndeks,
                $this->isWaliKelas ? $this->waliRombelIds : null,
            ))
            ->when(
                $this->isWaliKelas && $this->waliRombelIds !== null,
                fn ($q) => $q->whereIn('rombel_id', $this->waliRombelIds),
            )
            ->count();

        $baseQuery = $this->absenBaseQuery();

        $rows = (clone $baseQuery)
            ->selectRaw('LOWER(status) as status_key, COUNT(*) as total')
            ->whereIn(DB::raw('LOWER(status)'), ['hadir', 'sakit', 'izin', 'alpa', 'selesai'])
            ->groupBy(DB::raw('LOWER(status)'))
            ->pluck('total', 'status_key');

        $hadir = (int) ($rows['hadir'] ?? 0);
        $sakit = (int) ($rows['sakit'] ?? 0);
        $izin = (int) ($rows['izin'] ?? 0);
        $alpa = (int) ($rows['alpa'] ?? 0);
        $selesai = (int) ($rows['selesai'] ?? 0);

        $persentase = $totalMurid > 0 ? (int) round(($hadir / $totalMurid) * 100) : 0;

        return [
            'totalMurid' => $totalMurid,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'selesai' => $selesai,
            'persentase' => $persentase,
        ];
    }

    private function getTidakHadir()
    {
        return $this->absenBaseQuery()
            ->with([
                'murid:id,nama,rombel_id,nipd,nisn,image_path',
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
                fn ($q) => $q->whereRaw('LOWER(status) = ?', [strtolower($this->filterStatus)]),
                fn ($q) => $q->whereIn(DB::raw('LOWER(status)'), $this->statusOptions),
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

    public function exportExcel()
    {
        if ($this->isWaliKelas && $this->waliRombelIds !== null) {
            if (! $this->exportRombelId || ! in_array($this->exportRombelId, $this->waliRombelIds, true)) {
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

    public function render()
    {
        return view('laporan::livewire.laporan.rekap-absen.murid.rekap', [
            'listAbsen' => $this->getTidakHadir(),
            'statistik' => $this->getStatistik(),
            'chartRekap' => $this->getRekapKehadiranBulanan(),
        ]);
    }
}
