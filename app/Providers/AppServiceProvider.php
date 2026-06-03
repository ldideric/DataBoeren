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

        // App translations (and Filament's bundled ones) live under the generic
        // "nl" locale, so normalise any Dutch regional variant such as "nl_NL"
        // to "nl". This keeps the default UI in Dutch instead of falling back to
        // English; the panel's SetPanelLocale middleware can still override it
        // per user afterwards.
        if (str_starts_with((string) App::getLocale(), 'nl')) {
            App::setLocale('nl');
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
