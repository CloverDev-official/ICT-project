<?php

namespace Database\Seeders;

use App\Models\Murid\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['PPLG', 'TJKT', 'DKV'] as $nama) {
            Jurusan::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
