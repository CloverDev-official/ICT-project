<?php

namespace App\Console\Commands;

use App\Models\Murid\Rombel\Rombel;
use App\Services\Absensi\AutoAlpaMuridService;
use App\Services\Absensi\JadwalAbsensiService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoAlpaMurid extends Command
{
    protected $signature = 'absensi:auto-alpa-murid {--date=} {--rombel_id=}';

    protected $description = 'Tandai murid aktif yang belum scan masuk sebagai Alpa setelah jam masuk lewat.';

    public function handle(
        AutoAlpaMuridService $autoAlpaService,
        JadwalAbsensiService $jadwalService,
    ): int {
        $date = Carbon::parse($this->option('date') ?: now()->toDateString());
        $now = $date->isToday() ? now() : $date->copy()->endOfDay();
        $totalCreated = 0;

        $rombelIds = Rombel::query()
            ->when($this->option('rombel_id'), fn ($query, $rombelId) => $query->where('id', $rombelId))
            ->whereHas('murid', fn ($query) => $query->aktif())
            ->pluck('id');

        if ($rombelIds->isEmpty()) {
            $this->info('Auto alpa selesai. 0 data dibuat.');

            return self::SUCCESS;
        }

        foreach ($jadwalService->forRombels($rombelIds, $date) as $rombelId => $jadwal) {
            if (!$jadwalService->bolehScan($jadwal)) {
                continue;
            }

            $totalCreated += $autoAlpaService->syncForRombel(
                $rombelId,
                $date->toDateString(),
                $jadwal,
                $now,
            );
        }

        $this->info("Auto alpa selesai. {$totalCreated} data dibuat.");

        return self::SUCCESS;
    }
}
