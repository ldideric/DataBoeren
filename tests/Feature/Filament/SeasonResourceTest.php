<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Resources\Seasons\Pages\CreateSeason;
use App\Filament\Resources\Seasons\Pages\EditSeason;
use App\Filament\Resources\Seasons\Pages\ListSeasons;
use App\Filament\Resources\Seasons\Pages\ViewSeason;
use App\Filament\Resources\Seasons\RelationManagers\CampsitePricesRelationManager;
use App\Filament\Resources\Seasons\RelationManagers\PeriodsRelationManager;
use App\Filament\Resources\Seasons\SeasonResource;
use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Season;
use App\Models\SeasonPeriod;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->withRole(UserRole::Admin)->create();
    $this->actingAs($this->admin);
});

// List page

it('can render the seasons list page', function () {
    Livewire::test(ListSeasons::class)
        ->assertSuccessful();
});

it('can list seasons', function () {
    $seasons = Season::factory()->count(3)->create();

    Livewire::test(ListSeasons::class)
        ->assertCanSeeTableRecords($seasons);
});

it('can render season table columns', function () {
    Season::factory()->create();

    Livewire::test(ListSeasons::class)
        ->assertCanRenderTableColumn('name');
});

it('can search seasons by name', function () {
    $season = Season::factory()->create(['name' => 'Hoogseizoen']);
    $other = Season::factory()->create(['name' => 'Laagseizoen']);

    Livewire::test(ListSeasons::class)
        ->searchTable('Hoog')
        ->assertCanSeeTableRecords([$season])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can bulk delete seasons', function () {
    $seasons = Season::factory()->count(3)->create();

    Livewire::test(ListSeasons::class)
        ->callTableBulkAction(DeleteBulkAction::class, $seasons);

    $seasons->each(fn ($s) => $this->assertModelMissing($s));
});

// Create page

it('can render the create season page', function () {
    Livewire::test(CreateSeason::class)
        ->assertSuccessful();
});

it('can create a season', function () {
    Livewire::test(CreateSeason::class)
        ->fillForm(['name' => 'Voorseizoen'])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Season::class, ['name' => 'Voorseizoen']);
});

it('validates required name on season create', function () {
    Livewire::test(CreateSeason::class)
        ->fillForm(['name' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

// View page

it('can render the view season page', function () {
    $season = Season::factory()->create();

    Livewire::test(ViewSeason::class, ['record' => $season->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit season page', function () {
    $season = Season::factory()->create();

    Livewire::test(EditSeason::class, ['record' => $season->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve season data on the edit page', function () {
    $season = Season::factory()->create(['name' => 'Naseizoen']);

    Livewire::test(EditSeason::class, ['record' => $season->getRouteKey()])
        ->assertSchemaStateSet(['name' => 'Naseizoen']);
});

it('can update a season', function () {
    $season = Season::factory()->create(['name' => 'Oud']);

    Livewire::test(EditSeason::class, ['record' => $season->getRouteKey()])
        ->fillForm(['name' => 'Nieuw'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($season->refresh()->name)->toBe('Nieuw');
});

// Periods relation manager

it('can render the periods relation manager', function () {
    $season = Season::factory()->create();

    Livewire::test(PeriodsRelationManager::class, [
        'ownerRecord' => $season,
        'pageClass'   => EditSeason::class,
    ])
        ->assertSuccessful();
});

it('can list periods in the periods relation manager', function () {
    $season = Season::factory()->create();
    $periods = SeasonPeriod::factory()->count(2)->create(['season_id' => $season->id]);

    Livewire::test(PeriodsRelationManager::class, [
        'ownerRecord' => $season,
        'pageClass'   => EditSeason::class,
    ])
        ->assertCanSeeTableRecords($periods);
});

it('can create a period via the periods relation manager', function () {
    $season = Season::factory()->create();

    Livewire::test(PeriodsRelationManager::class, [
        'ownerRecord' => $season,
        'pageClass'   => EditSeason::class,
    ])
        ->callTableAction('create', data: [
            'starts_at' => '2026-06-01',
            'ends_at'   => '2026-08-31',
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(SeasonPeriod::class, [
        'season_id' => $season->id,
        'starts_at' => '2026-06-01 00:00:00',
        'ends_at'   => '2026-08-31 00:00:00',
    ]);
});

// Campsite prices relation manager

it('can render the campsite prices relation manager', function () {
    $season = Season::factory()->create();

    Livewire::test(CampsitePricesRelationManager::class, [
        'ownerRecord' => $season,
        'pageClass'   => EditSeason::class,
    ])
        ->assertSuccessful();
});

it('can list campsite prices in the campsite prices relation manager', function () {
    $season = Season::factory()->create();
    $prices = CampsitePrice::factory()->count(2)->create(['season_id' => $season->id]);

    Livewire::test(CampsitePricesRelationManager::class, [
        'ownerRecord' => $season,
        'pageClass'   => EditSeason::class,
    ])
        ->assertCanSeeTableRecords($prices);
});

it('can create a campsite price via the campsite prices relation manager', function () {
    $season = Season::factory()->create();
    $campsite = Campsite::factory()->create();

    Livewire::test(CampsitePricesRelationManager::class, [
        'ownerRecord' => $season,
        'pageClass'   => EditSeason::class,
    ])
        ->callTableAction('create', data: [
            'campsite_id'    => $campsite->id,
            'nightly_rate'   => 3000,
            'per_adult_rate' => 600,
            'per_child_rate' => 300,
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(CampsitePrice::class, [
        'season_id'    => $season->id,
        'campsite_id'  => $campsite->id,
        'nightly_rate' => 3000,
    ]);
});

// Authorization

it('prevents customers from accessing the seasons list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListSeasons::class)
        ->assertForbidden();
});

it('prevents employees from accessing the seasons list', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->actingAs($employee);

    Livewire::test(ListSeasons::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load seasons index via HTTP', function () {
    $this->get(SeasonResource::getUrl('index'))->assertOk();
});

it('admin can load create season page via HTTP', function () {
    $this->get(SeasonResource::getUrl('create'))->assertOk();
});

it('admin can load view season page via HTTP', function () {
    $season = Season::factory()->create();
    $this->get(SeasonResource::getUrl('view', ['record' => $season]))->assertOk();
});

it('admin can load edit season page via HTTP', function () {
    $season = Season::factory()->create();
    $this->get(SeasonResource::getUrl('edit', ['record' => $season]))->assertOk();
});

it('unauthenticated user is redirected to login from seasons', function () {
    Auth::logout();
    $this->get(SeasonResource::getUrl('index'))->assertRedirect('/admin/login');
});
