<?php

declare(strict_types=1);

use App\Enums\CampsiteType;
use App\Enums\UserRole;
use App\Filament\Resources\Campsites\CampsiteResource;
use App\Filament\Resources\Campsites\Pages\CreateCampsite;
use App\Filament\Resources\Campsites\Pages\EditCampsite;
use App\Filament\Resources\Campsites\Pages\ListCampsites;
use App\Filament\Resources\Campsites\Pages\ViewCampsite;
use App\Filament\Resources\Campsites\RelationManagers\PricesRelationManager;
use App\Filament\Resources\Campsites\RelationManagers\ReservationsRelationManager;
use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Reservation;
use App\Models\Season;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();
    $this->actingAs($admin);
});

// List page

it('can render the campsites list page', function () {
    Livewire::test(ListCampsites::class)
        ->assertSuccessful();
});

it('can list campsites', function () {
    $campsites = Campsite::factory()->count(3)->create();

    Livewire::test(ListCampsites::class)
        ->assertCanSeeTableRecords($campsites);
});

it('can render campsite table columns', function () {
    Campsite::factory()->create();

    Livewire::test(ListCampsites::class)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('type')
        ->assertCanRenderTableColumn('has_electricity')
        ->assertCanRenderTableColumn('max_people')
        ->assertCanRenderTableColumn('notes');
});

it('can search campsites by name', function () {
    $campsite = Campsite::factory()->create(['name' => 'Plek Uniek']);
    $other = Campsite::factory()->create(['name' => 'Plek Ander']);

    Livewire::test(ListCampsites::class)
        ->searchTable('Uniek')
        ->assertCanSeeTableRecords([$campsite])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can sort campsites by max_people', function () {
    $campsites = Campsite::factory()->count(3)->create();

    Livewire::test(ListCampsites::class)
        ->sortTable('max_people')
        ->assertCanSeeTableRecords($campsites->sortBy('max_people'), inOrder: true);
});

it('can bulk delete campsites', function () {
    $campsites = Campsite::factory()->count(3)->create();

    Livewire::test(ListCampsites::class)
        ->callTableBulkAction(DeleteBulkAction::class, $campsites);

    $campsites->each(fn ($c) => $this->assertSoftDeleted($c));
});

// Create page

it('can render the create campsite page', function () {
    Livewire::test(CreateCampsite::class)
        ->assertSuccessful();
});

it('can create a campsite', function () {
    Livewire::test(CreateCampsite::class)
        ->fillForm([
            'name'            => 'Plek 01',
            'type'            => CampsiteType::Paardenveld->value,
            'has_electricity' => true,
            'max_people'      => 4,
            'notes'           => 'Test notitie',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Campsite::class, [
        'name'            => 'Plek 01',
        'has_electricity' => true,
        'max_people'      => 4,
    ]);
});

it('validates required fields on campsite create', function () {
    Livewire::test(CreateCampsite::class)
        ->fillForm([
            'name'         => null,
            'type'         => null,
            'max_people'   => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name'         => 'required',
            'type'         => 'required',
            'max_people'   => 'required',
        ]);
});

// View page

it('can render the view campsite page', function () {
    $campsite = Campsite::factory()->create();

    Livewire::test(ViewCampsite::class, ['record' => $campsite->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit campsite page', function () {
    $campsite = Campsite::factory()->create();

    Livewire::test(EditCampsite::class, ['record' => $campsite->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve campsite data on the edit page', function () {
    $campsite = Campsite::factory()->create([
        'name'         => 'Plek 01',
        'max_people'   => 4,
    ]);

    Livewire::test(EditCampsite::class, ['record' => $campsite->getRouteKey()])
        ->assertSchemaStateSet([
            'name'         => 'Plek 01',
            'max_people'   => 4,
        ]);
});

it('can update a campsite', function () {
    $campsite = Campsite::factory()->create(['name' => 'Oud Plek']);

    Livewire::test(EditCampsite::class, ['record' => $campsite->getRouteKey()])
        ->fillForm(['name' => 'Nieuw Plek'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($campsite->refresh()->name)->toBe('Nieuw Plek');
});

// Prices relation manager

it('can render the prices relation manager', function () {
    $campsite = Campsite::factory()->create();

    Livewire::test(PricesRelationManager::class, [
        'ownerRecord' => $campsite,
        'pageClass'   => EditCampsite::class,
    ])
        ->assertSuccessful();
});

it('can list prices in the prices relation manager', function () {
    $campsite = Campsite::factory()->create();
    $prices = CampsitePrice::factory()->count(2)->create(['campsite_id' => $campsite->id]);

    Livewire::test(PricesRelationManager::class, [
        'ownerRecord' => $campsite,
        'pageClass'   => EditCampsite::class,
    ])
        ->assertCanSeeTableRecords($prices);
});

it('can create a price via the prices relation manager', function () {
    $campsite = Campsite::factory()->create();
    $season = Season::factory()->create();

    Livewire::test(PricesRelationManager::class, [
        'ownerRecord' => $campsite,
        'pageClass'   => EditCampsite::class,
    ])
        ->callTableAction('create', data: [
            'season_id'      => $season->id,
            'nightly_rate'   => 2000,
            'per_adult_rate' => 500,
            'per_child_rate' => 250,
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(CampsitePrice::class, [
        'campsite_id'  => $campsite->id,
        'season_id'    => $season->id,
        'nightly_rate' => 2000,
    ]);
});

// Campsite reservations relation manager

it('can render the campsite reservations relation manager', function () {
    $campsite = Campsite::factory()->create();

    Livewire::test(ReservationsRelationManager::class, [
        'ownerRecord' => $campsite,
        'pageClass'   => EditCampsite::class,
    ])
        ->assertSuccessful();
});

it('can list reservations in the campsite reservations relation manager', function () {
    $campsite = Campsite::factory()->create();
    $reservations = Reservation::factory()->count(2)->create(['campsite_id' => $campsite->id]);

    Livewire::test(ReservationsRelationManager::class, [
        'ownerRecord' => $campsite,
        'pageClass'   => EditCampsite::class,
    ])
        ->assertCanSeeTableRecords($reservations);
});

// Authorization

it('prevents customers from accessing the campsites list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListCampsites::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load campsites index via HTTP', function () {
    $this->get(CampsiteResource::getUrl('index'))->assertOk();
});

it('admin can load create campsite page via HTTP', function () {
    $this->get(CampsiteResource::getUrl('create'))->assertOk();
});

it('admin can load view campsite page via HTTP', function () {
    $campsite = Campsite::factory()->create();
    $this->get(CampsiteResource::getUrl('view', ['record' => $campsite]))->assertOk();
});

it('admin can load edit campsite page via HTTP', function () {
    $campsite = Campsite::factory()->create();
    $this->get(CampsiteResource::getUrl('edit', ['record' => $campsite]))->assertOk();
});

it('unauthenticated user is redirected to login from campsites', function () {
    Auth::logout();
    $this->get(CampsiteResource::getUrl('index'))->assertRedirect('/admin/login');
});
