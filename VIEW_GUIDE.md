# View & Route Guide

Deze guide legt uit hoe views en routes in dit project zijn georganiseerd, en hoe je nieuwe toevoegt. De voorbeelden komen direct uit onze codebase, dus je kunt meelezen door de gelinkte bestanden te openen.

## View system

Views staan onder [resources/views/](resources/views/) en zijn gegroepeerd in een folder per resource type. Elke folder volgt dezelfde conventies, zodat alles makkelijk terug te vinden is:

```
resources/views/
└── posts/               -> naam van het resource type (meervoud)
    ├── index.blade.php  -> toont alle items van dat type
    ├── create.blade.php -> formulier om een nieuw item aan te maken
    ├── edit.blade.php   -> formulier om een item te updaten
    └── show.blade.php   -> toont één specifiek item
```

Je hoeft niet elk bestand aan te maken — alleen die je daadwerkelijk gebruikt. [resources/views/bookings/](resources/views/bookings/) heeft bijvoorbeeld alleen `index.blade.php` en `create.blade.php`, omdat we momenteel geen detail- of edit-pagina voor bookings nodig hebben.

Gedeelde layout-onderdelen (navbar, footer, de HTML scaffold) staan in [resources/views/layouts/](resources/views/layouts/). Extend ze vanuit een view met `@extends('layouts.app')` in plaats van de boilerplate te herhalen.

## Route system

Alle web routes worden gedefinieerd in [routes/web.php](routes/web.php). Er zijn drie veelvoorkomende patronen die je zult toevoegen:

### 1. Een enkele pagina (zonder controller)

Wanneer je alleen een view wilt renderen en verder niks, kun je een route direct naar een closure laten wijzen die de view returnt:

```php
Route::get('/path/in/url', function () {
    return view('location.of.the.view');
});
```

Gebruik dit voor statische pagina's zoals "about" of "terms". Zodra je data wilt meegeven, de database moet bevragen, of een formulier wilt afhandelen, verplaats het dan naar een controller (zie hieronder).

### 2. Een enkele pagina met een controller

Voor alles wat meer is dan statische HTML koppel je de route aan een controller method. Dat is precies wat [routes/web.php:9](routes/web.php#L9) doet voor de home pagina:

```php
Route::get('/', [HomeController::class, 'index'])->name('home');
```

Het `->name('home')` deel zorgt ervoor dat je de route vanuit views en redirects kunt aanroepen als `route('home')`, in plaats van `/` hardcoded te gebruiken. Geef je routes altijd een naam — dat maakt URL-aanpassingen later pijnloos.

Om zo'n nieuwe pagina toe te voegen:

1. Maak de controller method aan (of een nieuwe controller via `php artisan make:controller PageController`).
2. Return een view ervanuit, op dezelfde manier als [HomeController](app/Http/Controllers/HomeController.php).
3. Voeg de route toe in [routes/web.php](routes/web.php) die naar de controller method wijst.
4. Voeg het bijbehorende `.blade.php` bestand toe onder [resources/views/](resources/views/).

### 3. Een volledige CRUD resource

Als een resource meerdere acties heeft (list, create, store, delete, etc.), groepeer ze dan onder één controller met `Route::controller()`. Zo is bookings opgezet in [routes/web.php:24-32](routes/web.php#L24-L32):

```php
Route::controller(BookingController::class)
    ->prefix('bookings')
    ->name('bookings.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/{reservation}', 'destroy')->name('destroy');
    });
```

Wat elk onderdeel doet:

- `->controller(BookingController::class)` — elke route in de group roept een method op deze controller aan, dus je hoeft alleen de method-naam te schrijven (`'index'`) in plaats van `[BookingController::class, 'index']`.
- `->prefix('bookings')` — plaatst `/bookings` voor elke URL in de group. Dus `'/'` wordt `/bookings`, en `'/create'` wordt `/bookings/create`.
- `->name('bookings.')` — plaatst `bookings.` voor elke route-naam. De `index` route wordt `bookings.index`, te gebruiken in views als `route('bookings.index')`.
- `{reservation}` — een route parameter. Laravel geeft wat er in dat URL-segment staat door aan de controller method. Met route-model binding zorgt het type-hinten van `Reservation $reservation` in de controller ervoor dat het bijbehorende model automatisch wordt geladen.

### Middleware groups (auth, guest)

Als een set routes alleen toegankelijk mag zijn wanneer iemand ingelogd is (of juist alleen wanneer iemand uitgelogd is), wikkel ze dan in een `Route::middleware(...)->group(...)`. De twee die wij gebruiken:

- `guest` — alleen toegankelijk wanneer je **niet** ingelogd bent (login, register formulieren). Zie [routes/web.php:14-19](routes/web.php#L14-L19).
- `auth` — alleen toegankelijk wanneer je ingelogd bent (bookings, logout). Zie [routes/web.php:21-33](routes/web.php#L21-L33).

Je kunt groups nesten, dus een CRUD group kan binnen een middleware group leven — dat is precies wat we voor bookings doen.

## Quick checklist voor het toevoegen van een nieuwe pagina

1. Bepaal of de pagina statisch is (closure) of dynamisch (controller).
2. Voeg een controller toe of breid een bestaande uit in [app/Http/Controllers/](app/Http/Controllers/).
3. Voeg de route toe in [routes/web.php](routes/web.php) met een `->name(...)`.
4. Voeg het blade-bestand toe in de bijbehorende folder onder [resources/views/](resources/views/).
5. Bepaal of de route binnen `auth` of `guest` middleware hoort.
6. Link ernaar vanuit andere views met `route('your.route.name')` — hardcode nooit de URL.

## Handige commands

- `php artisan route:list` — print elke geregistreerde route met zijn naam, method, en URL. Handig om te checken nadat je iets hebt toegevoegd.
- `php artisan make:controller FooController` — scaffold een nieuwe controller.
- `php artisan make:controller FooController --resource` — scaffold een controller met alle zeven RESTful methods al aangemaakt.
