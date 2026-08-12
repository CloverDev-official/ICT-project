<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class PermissionRegistry
{
    protected static ?array $routes = null;

    protected static ?array $all = null;

    protected static ?array $groups = null;

    protected static ?array $routePermissions = null;

    protected static function routes(): array
    {
        if (self::$routes !== null) {
            return self::$routes;
        }

        $routes = [];

        foreach (Route::getRoutes() as $route) {
            $permissions = self::extractPermissions($route->gatherMiddleware());

            if ($permissions === []) {
                continue;
            }

            $uri = self::normalizePath($route->uri());

            $routes[] = [
                'name' => $route->getName(),
                'uri' => $uri,
                'group' => self::resolveGroup($uri),
                'permissions' => $permissions,
            ];
        }

        return self::$routes = $routes;
    }

    protected static function normalizePath(?string $value): string
    {
        $path = trim((string) $value, '/');

        if ($path === 'mpanel') {
            return '';
        }

        if (Str::startsWith($path, 'mpanel/')) {
            $path = Str::after($path, 'mpanel/');
        }

        return trim($path, '/');
    }

    protected static function extractPermissions(array $middlewares): array
    {
        $permissions = [];

        foreach ($middlewares as $middleware) {
            if (!is_string($middleware) || !Str::startsWith($middleware, 'access:')) {
                continue;
            }

            $permission = trim(Str::after($middleware, 'access:'));

            if ($permission !== '') {
                $permissions[] = $permission;
            }
        }

        return array_values(array_unique($permissions));
    }

    protected static function resolveGroup(string $uri): string
    {
        $segment = Str::of($uri)->trim('/')->before('/')->value();

        return match ($segment) {
            'laporan-pengawas', 'rekap', 'riwayat' => 'Laporan',
            'absensi' => 'Absensi',
            'data', 'data-murid', 'data-guru', 'data-kelas', 'data-jurusan' => 'Data Master',
            'manajemen', 'generate-qr' => 'Manajemen',
            default => 'General',
        };
    }

    protected static function sortPermissions(array $permissions): array
    {
        $permissions = array_values(array_unique($permissions));
        sort($permissions, SORT_NATURAL | SORT_FLAG_CASE);

        return $permissions;
    }

    protected static function routePermissionsMap(): array
    {
        if (self::$routePermissions !== null) {
            return self::$routePermissions;
        }

        $routePermissions = [];

        foreach (self::routes() as $route) {
            if (!$route['name']) {
                continue;
            }

            $routePermissions[$route['name']] = $route['permissions'];
        }

        return self::$routePermissions = $routePermissions;
    }

    public static function all(): array
    {
        if (self::$all !== null) {
            return self::$all;
        }

        $permissions = [];

        foreach (self::routes() as $route) {
            $permissions = array_merge($permissions, $route['permissions']);
        }

        return self::$all = self::sortPermissions($permissions);
    }

    public static function groups(): array
    {
        if (self::$groups !== null) {
            return self::$groups;
        }

        $groups = [];

        foreach (self::routes() as $route) {
            $group = $route['group'];
            $groups[$group] = array_merge($groups[$group] ?? [], $route['permissions']);
        }

        $orderedGroups = [];
        $preferredOrder = ['General', 'Laporan', 'Absensi', 'Data Master', 'Manajemen'];

        foreach ($preferredOrder as $groupName) {
            if (!array_key_exists($groupName, $groups)) {
                continue;
            }

            $orderedGroups[$groupName] = self::sortPermissions($groups[$groupName]);
            unset($groups[$groupName]);
        }

        ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($groups as $groupName => $permissions) {
            $orderedGroups[$groupName] = self::sortPermissions($permissions);
        }

        return self::$groups = $orderedGroups;
    }

    public static function permissionsForRoute(string $routeName): array
    {
        return self::routePermissionsMap()[$routeName] ?? [];
    }

    public static function canAccessRoute(?User $user, string $routeName): bool
    {
        if (!$user) {
            return false;
        }

        $permissions = self::permissionsForRoute($routeName);

        if ($permissions === []) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (!$user->canAccess($permission)) {
                return false;
            }
        }

        return true;
    }

    public static function canAccessGroup(?User $user, string $groupName): bool
    {
        if (!$user) {
            return false;
        }

        foreach (self::routes() as $route) {
            if ($route['group'] !== $groupName) {
                continue;
            }

            if (self::canAccessRoute($user, $route['name'] ?? '')) {
                return true;
            }
        }

        return false;
    }

    public static function canAccessUriPrefix(?User $user, string $prefix): bool
    {
        if (!$user) {
            return false;
        }

        $normalizedPrefix = self::normalizePath($prefix);

        foreach (self::routes() as $route) {
            if (!Str::startsWith($route['uri'], $normalizedPrefix)) {
                continue;
            }

            if (self::canAccessRoute($user, $route['name'] ?? '')) {
                return true;
            }
        }

        return false;
    }

    public static function canAccessAnyUriPrefix(?User $user, array $prefixes): bool
    {
        foreach ($prefixes as $prefix) {
            if (self::canAccessUriPrefix($user, $prefix)) {
                return true;
            }
        }

        return false;
    }
}