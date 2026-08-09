<?php

namespace Modules\ScanQR\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AutoAlpaMuridService
{
    public function syncForRombels(iterable $rombelIds, Carbon|string $tanggal, ?Carbon $now = null): int
    {
        $date = $tanggal instanceof Carbon
            ? $tanggal->copy()
            : Carbon::parse($tanggal);

        if ($date->isFuture()) {
            return 0;
        }

        $now ??= $date->isToday() ? now() : $date->copy()->endOfDay();
        $totalCreated = 0;
        $jadwalService = app(JadwalAbsensiService::class);

        foreach ($jadwalService->forRombels($rombelIds, $date) as $rombelId => $jadwal) {
            if (!$jadwalService->bolehScan($jadwal)) {
                continue;
            }

            $totalCreated += $this->syncForRombel(
                (int) $rombelId,
                $date->toDateString(),
                $jadwal,
                $now,
            );
        }

        return $totalCreated;
    }

    public function syncForRombel(int $rombelId, string $tanggal, array $jadwal, ?Carbon $now = null): int
    {
        $now ??= now();
        $jamMasuk = $this->normalizeTime($jadwal['jam_masuk'] ?? null);

        if (!$jamMasuk) {
            return 0;
        }

        $autoAlpaStartsAt = Carbon::parse("{$tanggal} {$jamMasuk}")->addMinute();

        if ($now->lt($autoAlpaStartsAt)) {
            return 0;
        }

        $timestamp = now()->toDateTimeString();
        $keterangan = $this->autoAlpaKeterangan($jadwal);

        $query = $this->missingAbsenQuery($rombelId, $tanggal);
        $total = (clone $query)->count('murid.id');

        if ($total === 0) {
            return 0;
        }

        $inserted = DB::table('absen_murid')->insertUsing(
            [
                'murid_id',
                'tanggal',
                'status',
                'waktu_masuk',
                'waktu_keluar',
                'keterangan',
                'created_at',
                'updated_at',
            ],
            $query->selectRaw(
                'murid.id, ? as tanggal, ? as status, null as waktu_masuk, null as waktu_keluar, ? as keterangan, ? as created_at, ? as updated_at',
                [$tanggal, 'Alpa', $keterangan, $timestamp, $timestamp],
            ),
        );

        return is_int($inserted) ? $inserted : $total;
    }

    private function missingAbsenQuery(int $rombelId, string $tanggal)
    {
        $query = DB::table('murid')
            ->leftJoin('absen_murid', function ($join) use ($tanggal) {
                $join->on('absen_murid.murid_id', '=', 'murid.id')
                    ->where('absen_murid.tanggal', $tanggal)
                    ->whereNull('absen_murid.deleted_at');
            })
            ->where('murid.rombel_id', $rombelId)
            ->whereNull('murid.deleted_at')
            ->whereNull('absen_murid.id');

        if ($this->muridHasStatusColumn()) {
            return $query->where('murid.status', 'aktif');
        }

        return $query->whereNotNull('murid.rombel_id');
    }

    private function muridHasStatusColumn(): bool
    {
        static $hasStatusColumn = null;

        return $hasStatusColumn ??= Schema::hasColumn('murid', 'status');
    }

    private function autoAlpaKeterangan(array $jadwal): string
    {
        $parts = array_filter([
            'Otomatis alpa setelah melewati jam masuk',
            $jadwal['label'] ?? null,
            $jadwal['nama_acara'] ?? null,
            $jadwal['keterangan'] ?? null,
        ]);

        return mb_substr(implode(' - ', $parts), 0, 191);
    }

    private function normalizeTime(?string $time): ?string
    {
        if (!$time) {
            return null;
        }

        return strlen($time) === 5 ? $time . ':00' : substr($time, 0, 8);
    }
}
