<?php

namespace Modules\General\Livewire\Dashboard;

use App\Models\Admin;
use App\Models\Guru\AbsenGuru;
use App\Models\Guru\Guru;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    private const STATUS_OPTIONS = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'];

    private const STATUS_COLORS = [
        'Hadir' => '#22c55e',
        'Terlambat' => '#f97316',
        'Izin' => '#eab308',
        'Sakit' => '#3b82f6',
        'Alpa' => '#ef4444',
    ];

    public bool $isWaliKelas = false;

    public array $waliRombelIds = [];

    public ?int $waliGuruId = null;

    public $waliRombelList = [];

    public ?int $selectedRombelId = null;

    public array $dashboardData = [];

    public function mount(): void
    {
        Carbon::setLocale('id');

        $this->applyWaliKelasContext();
        $this->loadRombelList();
        $this->refreshDashboardData();
    }

    public function setRombelFilter(?int $rombelId): void
    {
        if ($this->isWaliKelas && ! $this->isAllowedRombel($rombelId)) {
            return;
        }

        $this->selectedRombelId = $rombelId;
        $this->refreshDashboardData();
        $this->dispatchDashboardData();
    }

    private function applyWaliKelasContext(): void
    {
        [$isWaliKelas, $waliRombelIds, $waliGuruId] = $this->getWaliKelasContext();

        $this->isWaliKelas = $isWaliKelas;
        $this->waliRombelIds = $waliRombelIds ?? [];
        $this->waliGuruId = $waliGuruId;
    }

    private function isAllowedRombel(?int $rombelId): bool
    {
        if ($rombelId === null) {
            return ! $this->isWaliKelas;
        }

        return in_array($rombelId, $this->waliRombelIds, true);
    }

    private function loadRombelList(): void
    {
        $query = Rombel::query()
            ->with([
                'tingkat:id,nama',
                'jurusan:id,nama',
                'indeks:id,nama',
            ])
            ->orderBy('tingkat_id')
            ->orderBy('jurusan_id')
            ->orderBy('indeks_id');

        if ($this->isWaliKelas) {
            $query->whereIn('id', $this->waliRombelIds);
        }

        $this->waliRombelList = $query->get();

        if ($this->isWaliKelas && ! $this->isAllowedRombel($this->selectedRombelId)) {
            $this->selectedRombelId = $this->waliRombelList->first()?->id;
        }
    }

    private function refreshDashboardData(): void
    {
        $this->dashboardData = $this->getDashboardData();
    }

    private function dispatchDashboardData(): void
    {
        $this->dispatch('dashboard-data-updated', dashboardData: $this->dashboardData);
    }

    private function getStatCards(): array
    {
        $stats = $this->getStats();

        return [
            [
                'label' => 'Jumlah Murid',
                'value' => $stats['murid'],
                'icon' => 'solar:users-group-rounded-bold',
                'color' => 'bg-blue-100 text-blue-main',
            ],
            [
                'label' => 'Jumlah Guru',
                'value' => $stats['guru'],
                'icon' => 'solar:user-id-bold',
                'color' => 'bg-emerald-100 text-emerald-600',
            ],
            [
                'label' => 'Kelas & Jurusan',
                'value' => sprintf('%d / %d', $stats['kelas'], $stats['jurusan']),
                'icon' => 'solar:buildings-2-bold',
                'color' => 'bg-amber-100 text-amber-600',
            ],
            [
                'label' => 'Jumlah Petugas',
                'value' => $stats['petugas'],
                'icon' => 'solar:settings-bold',
                'color' => 'bg-rose-100 text-rose-600',
            ],
        ];
    }

    private function getStats(): array
    {
        if ($this->isWaliKelas) {
            $rombelQuery = Rombel::query()->whereIn('id', $this->waliRombelIds);

            return [
                'murid' => Murid::query()
                    ->aktif()
                    ->whereIn('rombel_id', $this->waliRombelIds)
                    ->count(),
                'guru' => (clone $rombelQuery)
                    ->whereNotNull('wali_guru_id')
                    ->distinct('wali_guru_id')
                    ->count('wali_guru_id'),
                'kelas' => (clone $rombelQuery)->count(),
                'jurusan' => (clone $rombelQuery)
                    ->distinct('jurusan_id')
                    ->count('jurusan_id'),
                'petugas' => 0,
            ];
        }

        return [
            'murid' => Murid::query()->aktif()->count(),
            'guru' => Guru::count(),
            'kelas' => Rombel::count(),
            'jurusan' => Jurusan::count(),
            'petugas' => Admin::count(),
        ];
    }

    private function getDashboardData(): array
    {
        $today = now()->toDateString();
        $muridQuery = $this->absenMuridBaseQuery();
        $guruQuery = $this->absenGuruBaseQuery();

        return [
            'murid' => $this->buildDonutData(
                $this->getStatusCounts(clone $muridQuery, $today)
            ),
            'guru' => $this->buildDonutData(
                $this->getStatusCounts(clone $guruQuery, $today)
            ),
            'murid7' => $this->getSevenDayAttendance(clone $muridQuery),
            'guru7' => $this->getSevenDayAttendance(clone $guruQuery),
        ];
    }

    private function absenMuridBaseQuery(): Builder
    {
        return AbsenMurid::query()
            ->when($this->selectedRombelId, function (Builder $query) {
                $query->whereHas('murid.rombel', function (Builder $query) {
                    $query->whereKey($this->selectedRombelId);
                });
            })
            ->when(
                $this->isWaliKelas,
                function (Builder $query) {
                    $query->whereHas('murid', function (Builder $query) {
                        $query->whereIn('rombel_id', $this->waliRombelIds);
                    });
                }
            );
    }

    private function absenGuruBaseQuery(): Builder
    {
        return AbsenGuru::query()
            ->when(
                $this->isWaliKelas && $this->waliGuruId,
                fn (Builder $query) => $query->where('guru_id', $this->waliGuruId)
            )
            ->when(
                $this->isWaliKelas && ! $this->waliGuruId,
                fn (Builder $query) => $query->whereRaw('1 = 0')
            );
    }

    private function getStatusCounts(Builder $query, string $date): array
    {
        $rows = $query
            ->selectRaw('status, COUNT(*) as total')
            ->whereDate('tanggal', $date)
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [];

        foreach (self::STATUS_OPTIONS as $status) {
            $counts[$status] = (int) ($rows[$status] ?? 0);
        }

        return $counts;
    }

    private function buildDonutData(array $counts): array
    {
        $total = array_sum($counts);

        return [
            'total' => $total,
            'series' => collect($counts)
                ->map(fn (int $value, string $status) => [
                    'name' => $status,
                    'value' => $value,
                    'color' => self::STATUS_COLORS[$status] ?? '#94a3b8',
                ])
                ->values()
                ->all(),
        ];
    }

    private function getSevenDayAttendance(Builder $query): array
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays(6);
        $dateExpr = $this->dateExpression('tanggal');

        $rows = $query
            ->selectRaw("{$dateExpr} as tanggal, COUNT(*) as total")
            ->whereBetween('tanggal', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->where('status', 'Hadir')
            ->groupBy(DB::raw($dateExpr))
            ->pluck('total', 'tanggal');

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $end->copy()->subDays($i);

            $labels[] = $date->translatedFormat('d M');
            $values[] = (int) ($rows[$date->toDateString()] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function dateExpression(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "date({$column})"
            : "DATE({$column})";
    }

    private function getWaliKelasContext(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [false, null, null];
        }

        $roleSlug = Str::slug($user->role?->name ?? '');
        $isWaliKelas = in_array($roleSlug, ['wali-kelas', 'wali-murid'], true);

        if (! $isWaliKelas) {
            return [false, null, null];
        }

        $guru = Guru::query()
            ->where('user_id', $user->id)
            ->first();

        if (! $guru) {
            return [true, [], null];
        }

        $rombelIds = Rombel::query()
            ->where('wali_guru_id', $guru->id)
            ->pluck('id')
            ->all();

        return [true, $rombelIds, $guru->id];
    }

    public function render()
    {
        return view('general::livewire.dashboard.index', [
            'dateNow' => now()->translatedFormat('d F Y'),
            'statCards' => $this->getStatCards(),
            'dashboardData' => $this->dashboardData,
            'isWaliKelas' => $this->isWaliKelas,
            'waliRombelList' => $this->waliRombelList,
            'selectedRombelId' => $this->selectedRombelId,
        ]);
    }
}
