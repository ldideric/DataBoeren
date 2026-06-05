<?php

declare(strict_types=1);

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Enums\UserRole;
use App\Filament\Resources\Coupons\CouponResource;
use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\ViewCoupon;
use App\Filament\Resources\Coupons\RelationManagers\ReservationsRelationManager;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\Reservation;
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

it('can render the coupons list page', function () {
    Livewire::test(ListCoupons::class)
        ->assertSuccessful();
});

it('can list coupons', function () {
    $coupons = Coupon::factory()->count(3)->create();

    Livewire::test(ListCoupons::class)
        ->assertCanSeeTableRecords($coupons);
});

it('can render coupon table columns', function () {
    Coupon::factory()->create();

    Livewire::test(ListCoupons::class)
        ->assertCanRenderTableColumn('title')
        ->assertCanRenderTableColumn('code')
        ->assertCanRenderTableColumn('scope')
        ->assertCanRenderTableColumn('formatted_discount')
        ->assertCanRenderTableColumn('expires_at')
        ->assertCanRenderTableColumn('uses_count');
});

it('can search coupons by title', function () {
    $coupon = Coupon::factory()->create(['title' => 'Zomervakantie Korting']);
    $other = Coupon::factory()->create(['title' => 'Winterdeal']);

    Livewire::test(ListCoupons::class)
        ->searchTable('Zomervakantie')
        ->assertCanSeeTableRecords([$coupon])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can search coupons by code', function () {
    $coupon = Coupon::factory()->create(['code' => 'UNIEK99']);
    $other = Coupon::factory()->create(['code' => 'ANDERS11']);

    Livewire::test(ListCoupons::class)
        ->searchTable('UNIEK99')
        ->assertCanSeeTableRecords([$coupon])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can sort coupons by expires_at', function () {
    $coupons = Coupon::factory()->count(3)->create([
        'expires_at' => fn () => fake()->dateTimeBetween('+1 month', '+1 year'),
    ]);

    Livewire::test(ListCoupons::class)
        ->sortTable('expires_at')
        ->assertCanSeeTableRecords($coupons->sortBy('expires_at'), inOrder: true);
});

it('can bulk delete coupons', function () {
    $coupons = Coupon::factory()->count(3)->create();

    Livewire::test(ListCoupons::class)
        ->callTableBulkAction(DeleteBulkAction::class, $coupons);

    $coupons->each(fn ($c) => $this->assertSoftDeleted($c));
});

// Create page

it('can render the create coupon page', function () {
    Livewire::test(CreateCoupon::class)
        ->assertSuccessful();
});

it('can create a coupon', function () {
    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'title'          => 'Testkorting',
            'code'           => 'TEST10',
            'scope'          => CouponScope::Accommodation->value,
            'discount_type'  => DiscountType::Percent->value,
            'discount_value' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Coupon::class, [
        'title' => 'Testkorting',
        'code'  => 'TEST10',
    ]);
});

it('validates required fields on coupon create', function () {
    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'title'          => null,
            'code'           => null,
            'scope'          => null,
            'discount_type'  => null,
            'discount_value' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'title'          => 'required',
            'code'           => 'required',
            'scope'          => 'required',
            'discount_type'  => 'required',
            'discount_value' => 'required',
        ]);
});

it('validates unique code on coupon create', function () {
    Coupon::factory()->create(['code' => 'DUBBEL99']);

    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'title'          => 'Test',
            'code'           => 'DUBBEL99',
            'scope'          => CouponScope::Accommodation->value,
            'discount_type'  => DiscountType::Percent->value,
            'discount_value' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['code' => 'unique']);
});

// View page

it('can render the view coupon page', function () {
    $coupon = Coupon::factory()->create();

    Livewire::test(ViewCoupon::class, ['record' => $coupon->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit coupon page', function () {
    $coupon = Coupon::factory()->create();

    Livewire::test(EditCoupon::class, ['record' => $coupon->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve coupon data on the edit page', function () {
    $coupon = Coupon::factory()->create([
        'title'          => 'Testkorting',
        'code'           => 'TEST10',
        'scope'          => CouponScope::Accommodation,
        'discount_type'  => DiscountType::Percent,
        'discount_value' => 10,
    ]);

    Livewire::test(EditCoupon::class, ['record' => $coupon->getRouteKey()])
        ->assertSchemaStateSet([
            'title'          => 'Testkorting',
            'code'           => 'TEST10',
            'scope'          => CouponScope::Accommodation,
            'discount_type'  => DiscountType::Percent,
            'discount_value' => 10,
        ]);
});

it('can update a coupon', function () {
    $coupon = Coupon::factory()->create(['title' => 'Oud']);

    Livewire::test(EditCoupon::class, ['record' => $coupon->getRouteKey()])
        ->fillForm(['title' => 'Nieuw'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($coupon->refresh()->title)->toBe('Nieuw');
});

it('allows editing own code (unique ignores current record)', function () {
    $coupon = Coupon::factory()->create(['code' => 'EIGEN10']);

    Livewire::test(EditCoupon::class, ['record' => $coupon->getRouteKey()])
        ->fillForm(['code' => 'EIGEN10'])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('rejects duplicate code on coupon update', function () {
    Coupon::factory()->create(['code' => 'BEZET99']);
    $coupon = Coupon::factory()->create(['code' => 'MIJN10']);

    Livewire::test(EditCoupon::class, ['record' => $coupon->getRouteKey()])
        ->fillForm(['code' => 'BEZET99'])
        ->call('save')
        ->assertHasFormErrors(['code' => 'unique']);
});

// Reservations relation manager

it('can render the coupon reservations relation manager', function () {
    $coupon = Coupon::factory()->create();

    Livewire::test(ReservationsRelationManager::class, [
        'ownerRecord' => $coupon,
        'pageClass'   => EditCoupon::class,
    ])
        ->assertSuccessful();
});

it('can list reservations in the coupon reservations relation manager', function () {
    $coupon = Coupon::factory()->create();
    $reservations = Reservation::factory()->withCoupon($coupon)->count(2)->create();

    Livewire::test(ReservationsRelationManager::class, [
        'ownerRecord' => $coupon,
        'pageClass'   => EditCoupon::class,
    ])
        ->assertCanSeeTableRecords($reservations);
});

// Authorization

it('prevents customers from accessing the coupons list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListCoupons::class)
        ->assertForbidden();
});

// Extra-scoped coupon

it('can create a coupon scoped to a specific extra', function () {
    $extra = Extra::factory()->create();

    Livewire::test(CreateCoupon::class)
        ->fillForm([
            'title'          => 'Extra Korting',
            'code'           => 'EXTRA10',
            'scope'          => CouponScope::Extra->value,
            'extra_id'       => $extra->id,
            'discount_type'  => DiscountType::Percent->value,
            'discount_value' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Coupon::class, [
        'code'  => 'EXTRA10',
        'scope' => CouponScope::Extra->value,
    ]);
});

// HTTP routes

it('admin can load coupons index via HTTP', function () {
    $this->get(CouponResource::getUrl('index'))->assertOk();
});

it('admin can load create coupon page via HTTP', function () {
    $this->get(CouponResource::getUrl('create'))->assertOk();
});

it('admin can load view coupon page via HTTP', function () {
    $coupon = Coupon::factory()->create();
    $this->get(CouponResource::getUrl('view', ['record' => $coupon]))->assertOk();
});

it('admin can load edit coupon page via HTTP', function () {
    $coupon = Coupon::factory()->create();
    $this->get(CouponResource::getUrl('edit', ['record' => $coupon]))->assertOk();
});

it('unauthenticated user is redirected to login from coupons', function () {
    Auth::logout();
    $this->get(CouponResource::getUrl('index'))->assertRedirect('/admin/login');
});
