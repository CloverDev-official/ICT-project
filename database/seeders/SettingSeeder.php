<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['key' => 'waktu_masuk'],
            ['value' => '07:30:00']
        );

        Setting::updateOrCreate(
            ['key' => 'waktu_keluar'],
            ['value' => '16:30:00']
        );
    }
}
