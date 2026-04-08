<?php

namespace Database\Seeders;

use App\Models\Murid\IndeksRombel;
use App\Models\Murid\Jurusan;
use App\Models\Murid\Rombel;
use App\Models\Murid\TingkatKelas;
use Illuminate\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
        $tingkatIds = TingkatKelas::query()->pluck('id');
        $jurusanIds = Jurusan::query()->pluck('id');
        $indeksIds = IndeksRombel::query()->pluck('id');

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
                        'tingkat_kelas_id' => $tingkatId,
                        'jurusan_id' => $jurusanId,
                        'indeks_rombel_id' => $indeksId,
                    ]);
                }
            }
        }
    }
}
