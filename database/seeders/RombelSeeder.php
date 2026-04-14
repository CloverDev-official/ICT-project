<?php

namespace Database\Seeders;

use App\Models\Murid\Rombel\Indeks;
use App\Models\Murid\Rombel\Jurusan;
use App\Models\Murid\Rombel\Rombel;
use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
        $tingkatIds = Tingkat::query()->pluck('id');
        $jurusanIds = Jurusan::query()->pluck('id');
        $indeksIds = Indeks::query()->pluck('id');

        if (
            $tingkatIds->isEmpty() ||
            $jurusanIds->isEmpty() ||
            $indeksIds->isEmpty()
        ) {
            return;
        }

        foreach ($tingkatIds as $tingkatId) {
            foreach ($jurusanIds as $jurusanId) {
                foreach ($indeksIds as $indeksId) {
                    Rombel::query()->firstOrCreate([
                        'tingkat_id' => $tingkatId,
                        'jurusan_id' => $jurusanId,
                        'indeks_id' => $indeksId,
                    ]);
                }
            }
        }
    }
}
