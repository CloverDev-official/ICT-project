<?php

namespace App\Livewire\Manajemen;

use App\Helpers\ToastMagic;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Livewire\Component;

class TahunAjaran extends Component
{
    public string $currentAcademicYearLabel = '';

    public string $nextAcademicYearLabel = '';

    public ?string $lastProcessedAt = null;

    public array $levelStats = [];

    public function mount(): void
    {
        $this->refreshSummary();
    }

    private function getAcademicYearStart(?Carbon $date = null): int
    {
        $date ??= now();

        return $date->month >= 7 ? (int) $date->year : (int) $date->year - 1;
    }

    private function formatAcademicYear(int $startYear): string
    {
        return $startYear . ' / ' . ($startYear + 1);
    }

    private function getTingkatOrder(string $nama): int
    {
        return match (strtoupper(trim($nama))) {
            'X' => 1,
            'XI' => 2,
            'XII' => 3,
            default => 99,
        };
    }

    private function getNextTingkat(?string $nama): ?string
    {
        return match (strtoupper(trim((string) $nama))) {
            'X'   => 'XI',
            'XI'  => 'XII',
            'XII' => 'XIII',
            default => null,
        };
    }

    private function getTingkatModel(string $nama): ?Tingkat
    {
        return Tingkat::query()->where('nama', strtoupper(trim($nama)))->first();
    }

    private function getTargetTahunMasukForTingkat(string $tingkat, int $academicYearStart): int
    {
        return match (strtoupper(trim($tingkat))) {
            'X' => $academicYearStart,
            'XI' => $academicYearStart - 1,
            'XII' => $academicYearStart - 2,
            default => $academicYearStart,
        };
    }

    private function refreshSummary(): void
    {
        $currentStart = $this->getAcademicYearStart();

        $this->currentAcademicYearLabel = $this->formatAcademicYear($currentStart);
        $this->nextAcademicYearLabel = $this->formatAcademicYear($currentStart + 1);
        $this->lastProcessedAt = Setting::query()
            ->where('key', 'tahun_ajaran_terakhir_diproses')
            ->value('value');

        $this->levelStats = collect(['X', 'XI', 'XII'])
            ->mapWithKeys(function ($level) {
                $count = Murid::query()
                    ->aktif()
                    ->whereHas('rombel.tingkat', function ($query) use ($level) {
                        $query->where('nama', $level);
                    })
                    ->count();

                return [$level => $count];
            })
            ->all();

        $this->levelStats['Lulus'] = $this->hasMuridStatusColumn()
            ? Murid::query()->where('status', 'lulus')->count()
            : Murid::query()->whereNull('rombel_id')->count();
    }

    private function hasMuridStatusColumn(): bool
    {
        static $hasStatusColumn = null;

        return $hasStatusColumn ??= Schema::hasColumn('murid', 'status');
    }

    #[On('proses-kenaikan-kelas')]
    public function prosesKenaikanKelas(): void
    {
        $currentStart = $this->getAcademicYearStart();
        $nextStart = $currentStart + 1;

        $result = DB::transaction(function () use ($nextStart) {
            $rombels = Rombel::query()
                ->with([
                    'tingkat',
                    'jurusan',
                    'indeks',
                    'murid' => fn ($query) => $query->aktif(),
                ])
                ->get()
                ->sortByDesc(fn ($rombel) => $this->getTingkatOrder($rombel->tingkat?->nama ?? ''))
                ->values();

            $promotedCount = 0;
            $graduatedCount = 0;

            foreach ($rombels as $rombel) {
                $nextTingkat = $this->getNextTingkat($rombel->tingkat?->nama ?? '');
                $activeStudentCount = $rombel->murid->count();

                if (!$nextTingkat) {
                    $graduatePayload = [
                        'rombel_id' => null,
                    ];

                    if ($this->hasMuridStatusColumn()) {
                        $graduatePayload['status'] = 'lulus';
                    }

                    Murid::query()
                        ->aktif()
                        ->where('rombel_id', $rombel->id)
                        ->update($graduatePayload);

                    $graduatedCount += $activeStudentCount;
                    continue;
                }

                $nextTingkatModel = Tingkat::firstOrCreate(
                    [
                        'nama' => $nextTingkat,
                    ]
                );

                $targetRombel = Rombel::firstOrCreate(
                    [
                        'tingkat_id' => $nextTingkatModel->id,
                        'jurusan_id' => $rombel->jurusan_id,
                        'indeks_id'  => $rombel->indeks_id, // Bisa null
                    ],
                    [
                        'tahun_masuk' => $this->getTargetTahunMasukForTingkat(
                            $nextTingkat,
                            $nextStart
                        ),
                    ],
                );

                $targetTahunMasuk = $this->getTargetTahunMasukForTingkat(
                    $nextTingkat,
                    $nextStart
                );

                if (
                    !$targetRombel->tahun_masuk ||
                    (int) $targetRombel->tahun_masuk !== $targetTahunMasuk
                ) {
                    $targetRombel->update([
                        'tahun_masuk' => $targetTahunMasuk,
                    ]);
                }

                Murid::query()
                    ->aktif()
                    ->where('rombel_id', $rombel->id)
                    ->update([
                        'rombel_id' => $targetRombel->id,
                    ]);

                $promotedCount += $activeStudentCount;
            }

            Setting::updateOrCreate(
                [
                    'key' => 'tahun_ajaran_terakhir_diproses',
                ],
                [
                    'value' => now()->format('Y-m-d H:i:s'),
                ],
            );

            Setting::updateOrCreate(
                [
                    'key' => 'tahun_ajaran_aktif',
                ],
                [
                    'value' => $this->formatAcademicYear($nextStart),
                ],
            );

            return [
                $promotedCount,
                $graduatedCount,
            ];
        });

        ToastMagic::success(
            'Kenaikan kelas selesai',
            'Berhasil dipromosikan: ' . $result[0] . ' murid. Lulus: ' . $result[1] . ' murid.'
        );

        $this->refreshSummary();
    }

    public function render()
    {
        return view('livewire.manajemen.tahun-ajaran', [
            'currentAcademicYearLabel' => $this->currentAcademicYearLabel,
            'nextAcademicYearLabel' => $this->nextAcademicYearLabel,
            'lastProcessedAt' => $this->lastProcessedAt,
            'levelStats' => $this->levelStats,
        ]);
    }
}
