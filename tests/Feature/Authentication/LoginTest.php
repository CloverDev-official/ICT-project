<?php

namespace Tests\Feature\Authentication;

use App\Livewire\Auth\Login;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_login_from_the_livewire_form(): void
    {
        $user = $this->createUser(isActive: true);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login_from_the_livewire_form(): void
    {
        $user = $this->createUser(isActive: false);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'secret-password')
            ->call('login');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_bypass_the_form_via_fortify_endpoint(): void
    {
        $user = $this->createUser(isActive: false);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'secret-password',
        ]);

        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_to_the_role_aware_home_gateway(): void
    {
        $user = $this->createUser(isActive: true);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/mpanel');

        $this->get('/mpanel')->assertRedirect(route('dashboard'));
    }

    private function createUser(bool $isActive): User
    {
        $role = Role::query()->create([
            'name' => 'Administrator',
            'permissions' => ['dashboard'],
        ]);

        return User::query()->create([
            'name' => 'Test User',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('secret-password'),
            'role_id' => $role->id,
            'is_active' => $isActive,
        ]);
    }
}
