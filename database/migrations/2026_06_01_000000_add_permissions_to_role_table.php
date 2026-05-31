<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            'edit-absen-murid',
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
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('name');
        });

        DB::table('role')->orderBy('id')->get()->each(function ($role) {
            $permissions = $this->defaultPermissions[
                \Illuminate\Support\Str::slug($role->name)
            ] ?? [];

            DB::table('role')
                ->where('id', $role->id)
                ->update([
                    'permissions' => json_encode($permissions),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }
};