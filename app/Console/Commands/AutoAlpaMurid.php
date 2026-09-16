<?php

namespace App\Console\Commands;

use App\Models\Murid\Rombel\Rombel;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Modules\ScanQR\Services\AutoAlpaMuridService;

class AutoAlpaMurid extends Command
{
    protected $signature = 'absensi:auto-alpa-murid
                            {--date= : Tanggal absensi dengan format YYYY-MM-DD}
                            {--rombel_id= : ID rombel yang ingin diproses}';

    protected $description = 'Tandai murid aktif yang belum scan masuk sebagai Alpa setelah batas scan masuk lewat.';

    public function handle(
        AutoAlpaMuridService $autoAlpaService,
    ): int {
        $date = $this->resolveDate();

        if (! $date) {
            return self::FAILURE;
        }

        $rombelId = $this->resolveRombelId();

        if ($rombelId === false) {
            return self::FAILURE;
        }

        $now = $date->isToday() ? now() : $date->copy()->endOfDay();

        $rombelIds = Rombel::query()
            ->when($rombelId, fn ($query, $id) => $query->where('id', $id))
            ->whereHas('murid', fn ($query) => $query->aktif())
            ->pluck('id');

        if ($rombelIds->isEmpty()) {
            $this->info("Auto alpa selesai untuk {$date->toDateString()}. Tidak ada rombel aktif yang diproses.");

            return self::SUCCESS;
        }

        $totalCreated = $autoAlpaService->syncForRombels($rombelIds, $date, $now);

        $this->table(
            ['Tanggal', 'Rombel diproses', 'Data alpa baru'],
            [[$date->toDateString(), $rombelIds->count(), $totalCreated]],
        );

        return self::SUCCESS;
    }

    private function resolveDate(): ?Carbon
    {
        $value = $this->option('date');

        if (! $value) {
            return today();
        }

        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $this->error('Format --date harus YYYY-MM-DD, contohnya 2026-09-16.');

            return null;
        }

        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);
        } catch (InvalidFormatException) {
            $this->error('Format --date harus YYYY-MM-DD, contohnya 2026-09-16.');

            return null;
        }
        $errors = Carbon::getLastErrors();

        if (! $date || ($errors !== false && ($errors['warning_count'] || $errors['error_count']))) {
            $this->error('Format --date harus YYYY-MM-DD, contohnya 2026-09-16.');

            return null;
        }

        return $date;
    }

    private function resolveRombelId(): int|false|null
    {
        $value = $this->option('rombel_id');

        if ($value === null) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            $this->error('--rombel_id harus berupa ID rombel positif.');

            return false;
        }

        return (int) $value;
    }
}
