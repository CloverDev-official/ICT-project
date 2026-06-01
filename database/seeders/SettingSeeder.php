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
            ['key' => 'nama_website'],
            ['value' => 'Absensi']
        );

        Setting::updateOrCreate(
            ['key' => 'copyright'],
            ['value' => 'SMKN 2 Banjarmasin © 2026']
        );

        Setting::updateOrCreate(
            ['key' => 'logo'],
            ['value' => 'assets/img/logo_smkn_2.png']
        );

        Setting::updateOrCreate(
            ['key' => 'login_image'],
            ['value' => 'assets/img/skenda-profil.jpeg']
        );

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
