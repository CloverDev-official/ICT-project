<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    use WithoutModelEvents;

    private array $defaultPermissions = [
        'super-admin' => [
            'dashboard',
            'pilih-absen',
            'laporan',
            'rekap-absen',
            'rekap-absen-murid',
            'rekap-absen-guru',
            'riwayat',
            'riwayat-murid',
            'data-master',
            'data-murid',
            'data-guru',
            'data-kelas',
            'data-jurusan',
            'absensi',
            'absensi-murid',
            'absensi-guru',
            'manajemen',
            'generate-qr',
            'manajemen-waktu',
            'manajemen-murid',
            'manajemen-lainnya',
            'manajemen-role',
            'manajemen-tahun-ajaran',
            'pengaturan',
            'profil',
        ],
        'wali-kelas' => [
            'dashboard',
            'laporan',
            'rekap-absen',
            'rekap-absen-murid',
            'riwayat',
            'riwayat-murid',
            'absensi',
            'absensi-murid',
            'data-master',
            'data-murid',
            'profil',
        ],
        'operator' => [
            'manajemen',
            'pilih-absen',
            'generate-qr',
            'manajemen-waktu',
            'profil',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [

            1 => 'Super Admin',
            2 => 'Pengawas',
            3 => 'Guru',
            4 => 'Wali Kelas',
            5 => 'Operator',
        ];

        foreach ($roles as $id => $nama) {
            Role::updateOrCreate(
                ['id' => $id],
                [
                    'name' => $nama,
                    'permissions' => $this->defaultPermissions[
                        \Illuminate\Support\Str::slug($nama)
                    ] ?? [],
                ],
            );
        }
    }
}
