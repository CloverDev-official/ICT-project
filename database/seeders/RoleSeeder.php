<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [

            1 => 'Super Admin',
            2 => 'Admin',
            3 => 'Guru',
            4 => 'Murid',
            5 => 'Wali Kelas',
            6 => 'Operator',
        ];

        foreach ($roles as $id => $nama) {
            Role::updateOrCreate(
                ['id' => $id],
                ['name' => $nama],
            );
        }
    }
}
