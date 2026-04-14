<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Murid\Murid;
use App\Models\Murid\Rombel\Rombel;

class MuridSeeder extends Seeder
{
    public function run(): void
    {
        $rombelIds = Rombel::query()->pluck('id');

        if ($rombelIds->isEmpty()) {
            Murid::factory()->count(100)->create();
            return;
        }

        Murid::factory()
            ->count(100)
            ->state(fn() => ['rombel_id' => $rombelIds->random()])
            ->create();
    }
}
