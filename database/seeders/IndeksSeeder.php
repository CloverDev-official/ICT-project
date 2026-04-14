<?php

namespace Database\Seeders;

use App\Models\Murid\Rombel\Indeks;
use Illuminate\Database\Seeder;

class IndeksSeeder extends Seeder
{
    public function run(): void
    {
        // A, B, C, D, dst
        foreach (range('A', 'H') as $nama) {
            Indeks::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
