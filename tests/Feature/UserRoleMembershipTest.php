<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_permissions_are_combined_from_all_assigned_roles(): void
    {
        $waliKelas = Role::create([
            'name' => 'Wali Kelas',
            'permissions' => ['data-murid'],
        ]);
        $pengawas = Role::create([
            'name' => 'Pengawas',
            'permissions' => ['laporan'],
        ]);
        $user = User::create([
            'name' => 'Guru Pengawas',
            'email' => 'guru-pengawas@example.test',
            'password' => 'password',
            'role_id' => $waliKelas->id,
            'is_active' => true,
        ]);

        $user->roles()->sync([$waliKelas->id, $pengawas->id]);

        $this->assertTrue($user->hasRole('Wali Kelas'));
        $this->assertTrue($user->hasRole('Pengawas'));
        $this->assertFalse($user->hasOnlyRoles(['Wali Kelas', 'Wali Murid']));
        $this->assertTrue($user->canAccess('data-murid'));
        $this->assertTrue($user->canAccess('laporan'));
        $this->assertSame(['Pengawas', 'Wali Kelas'], $user->fresh()->assignedRoles()->pluck('name')->sort()->values()->all());
    }
}
