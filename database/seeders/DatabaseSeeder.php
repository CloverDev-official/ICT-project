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
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);

        User::updateOrCreate(
            ['email' => 'test@test.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('test'),
                'role_id' => 1,
                'is_active' => true,
            ],
        );

        $this->call(SettingSeeder::class);
    
        $this->call([
            TingkatSeeder::class,
            JurusanSeeder::class,
            IndeksSeeder::class,
            RombelSeeder::class,
        ]);

        $this->call(MuridSeeder::class);

        // $this->call(AbsenMuridSeeder::class);
    }
}
