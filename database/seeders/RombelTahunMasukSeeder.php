<?php

namespace Database\Seeders;

use App\Models\Murid\Rombel\Rombel;
use Illuminate\Database\Seeder;

class RombelTahunMasukSeeder extends Seeder
{
    public function run(): void
    {
        $baseYear = now()->year;

        Rombel::query()
            ->with('tingkat')
            ->get()
            ->each(function (Rombel $rombel) use ($baseYear) {
                $tingkatNama = strtolower((string) ($rombel->tingkat?->nama ?? ''));

                $tahunMasuk = match ($tingkatNama) {
                    'x' => $baseYear,
                    'xi' => $baseYear - 1,
                    'xii' => $baseYear - 2,
                    default => null,
                };

                if ($tahunMasuk !== null) {
                    $rombel->update(['tahun_masuk' => $tahunMasuk]);
                }
            });
    }
}