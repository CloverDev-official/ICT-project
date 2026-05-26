<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Master Data & Configuration (Safe for Production)
        $this->call([
            RoleSeeder::class,
            SettingSeeder::class,
            TingkatSeeder::class,
            JurusanSeeder::class,
            IndeksSeeder::class,
            RombelSeeder::class,
            JadwalAbsenSeeder::class,
        ]);

        // 2. Default Admin User
        User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('test'),
                'role_id' => 1,
                'is_active' => true,
            ],
        );

        // 3. Optional/Dummy Data (ONLY for Development)
        // Jika ingin menjalankan data dummy 10rb murid, jalankan:
        // php artisan db:seed --class=MuridSeeder
        // $this->call(MuridSeeder::class);
        // $this->call(AbsenMuridSeeder::class);
    }
}
