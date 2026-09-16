<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\General\Livewire\RefreshAllBrowsers\Index;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class BrowserRefreshTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost']);
    }

    public function test_super_admin_can_confirm_a_browser_refresh_signal(): void
    {
        $superAdmin = $this->user(1, 'Super Admin', ['pengaturan']);

        $this->actingAs($superAdmin);
        $component = app(Index::class);
        $component->requestRefresh();
        $this->assertTrue($component->showConfirmation);
        $component->confirmRefresh();
        $this->assertFalse($component->showConfirmation);

        $version = Setting::valueOf('system.browser_refresh_version');
        $this->assertNotEmpty($version);
        $route = app('router')->getRoutes()->getByName('browser-refresh-version');
        $this->assertSame('browser-refresh-version', $route->uri());
        $this->assertSame(['HEAD'], $route->methods());
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains('throttle:browser-refresh-version', $route->gatherMiddleware());

        $response = app()->call($route->getAction('uses'));
        $this->assertSame(204, $response->getStatusCode());
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', (string) $response->headers->get('Cache-Control'));
        $this->assertSame($version, $response->headers->get('X-Browser-Refresh-Version'));
    }

    public function test_non_super_admin_cannot_see_or_trigger_browser_refresh(): void
    {
        $operator = $this->user(2, 'Operator', ['pengaturan']);

        $this->actingAs($operator);
        $component = app(Index::class);

        try {
            $component->requestRefresh();
            $this->fail('Non-super admin dapat membuka konfirmasi refresh browser.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $this->assertNull(Setting::valueOf('system.browser_refresh_version'));
    }

    private function user(int $roleId, string $roleName, array $permissions): User
    {
        Role::forceCreate([
            'id' => $roleId,
            'name' => $roleName,
            'permissions' => $permissions,
        ]);

        return User::create([
            'name' => $roleName,
            'email' => strtolower(str_replace(' ', '-', $roleName)).'@example.test',
            'password' => 'password',
            'role_id' => $roleId,
            'is_active' => true,
        ]);
    }
}
