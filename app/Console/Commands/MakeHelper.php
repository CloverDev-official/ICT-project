<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:helper {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new helper file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Helpers/{$name}.php");

        if (!file_exists(app_path('Helpers'))) {
            mkdir(app_path('Helpers'), 0755, true);
        }

        if (file_exists($path)) {
            $this->error('Helper already exists!');
            return;
        }

        file_put_contents($path, "<?php\n\n");

        $this->info("Helper {$name} created successfully.");
    }
}
