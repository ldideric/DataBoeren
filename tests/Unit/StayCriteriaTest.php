<?php

use App\Booking\DTO\StayCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

function criteriaFrom(array $query): StayCriteria
{
    return StayCriteria::fromRequest(Request::create('/campsites', 'GET', $query));
}

it('is incomplete when any field is missing', function () {
    expect(criteriaFrom([])->isComplete())->toBeFalse();

    $partial = criteriaFrom([
        'datestart' => Carbon::tomorrow()->format('Y-m-d'),
        'dateend' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
        'adults' => 2,
        // children + vehicles omitted
    ]);

    expect($partial->isComplete())->toBeFalse();
});

it('is complete with valid future dates and all counts', function () {
    $criteria = criteriaFrom([
        'datestart' => Carbon::tomorrow()->format('Y-m-d'),
        'dateend' => Carbon::tomorrow()->addDays(3)->format('Y-m-d'),
        'adults' => 2,
        'children' => 1,
        'vehicles' => 1,
    ]);

    expect($criteria->isComplete())->toBeTrue()
        ->and($criteria->partySize())->toBe(3);
});

it('rejects a check-in in the past', function () {
    $criteria = criteriaFrom([
        'datestart' => Carbon::yesterday()->format('Y-m-d'),
        'dateend' => Carbon::tomorrow()->format('Y-m-d'),
        'adults' => 1,
        'children' => 0,
        'vehicles' => 0,
    ]);

    expect($criteria->hasValidDates())->toBeFalse();
});

it('rejects a check-out on or before check-in', function () {
    $sameDay = Carbon::tomorrow()->format('Y-m-d');

    $criteria = criteriaFrom([
        'datestart' => $sameDay,
        'dateend' => $sameDay,
        'adults' => 1,
        'children' => 0,
        'vehicles' => 0,
    ]);

    expect($criteria->hasValidDates())->toBeFalse();
});

it('clamps adults to at least one and floors children and vehicles at zero', function () {
    $criteria = criteriaFrom([
        'datestart' => Carbon::tomorrow()->format('Y-m-d'),
        'dateend' => Carbon::tomorrow()->addDay()->format('Y-m-d'),
        'adults' => 0,
        'children' => -3,
        'vehicles' => -1,
    ]);

    expect($criteria->adults)->toBe(1)
        ->and($criteria->children)->toBe(0)
        ->and($criteria->vehicles)->toBe(0);
});
