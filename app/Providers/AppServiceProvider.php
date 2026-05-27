<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (App::environment('production', 'staging')) {
            URL::forceScheme('https');
        }

        $this->loadSubdirMigrations();
    }

    protected function loadSubdirMigrations(): void
    {
        $migrationsPath = database_path('migrations');
        $directories = glob($migrationsPath.'/*', GLOB_ONLYDIR);
        $paths = array_merge([$migrationsPath], $directories);

        $this->loadMigrationsFrom($paths);
    }
}
