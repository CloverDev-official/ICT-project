<?php

namespace Database\Seeders;

use App\Models\Murid\Rombel\Tingkat;
use Illuminate\Database\Seeder;

class TingkatSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['X', 'XI', 'XII', 'XIII'] as $nama) {
            Tingkat::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
