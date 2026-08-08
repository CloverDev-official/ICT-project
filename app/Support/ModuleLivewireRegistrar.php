<?php

namespace App\Support;

use Livewire\Component;
use Livewire\Livewire;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleLivewireRegistrar
{
    public static function register(string $module): void
    {
        $path = module_path($module, 'app/Livewire');
        if (! is_dir($path)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace(
                $path . DIRECTORY_SEPARATOR,
                '',
                $file->getPathname()
            );

            $relativePath = str_replace(
                DIRECTORY_SEPARATOR,
                '\\',
                $relativePath
            );

            $relativePath = substr(
                $relativePath,
                0,
                -strlen('.php')
            );

            $class = "Modules\\{$module}\\Livewire\\{$relativePath}";

            if (! class_exists($class)) {
                continue;
            }

            if (! is_subclass_of($class, Component::class)) {
                continue;
            }

            $alias = self::makeAlias($module, $relativePath);

            // dd($alias, $class);

            Livewire::component($alias, $class);
        }
    }

    protected static function makeAlias(
        string $module,
        string $relativePath
    ): string {
        $parts = explode('\\', $relativePath);

        if ($parts !== [] && strcasecmp($parts[0], $module) === 0) {
            array_shift($parts);
        }

        $parts = array_map(
            fn ($part) => strtolower(
                preg_replace('/(?<!^)[A-Z]/', '-$0', $part)
            ),
            $parts
        );

        return strtolower($module) . '.' . implode('.', $parts);
    }
}