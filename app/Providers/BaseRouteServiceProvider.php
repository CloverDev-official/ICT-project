<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class BaseRouteServiceProvider extends ServiceProvider
{
    protected string $name;

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        $this->map();
    }

    /**
     * Define the routes for the application.
     */
    protected function map(): void
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }


    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware([
            'web',
            'auth',
        ])
        ->prefix('mpanel')
        ->group(
            module_path($this->name, 'routes/web.php')
        );
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        $path = module_path($this->name, 'routes/api.php');

        if (file_exists($path)) {
            Route::middleware('api')
                ->prefix('api')
                ->group($path);
        }
    }
}