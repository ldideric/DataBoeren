<?php

use App\Enums\CampsiteType;
use App\Models\Campsite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/** A complete, valid stay starting tomorrow. */
function stay(array $overrides = []): array
{
    return array_merge([
        'datestart' => today()->addDay()->format('Y-m-d'),
        'dateend' => today()->addDays(3)->format('Y-m-d'),
        'adults' => 2,
        'children' => 0,
        'vehicles' => 1,
    ], $overrides);
}

it('shows no results until the stay criteria are complete', function () {
    Campsite::factory()->create(['max_people' => 6, 'max_vehicles' => 2]);

    Livewire::test('campsite-search')
        ->assertSee('Vul je verblijfsgegevens in')
        ->assertSee('Nog geen zoekopdracht');
});

it('lists available campsites that fit the party once searched', function () {
    Campsite::factory()->create(['name' => 'Grote Plek', 'max_people' => 6, 'max_vehicles' => 2]);
    Campsite::factory()->create(['name' => 'Kleine Plek', 'max_people' => 1, 'max_vehicles' => 1]);

    Livewire::test('campsite-search', stay())
        ->assertSee('Grote Plek')
        ->assertDontSee('Kleine Plek'); // too small for 2 people
});

it('filters by accommodation type in SQL, not just in the browser (bug: filters work for all fields)', function () {
    Campsite::factory()->ofType(CampsiteType::Koeienveld)->create(['name' => 'Koe Plek', 'max_people' => 6, 'max_vehicles' => 2]);
    Campsite::factory()->ofType(CampsiteType::Geitenveld)->create(['name' => 'Geit Plek', 'max_people' => 6, 'max_vehicles' => 2]);

    Livewire::test('campsite-search', stay())
        ->set('type', CampsiteType::Koeienveld->value)
        ->assertSee('Koe Plek')
        ->assertDontSee('Geit Plek')
        ->assertSee('1 beschikbaarheden gevonden'); // total reflects the filter
});

it('keeps the type filter when paging (bug: page 2 reset the filter)', function () {
    Campsite::factory()->count(12)->ofType(CampsiteType::Koeienveld)->create(['max_people' => 6, 'max_vehicles' => 2]);
    Campsite::factory()->count(5)->ofType(CampsiteType::Geitenveld)->create(['max_people' => 6, 'max_vehicles' => 2]);

    Livewire::test('campsite-search', stay())
        ->set('type', CampsiteType::Koeienveld->value)
        ->call('gotoPage', 2)
        ->assertSet('type', CampsiteType::Koeienveld->value)
        ->assertSee('12 beschikbaarheden gevonden'); // still only the filtered total
});

it('does not paginate an empty filtered result (bug: empty results had multiple pages)', function () {
    // Plenty of one type (would be 2 pages unfiltered) and none of another.
    Campsite::factory()->count(12)->ofType(CampsiteType::Koeienveld)->create(['max_people' => 6, 'max_vehicles' => 2]);

    Livewire::test('campsite-search', stay())
        ->set('type', CampsiteType::Schapenveld->value)
        ->assertSee('Geen beschikbare plekken')
        ->assertDontSee('Volgende'); // no pagination nav at all
});

it('resets to page one when the criteria change (live search, no button)', function () {
    Campsite::factory()->count(12)->ofType(CampsiteType::Koeienveld)->create(['max_people' => 6, 'max_vehicles' => 2]);

    Livewire::test('campsite-search', stay())
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->set('children', 1) // any criteria change re-runs the search
        ->assertSet('paginators.page', 1);
});

it('searches live as soon as both dates are present (no submit needed)', function () {
    Campsite::factory()->create(['name' => 'Live Plek', 'max_people' => 6, 'max_vehicles' => 2]);

    Livewire::test('campsite-search')
        ->assertSee('Nog geen zoekopdracht')
        ->set('datestart', today()->addDay()->format('Y-m-d'))
        ->assertSee('Nog geen zoekopdracht') // one date is not enough
        ->set('dateend', today()->addDays(3)->format('Y-m-d'))
        ->assertSee('Live Plek')             // results appear without a search button
        ->assertSee('beschikbaarheden gevonden');
});
