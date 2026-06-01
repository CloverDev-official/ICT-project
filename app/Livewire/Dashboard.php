<?php

namespace App\Livewire;

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
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public bool $isWaliKelas = false;

    public array $waliRombelIds = [];

    public ?int $waliGuruId = null;

    public $waliRombelList = [];

    public ?int $selectedRombelId = null;

    public function render()
    {   
        Carbon::setLocale('id');
        $dateNow = Carbon::now()->translatedFormat('d F Y');
        $today = now()->toDateString();

        [$isWaliKelas, $waliRombelIds, $waliGuruId] = $this->getWaliKelasContext();
        $this->isWaliKelas = $isWaliKelas;
        $this->waliRombelIds = $waliRombelIds ?? [];
        $this->waliGuruId = $waliGuruId;

        if ($isWaliKelas) {
            $this->waliRombelList = Rombel::query()
                ->with(['tingkat:id,nama', 'jurusan:id,nama', 'indeks:id,nama'])
                ->whereIn('id', $this->waliRombelIds)
                ->orderBy('tingkat_id')
                ->orderBy('jurusan_id')
                ->orderBy('indeks_id')
                ->get();

            if ($this->selectedRombelId === null && $this->waliRombelList->isNotEmpty()) {
                $this->selectedRombelId = $this->waliRombelList->first()->id;
            }

            if ($this->selectedRombelId !== null && !in_array($this->selectedRombelId, $this->waliRombelIds, true)) {
                $this->selectedRombelId = $this->waliRombelList->first()?->id;
            }
        } else {
            $this->waliRombelList = Rombel::query()
                ->with(['tingkat:id,nama', 'jurusan:id,nama', 'indeks:id,nama'])
                ->orderBy('tingkat_id')
                ->orderBy('jurusan_id')
                ->orderBy('indeks_id')
                ->get();
        }

        if ($isWaliKelas && $waliRombelIds !== null) {
            $rombelQuery = Rombel::query()->whereIn('id', $waliRombelIds);

            $stats = [
                'murid' => Murid::query()->whereIn('rombel_id', $waliRombelIds)->count(),
                'guru' => $rombelQuery->whereNotNull('wali_guru_id')
                    ->distinct('wali_guru_id')
                    ->count('wali_guru_id'),
                'kelas' => $rombelQuery->count(),
                'jurusan' => $rombelQuery->distinct('jurusan_id')->count('jurusan_id'),
                'petugas' => 0,
            ];
        } else {
            $stats = [
                'murid' => Murid::query()->count(),
                'guru' => Guru::query()->count(),
                'kelas' => Rombel::query()->count(),
                'jurusan' => Jurusan::query()->count(),
                'petugas' => Admin::query()->count(),
            ];
        }

        $dashboardData = $this->buildDashboardData();

        $statCards = [
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

        return view('livewire.dashboard', [
            'dateNow' => $dateNow,
            'statCards' => $statCards,
            'dashboardData' => $dashboardData,
            'isWaliKelas' => $this->isWaliKelas,
            'waliRombelList' => $this->waliRombelList,
            'selectedRombelId' => $this->selectedRombelId,
        ]);
    }

    public function setRombelFilter(?int $rombelId): void
    {
        if ($this->isWaliKelas) {
            $allowed = collect($this->waliRombelIds)->contains($rombelId);

            if ($rombelId !== null && !$allowed) {
                return;
            }
        }

        $this->selectedRombelId = $rombelId;

        $this->dispatch('dashboard-data-updated', dashboardData: $this->buildDashboardData());
    }

    private function buildDashboardData(): array
    {
        $today = now()->toDateString();

        $muridQuery = AbsenMurid::query();
        $guruQuery = AbsenGuru::query();

        if ($this->selectedRombelId) {
            $muridQuery = $muridQuery->whereHas('murid.rombel', function ($q) {
                $q->whereKey($this->selectedRombelId);
            });
        }

        if ($this->isWaliKelas && $this->waliGuruId) {
            $guruQuery = $guruQuery->where('guru_id', $this->waliGuruId);
        } elseif ($this->isWaliKelas) {
            $guruQuery = $guruQuery->whereRaw('1 = 0');
        }

        $muridStatus = $this->statusCounts($muridQuery, $today);
        $guruStatus = $this->statusCounts($guruQuery, $today);

        return [
            'murid' => $this->buildDonutData($muridStatus),
            'guru' => $this->buildDonutData($guruStatus),
            'murid7' => $this->buildSevenDayData($muridQuery),
            'guru7' => $this->buildSevenDayData($guruQuery),
        ];
    }

    private function statusCounts(Builder $query, string $date): array
    {
        $statuses = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'];

        $rows = $query
            ->selectRaw('status, COUNT(*) as total')
            ->whereDate('tanggal', $date)
            ->groupBy('status')
            ->pluck('total', 'status');

        $result = [];

        foreach ($statuses as $status) {
            $result[$status] = (int) ($rows[$status] ?? 0);
        }

        return $result;
    }

    private function buildDonutData(array $counts): array
    {
        $colors = [
            'Hadir' => '#22c55e',
            'Terlambat' => '#f97316',
            'Izin' => '#eab308',
            'Sakit' => '#3b82f6',
            'Alpa' => '#ef4444',
        ];

        $series = [];
        $total = 0;

        foreach ($counts as $status => $value) {
            $series[] = [
                'name' => $status,
                'value' => $value,
                'color' => $colors[$status] ?? '#94a3b8',
            ];
            $total += $value;
        }

        return [
            'total' => $total,
            'series' => $series,
        ];
    }

    private function buildSevenDayData(Builder $query): array
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays(6);
        $driver = DB::connection()->getDriverName();
        $dateExpr = $driver === 'sqlite' ? "date(tanggal)" : 'DATE(tanggal)';

        $rows = $query
            ->selectRaw("{$dateExpr} as tanggal, COUNT(*) as total")
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->where('status', 'Hadir')
            ->groupBy(DB::raw($dateExpr))
            ->pluck('total', 'tanggal');

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $end->copy()->subDays($i);
            $key = $date->toDateString();
            $labels[] = $date->format('d M');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getWaliKelasContext(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [false, null, null];
        }

        $roleSlug = Str::slug($user->role?->name ?? '');
        $isWaliKelas = in_array($roleSlug, ['wali-kelas', 'wali-murid'], true);

        if (!$isWaliKelas) {
            return [false, null, null];
        }

        $guru = Guru::query()->where('user_id', $user->id)->first();
        if (!$guru) {
            return [true, [], null];
        }

        $rombelIds = Rombel::query()
            ->where('wali_guru_id', $guru->id)
            ->pluck('id')
            ->all();

        return [true, $rombelIds, $guru->id];
    }
}
