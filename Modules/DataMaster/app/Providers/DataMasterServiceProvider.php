<?php

namespace Modules\DataMaster\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Support\ModuleLivewireRegistrar;

class DataMasterServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'DataMaster';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'datamaster';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
        
        ModuleLivewireRegistrar::register($this->name);
    }

    /**
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }
}
