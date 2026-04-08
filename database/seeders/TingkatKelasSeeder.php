<?php

namespace Database\Seeders;

use App\Models\Murid\TingkatKelas;
use Illuminate\Database\Seeder;

class TingkatKelasSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['X', 'XI', 'XII'] as $nama) {
            TingkatKelas::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
