<?php

use App\Enums\BillingType;
use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Models\Campsite;
use App\Models\CampsitePrice;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\Season;
use App\Models\SeasonPeriod;
use App\Models\User;
use App\Pricing\Actions\CalculatePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('calculates the order summary with coupon and extra totals', function () {
    config()->set('pricing.last_minute.enabled', false);

    $season = Season::factory()->create(['name' => 'Zomer']);
    SeasonPeriod::factory()->create([
        'season_id' => $season->id,
        'starts_at' => '2026-01-01',
        'ends_at' => '2026-12-31',
    ]);
    $campsite = Campsite::factory()->create();
    CampsitePrice::factory()->create([
        'campsite_id' => $campsite->id,
        'season_id' => $season->id,
        'nightly_rate' => 20,
        'per_adult_rate' => 5,
        'per_child_rate' => 2,
    ]);

    $customer = User::factory()->create();
    $coupon = Coupon::factory()->create([
        'scope' => CouponScope::Accommodation,
        'discount_type' => DiscountType::Percent,
        'discount_value' => 25,
        'uses_count' => 0,
    ]);
    $reservation = Reservation::factory()->pending()->create([
        'customer_id' => $customer->id,
        'campsite_id' => $campsite->id,
        'coupon_id' => $coupon->id,
        'check_in' => '2026-06-01',
        'check_out' => '2026-06-03',
        'num_adults' => 2,
        'num_children' => 1,
        'num_vehicles' => 0,
    ]);

    $extra = Extra::factory()->create([
        'billing_type' => BillingType::PerNight,
        'price' => 3,
    ]);

    $summary = app(CalculatePrice::class)->calculate($reservation, [
        ['extra' => $extra, 'quantity' => 2],
    ]);

    expect($summary->reservation_id)->toBe($reservation->id)
        ->and($summary->season_name)->toBe('Zomer')
        ->and($summary->num_nights)->toBe(2)
        ->and((float) $summary->nightly_rate)->toBe(20.0)
        ->and((float) $summary->per_adult_rate)->toBe(5.0)
        ->and((float) $summary->per_child_rate)->toBe(2.0)
        ->and($summary->last_minute_applied)->toBeFalse()
        ->and($summary->last_minute_discount)->toBeNull()
        ->and((float) $summary->coupon_discount)->toBe(16.0)
        ->and((float) $summary->extras_total)->toBe(12.0)
        ->and((float) $summary->total)->toBe(60.0);
});