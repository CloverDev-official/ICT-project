<?php

namespace App\Services\Rombel;

use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Eloquent\Builder;

class RombelFilterService
{
    public function applyRombelFilters(
        Builder $query,
        ?int $tingkatId,
        ?int $jurusanId,
        ?int $indeksId,
        ?array $waliRombelIds,
    ): Builder {
        if ($waliRombelIds !== null) {
            $query->whereIn('id', $waliRombelIds);
        }

        return $query
            ->when($tingkatId, fn ($q) => $q->where('tingkat_id', $tingkatId))
            ->when($jurusanId, fn ($q) => $q->where('jurusan_id', $jurusanId))
            ->when($indeksId, fn ($q) => $q->where('indeks_id', $indeksId));
    }

    public function applyRombelFiltersToRelation(
        Builder $query,
        string $relationPath,
        ?int $tingkatId,
        ?int $jurusanId,
        ?int $indeksId,
        ?array $waliRombelIds,
    ): Builder {
        $hasFilter = $tingkatId || $jurusanId || $indeksId || $waliRombelIds !== null;

        if (!$hasFilter) {
            return $query;
        }

        return $query->whereHas($relationPath, function ($q) use ($tingkatId, $jurusanId, $indeksId, $waliRombelIds) {
            $this->applyRombelFilters($q, $tingkatId, $jurusanId, $indeksId, $waliRombelIds);
        });
    }

    public function getRombelList(
        ?int $tingkatId,
        ?int $jurusanId,
        ?int $indeksId,
        ?array $waliRombelIds,
    ) {
        return Rombel::query()
            ->tap(fn ($q) => $this->applyRombelFilters($q, $tingkatId, $jurusanId, $indeksId, $waliRombelIds))
            ->with(['tingkat:id,nama', 'jurusan:id,nama', 'indeks:id,nama'])
            ->get();
    }

    public function getAvailableJurusan(
        ?int $tingkatId,
        ?int $jurusanId,
        ?int $indeksId,
        ?array $waliRombelIds,
    ) {
        return Jurusan::query()
            ->whereIn('id', $this->rombelSubquery(
                'jurusan_id',
                $tingkatId,
                null, // jangan filter jurusan sendiri
                $indeksId,
                $waliRombelIds
            ))
            ->orderBy('nama')
            ->orderBy('id')
            ->get(['id', 'nama']);
    }

    public function getAvailableIndeks(
        ?int $tingkatId,
        ?int $jurusanId,
        ?int $indeksId,
        ?array $waliRombelIds,
    ) {
        return Indeks::query()
            ->whereIn('id', $this->rombelSubquery(
                'indeks_id',
                $tingkatId,
                $jurusanId,
                null, // jangan filter indeks sendiri
                $waliRombelIds
            ))
            ->orderBy('nama')
            ->orderBy('id')
            ->get(['id', 'nama']);
    }

    private function rombelSubquery(
        string $column,
        ?int $tingkatId,
        ?int $jurusanId,
        ?int $indeksId,
        ?array $waliRombelIds,
    ): Builder {
        return Rombel::query()
            ->select($column)
            ->distinct()
            ->tap(fn ($q) => $this->applyRombelFilters($q, $tingkatId, $jurusanId, $indeksId, $waliRombelIds));
    }
}
