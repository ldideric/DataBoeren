<?php

use App\Enums\CampsiteType;
use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Season;
use App\Models\SeasonPeriod;
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
    ], $overrides);
}

function makeBookable(Campsite|iterable $campsites): void
{
    $season = Season::query()->first() ?? Season::factory()
        ->has(
            SeasonPeriod::factory()->state([
                'starts_at' => today()->subYear(),
                'ends_at' => today()->addYear(),
            ]),
            'periods',
        )
        ->create();

    foreach ($campsites instanceof Campsite ? [$campsites] : $campsites as $campsite) {
        CampsitePrice::factory()->for($campsite)->for($season)->create();
    }
}

it('shows no results until the stay criteria are complete', function () {
    Campsite::factory()->create(['max_people' => 6]);

    Livewire::test('campsite-search')
        ->assertSee('Vul je verblijfsgegevens in')
        ->assertSee('Nog geen zoekopdracht');
});

it('lists available campsites that fit the party once searched', function () {
    $big = Campsite::factory()->create(['name' => 'Grote Plek', 'max_people' => 6]);
    Campsite::factory()->create(['name' => 'Kleine Plek', 'max_people' => 1]);
    makeBookable($big);

    Livewire::test('campsite-search', stay())
        ->assertSee('Grote Plek')
        ->assertDontSee('Kleine Plek'); // too small for 2 people
});

it('hides campsites that have no price for the stay', function () {
    $priced = Campsite::factory()->create(['name' => 'Met Prijs', 'max_people' => 6]);
    makeBookable($priced);
    Campsite::factory()->create(['name' => 'Zonder Prijs', 'max_people' => 6]); // no price set

    Livewire::test('campsite-search', stay())
        ->assertSee('Met Prijs')
        ->assertDontSee('Zonder Prijs');
});

it('filters by accommodation type in SQL, not just in the browser (bug: filters work for all fields)', function () {
    $koe = Campsite::factory()->ofType(CampsiteType::Koeienveld)->create(['name' => 'Koe Plek', 'max_people' => 6]);
    $geit = Campsite::factory()->ofType(CampsiteType::Geitenveld)->create(['name' => 'Geit Plek', 'max_people' => 6]);
    makeBookable([$koe, $geit]);

    Livewire::test('campsite-search', stay())
        ->set('types', [CampsiteType::Koeienveld->value])
        ->assertSee('Koe Plek')
        ->assertDontSee('Geit Plek')
        ->assertSee('1 beschikbaarheden gevonden'); // total reflects the filter
});

it('filters by multiple accommodation types at once', function () {
    $koe = Campsite::factory()->ofType(CampsiteType::Koeienveld)->create(['name' => 'Koe Plek', 'max_people' => 6]);
    $geit = Campsite::factory()->ofType(CampsiteType::Geitenveld)->create(['name' => 'Geit Plek', 'max_people' => 6]);
    $schaap = Campsite::factory()->ofType(CampsiteType::Schapenveld)->create(['name' => 'Schaap Plek', 'max_people' => 6]);
    makeBookable([$koe, $geit, $schaap]);

    Livewire::test('campsite-search', stay())
        ->set('types', [CampsiteType::Koeienveld->value, CampsiteType::Geitenveld->value])
        ->assertSee('Koe Plek')
        ->assertSee('Geit Plek')
        ->assertDontSee('Schaap Plek')
        ->assertSee('2 beschikbaarheden gevonden');
});

it('keeps the type filter when paging (bug: page 2 reset the filter)', function () {
    makeBookable(Campsite::factory()->count(12)->ofType(CampsiteType::Koeienveld)->create(['max_people' => 6]));
    makeBookable(Campsite::factory()->count(5)->ofType(CampsiteType::Geitenveld)->create(['max_people' => 6]));

    Livewire::test('campsite-search', stay())
        ->set('types', [CampsiteType::Koeienveld->value])
        ->call('gotoPage', 2)
        ->assertSet('types', [CampsiteType::Koeienveld->value])
        ->assertSee('12 beschikbaarheden gevonden'); // still only the filtered total
});

it('does not paginate an empty filtered result (bug: empty results had multiple pages)', function () {
    // Plenty of one type (would be 2 pages unfiltered) and none of another.
    makeBookable(Campsite::factory()->count(12)->ofType(CampsiteType::Koeienveld)->create(['max_people' => 6]));

    Livewire::test('campsite-search', stay())
        ->set('types', [CampsiteType::Schapenveld->value])
        ->assertSee('Geen beschikbare plekken')
        ->assertDontSee('Volgende'); // no pagination nav at all
});

it('resets to page one when the criteria change (live search, no button)', function () {
    makeBookable(Campsite::factory()->count(12)->ofType(CampsiteType::Koeienveld)->create(['max_people' => 6]));

    Livewire::test('campsite-search', stay())
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->set('children', 1) // any criteria change re-runs the search
        ->assertSet('paginators.page', 1);
});

it('searches live as soon as both dates are present (no submit needed)', function () {
    makeBookable(Campsite::factory()->create(['name' => 'Live Plek', 'max_people' => 6]));

    Livewire::test('campsite-search')
        ->assertSee('Nog geen zoekopdracht')
        ->set('datestart', today()->addDay()->format('Y-m-d'))
        ->assertSee('Nog geen zoekopdracht') // one date is not enough
        ->set('dateend', today()->addDays(3)->format('Y-m-d'))
        ->assertSee('Live Plek')             // results appear without a search button
        ->assertSee('beschikbaarheden gevonden');
});
