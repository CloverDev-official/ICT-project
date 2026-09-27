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
            ['key' => 'jadwal.default_masuk'], 
            ['value' => '07:30:00']
        );
        
        Setting::updateOrCreate(
            ['key' => 'jadwal.default_pulang_normal'], 
            ['value' => '16:30:00']
        );
        
        Setting::updateOrCreate(
            ['key' => 'jadwal.default_pulang_jumat'], 
            ['value' => '11:30:00']
        );
        
        Setting::updateOrCreate(
            ['key' => 'jadwal.scan_masuk_mulai'], 
            ['value' => '06:00:00']
        );
        
        Setting::updateOrCreate(
            ['key' => 'jadwal.scan_masuk_sampai'], 
            ['value' => '07:35:00']
        );

        Setting::updateOrCreate(
            ['key' => 'jadwal.scan_masuk_sampai_dasar'],
            ['value' => '07:30:00']
        );

        Setting::updateOrCreate(
            ['key' => 'jadwal.toleransi_masuk'],
            ['value' => '5']
        );
        
        Setting::updateOrCreate(
            ['key' => 'jadwal.scan_keluar_mulai'], 
            ['value' => '16:30:00']
        );
        
        Setting::updateOrCreate(
            ['key' => 'jadwal.scan_keluar_sampai'], 
            ['value' => '18:00:00']
        );
        
        Setting::updateOrCreate(
            ['key' => 'waktu_keluar'],
            ['value' => '16:30:00']
        );
    }
}
