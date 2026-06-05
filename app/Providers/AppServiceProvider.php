<?php

namespace App\Providers;

use App\Http\ViewComposers\NavigationComposer;
use App\Listeners\ConfirmReservationOnStripePayment;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Events\WebhookReceived;

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

        View::composer('layouts.navigation', NavigationComposer::class);

        // Stripe is the source of truth for online payments: confirm the booking
        // (and send its mail) from the webhook so a customer who never returns to
        // the success page — or who paid with a delayed method like iDEAL — still
        // gets confirmed. Cashier verifies the signature before this fires.
        Event::listen(WebhookReceived::class, ConfirmReservationOnStripePayment::class);
    }

    protected function loadSubdirMigrations(): void
    {
        $migrationsPath = database_path('migrations');
        $directories = glob($migrationsPath.'/*', GLOB_ONLYDIR);
        $paths = array_merge([$migrationsPath], $directories);

        $this->loadMigrationsFrom($paths);
    }
}
