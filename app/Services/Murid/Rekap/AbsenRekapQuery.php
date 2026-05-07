<?php

namespace App\Services\Murid\Rekap;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AbsenRekapQuery
{
    public function __construct(
        private readonly ?string $bulan,
        private readonly ?int $rombelId,
    ) {
    }

    public function base(): Builder
    {
        return DB::table('absen_murid')
            ->join('murid', 'murid.id', '=', 'absen_murid.murid_id')
            ->join('rombel', 'rombel.id', '=', 'murid.rombel_id')
            ->leftJoin('tingkat', 'tingkat.id', '=', 'rombel.tingkat_id')
            ->leftJoin('jurusan', 'jurusan.id', '=', 'rombel.jurusan_id')
            ->leftJoin('indeks', 'indeks.id', '=', 'rombel.indeks_id')
            ->when($this->rombelId, fn($q) => $q->where('rombel.id', $this->rombelId))
            ->when($this->bulan, function ($q) {
                $start = Carbon::createFromFormat('Y-m', $this->bulan)->startOfMonth();
                $end = (clone $start)->endOfMonth();
                $q->whereBetween('absen_murid.tanggal', [$start->toDateString(), $end->toDateString()]);
            });
    }

    public function rombelNamaExpr(): string
    {
        return $this->driver() === 'sqlite'
            ? "trim(COALESCE(tingkat.nama, '') || ' ' || COALESCE(jurusan.nama, '') || ' ' || COALESCE(indeks.nama, ''))"
            : "TRIM(CONCAT_WS(' ', tingkat.nama, jurusan.nama, indeks.nama))";
    }

    public function dateExpr(): string
    {
        return $this->driver() === 'sqlite'
            ? "date(tanggal)"
            : 'DATE(tanggal)';
    }

    public function yearExpr(): string
    {
        return $this->driver() === 'sqlite'
            ? "CAST(strftime('%Y', tanggal) AS INTEGER)"
            : 'YEAR(tanggal)';
    }

    public function monthExpr(): string
    {
        return $this->driver() === 'sqlite'
            ? "CAST(strftime('%m', tanggal) AS INTEGER)"
            : 'MONTH(tanggal)';
    }

    public function weekExpr(): string
    {
        return $this->driver() === 'sqlite'
            ? "CAST(strftime('%W', tanggal) AS INTEGER)"
            : 'WEEK(tanggal, 3)';
    }

    private function driver(): string
    {
        return DB::connection()->getDriverName();
    }
}
