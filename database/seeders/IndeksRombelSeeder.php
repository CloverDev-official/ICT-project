<?php

namespace Database\Seeders;

use App\Models\Murid\IndeksRombel;
use Illuminate\Database\Seeder;

class IndeksRombelSeeder extends Seeder
{
    public function run(): void
    {
        // A, B, C, D, dst
        foreach (range('A', 'H') as $nama) {
            IndeksRombel::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
