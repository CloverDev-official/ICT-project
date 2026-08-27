<?php

namespace Modules\Laporan\Services\StudentAttendance;

use App\Enums\AttendanceStatus;
use App\Models\Murid\AbsenMurid;
use App\Models\Murid\Murid;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StudentAttendanceHistoryQuery
{
    public function __construct(
        private readonly StudentAttendanceAccessScope $accessScope,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters, User $user): LengthAwarePaginator
    {
        $query = $this->filteredQuery($filters, $user)
            ->select([
                'absen_murid.id',
                'absen_murid.murid_id',
                'absen_murid.status',
                'absen_murid.tanggal',
            ])
            ->with([
                'murid:id,uuid,nama,nipd,nisn,rombel_id',
                'murid.rombel:id,tingkat_id,jurusan_id,indeks_id',
                'murid.rombel.tingkat:id,nama',
                'murid.rombel.jurusan:id,nama',
                'murid.rombel.indeks:id,nama',
            ]);

        $this->applySorting($query, $filters);

        $perPage = (int) ($filters['per_page'] ?? 20);
        $paginator = $query->paginate($perPage);

        return $paginator->appends(array_filter(
            $filters,
            static fn ($value): bool => $value !== null && $value !== '',
        ));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{rombels: array<int, array{rombel: string, statuses: array<int, array{code: string, label: string, total: int}>, total: int}>, grand_total: int}
     */
    public function summary(array $filters, User $user): array
    {
        $rows = $this->filteredQuery($filters, $user)
            ->leftJoin('murid as summary_murid', 'summary_murid.id', '=', 'absen_murid.murid_id')
            ->leftJoin('rombel as summary_rombel', 'summary_rombel.id', '=', 'summary_murid.rombel_id')
            ->leftJoin('tingkat as summary_tingkat', 'summary_tingkat.id', '=', 'summary_rombel.tingkat_id')
            ->leftJoin('jurusan as summary_jurusan', 'summary_jurusan.id', '=', 'summary_rombel.jurusan_id')
            ->leftJoin('indeks as summary_indeks', 'summary_indeks.id', '=', 'summary_rombel.indeks_id')
            ->select([
                'summary_rombel.id as rombel_id',
                'summary_tingkat.nama as tingkat_nama',
                'summary_jurusan.nama as jurusan_nama',
                'summary_indeks.nama as indeks_nama',
            ])
            ->selectRaw('LOWER(absen_murid.status) as status_code, COUNT(*) as total')
            ->groupBy([
                'summary_rombel.id',
                'summary_tingkat.nama',
                'summary_jurusan.nama',
                'summary_indeks.nama',
                DB::raw('LOWER(absen_murid.status)'),
            ])
            ->orderBy('summary_tingkat.nama')
            ->orderBy('summary_jurusan.nama')
            ->orderBy('summary_indeks.nama')
            ->get();

        $grandTotal = 0;
        $rombels = [];

        foreach ($rows as $row) {
            $key = $row->rombel_id === null ? 'tanpa-rombel' : (string) $row->rombel_id;

            if (! isset($rombels[$key])) {
                $rombels[$key] = [
                    'rombel' => $this->rombelName(
                        $row->tingkat_nama,
                        $row->jurusan_nama,
                        $row->indeks_nama,
                    ),
                    'statuses' => $this->emptyStatusTotals(),
                    'total' => 0,
                ];
            }

            $total = (int) $row->total;
            $statusCode = (string) $row->status_code;
            $rombels[$key]['statuses'][$statusCode]['total'] = $total;
            $rombels[$key]['total'] += $total;
            $grandTotal += $total;
        }

        $rombels = array_map(function (array $rombel): array {
            $rombel['statuses'] = array_values($rombel['statuses']);

            return $rombel;
        }, array_values($rombels));

        return [
            'rombels' => $rombels,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters, User $user): Builder
    {
        $query = AbsenMurid::query()
            ->whereIn(
                DB::raw('LOWER(absen_murid.status)'),
                AttendanceStatus::codes(),
            )
            ->whereHas('murid', fn (Builder $studentQuery) => $studentQuery
                ->whereNull('murid.deleted_at')
                ->aktif());

        $accessibleRombelIds = $this->accessScope->accessibleRombelIds($user);

        $this->applyRombelFilters($query, $filters, $accessibleRombelIds);

        if (isset($filters['murid_id'])) {
            $query->where('absen_murid.murid_id', (int) $filters['murid_id']);
        }

        if (isset($filters['search'])) {
            $this->applySearch($query, (string) $filters['search']);
        }

        if (isset($filters['date_from'])) {
            $query->where('absen_murid.tanggal', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('absen_murid.tanggal', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<int, int>|null  $accessibleRombelIds
     */
    private function applyRombelFilters(
        Builder $query,
        array $filters,
        ?array $accessibleRombelIds,
    ): void {
        $hasNameFilter = isset($filters['tingkat'])
            || isset($filters['jurusan'])
            || isset($filters['indeks']);

        if (! $hasNameFilter && $accessibleRombelIds === null) {
            return;
        }

        $query->whereHas('murid.rombel', function (Builder $rombelQuery) use ($filters, $accessibleRombelIds) {
            if ($accessibleRombelIds !== null) {
                $rombelQuery->whereIn('rombel.id', $accessibleRombelIds);
            }

            foreach ([
                'tingkat' => ['relation' => 'tingkat', 'table' => 'tingkat'],
                'jurusan' => ['relation' => 'jurusan', 'table' => 'jurusan'],
                'indeks' => ['relation' => 'indeks', 'table' => 'indeks'],
            ] as $filter => $reference) {
                if (! isset($filters[$filter])) {
                    continue;
                }

                $rombelQuery->whereHas($reference['relation'], function (Builder $referenceQuery) use ($filters, $filter, $reference) {
                    $referenceQuery->whereRaw(
                        "LOWER({$reference['table']}.nama) = ?",
                        [mb_strtolower((string) $filters[$filter])],
                    );
                });
            }
        });
    }

    private function applySearch(Builder $query, string $search): void
    {
        $escapedSearch = str_replace(
            ['!', '%', '_'],
            ['!!', '!%', '!_'],
            mb_strtolower($search),
        );
        $pattern = "%{$escapedSearch}%";

        $query->whereHas('murid', function (Builder $studentQuery) use ($pattern) {
            $studentQuery->where(function (Builder $searchQuery) use ($pattern) {
                $searchQuery
                    ->whereRaw("LOWER(murid.nama) LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("LOWER(murid.nipd) LIKE ? ESCAPE '!'", [$pattern])
                    ->orWhereRaw("LOWER(murid.nisn) LIKE ? ESCAPE '!'", [$pattern]);
            });
        });
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applySorting(Builder $query, array $filters): void
    {
        $sortBy = (string) ($filters['sort_by'] ?? 'tanggal');
        $direction = ($filters['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'nama') {
            $query->orderBy(
                Murid::query()
                    ->select('nama')
                    ->whereColumn('murid.id', 'absen_murid.murid_id')
                    ->limit(1),
                $direction,
            );
        } else {
            $column = match ($sortBy) {
                'status' => 'absen_murid.status',
                'id' => 'absen_murid.id',
                default => 'absen_murid.tanggal',
            };

            $query->orderBy($column, $direction);
        }

        if ($sortBy !== 'id') {
            $query->orderBy('absen_murid.id', $direction);
        }
    }

    /** @return array<string, array{code: string, label: string, total: int}> */
    private function emptyStatusTotals(): array
    {
        $statuses = [];

        foreach (AttendanceStatus::cases() as $status) {
            $statuses[$status->lowercase()] = [
                'code' => $status->lowercase(),
                'label' => $status->value,
                'total' => 0,
            ];
        }

        return $statuses;
    }

    private function rombelName(?string $tingkat, ?string $jurusan, ?string $indeks): string
    {
        $name = trim(implode(' ', array_filter([$tingkat, $jurusan, $indeks])));

        return $name !== '' ? $name : 'Tanpa Rombel';
    }
}
