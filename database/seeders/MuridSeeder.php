<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Murid;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        Murid::factory()->count(100)->create();

        $this->call([
        MuridSeeder::class,
    ]);
    }
}