<?php

declare(strict_types=1);

use App\Enums\BillingType;
use App\Enums\StockType;
use App\Enums\UserRole;
use App\Filament\Resources\Extras\ExtraResource;
use App\Filament\Resources\Extras\Pages\CreateExtra;
use App\Filament\Resources\Extras\Pages\EditExtra;
use App\Filament\Resources\Extras\Pages\ListExtras;
use App\Filament\Resources\Extras\Pages\ViewExtra;
use App\Models\Extra;
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

it('can render the extras list page', function () {
    Livewire::test(ListExtras::class)
        ->assertSuccessful();
});

it('can list extras', function () {
    $extras = Extra::factory()->count(3)->create();

    Livewire::test(ListExtras::class)
        ->assertCanSeeTableRecords($extras);
});

it('can render extra table columns', function () {
    Extra::factory()->create();

    Livewire::test(ListExtras::class)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('billing_type')
        ->assertCanRenderTableColumn('price')
        ->assertCanRenderTableColumn('stock_type')
        ->assertCanRenderTableColumn('stock')
        ->assertCanRenderTableColumn('max_per_booking');
});

it('can search extras by name', function () {
    $extra = Extra::factory()->create(['name' => 'Uniek Item']);
    $other = Extra::factory()->create(['name' => 'Ander Item']);

    Livewire::test(ListExtras::class)
        ->searchTable('Uniek')
        ->assertCanSeeTableRecords([$extra])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can sort extras by price', function () {
    $extras = Extra::factory()->count(3)->create();

    Livewire::test(ListExtras::class)
        ->sortTable('price')
        ->assertCanSeeTableRecords($extras->sortBy('price'), inOrder: true);
});

it('can bulk delete extras', function () {
    $extras = Extra::factory()->count(3)->create();

    Livewire::test(ListExtras::class)
        ->callTableBulkAction(DeleteBulkAction::class, $extras);

    $extras->each(fn ($e) => $this->assertSoftDeleted($e));
});

// Create page

it('can render the create extra page', function () {
    Livewire::test(CreateExtra::class)
        ->assertSuccessful();
});

it('can create an extra', function () {
    Livewire::test(CreateExtra::class)
        ->fillForm([
            'name'            => 'Bedlinnen',
            'description'     => 'Schone bedlinnen set',
            'billing_type'    => BillingType::OneTime->value,
            'price'           => 1500,
            'stock_type'      => StockType::Rental->value,
            'stock'           => null,
            'max_per_booking' => null,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Extra::class, [
        'name'  => 'Bedlinnen',
        'price' => 1500,
    ]);
});

it('validates required fields on extra create', function () {
    Livewire::test(CreateExtra::class)
        ->fillForm([
            'name'         => null,
            'billing_type' => null,
            'price'        => null,
            'stock_type'   => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name'         => 'required',
            'billing_type' => 'required',
            'price'        => 'required',
            'stock_type'   => 'required',
        ]);
});

// View page

it('can render the view extra page', function () {
    $extra = Extra::factory()->create();

    Livewire::test(ViewExtra::class, ['record' => $extra->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit extra page', function () {
    $extra = Extra::factory()->create();

    Livewire::test(EditExtra::class, ['record' => $extra->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve extra data on the edit page', function () {
    $extra = Extra::factory()->create([
        'name'  => 'Handdoek',
        'price' => 500,
    ]);

    Livewire::test(EditExtra::class, ['record' => $extra->getRouteKey()])
        ->assertSchemaStateSet([
            'name'  => 'Handdoek',
            'price' => 500,
        ]);
});

it('can update an extra', function () {
    $extra = Extra::factory()->create(['name' => 'Oud', 'price' => 500]);

    Livewire::test(EditExtra::class, ['record' => $extra->getRouteKey()])
        ->fillForm(['name' => 'Nieuw', 'price' => 750])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($extra->refresh())
        ->name->toBe('Nieuw')
        ->price->toBe(750);
});

it('can update extra stock', function () {
    $extra = Extra::factory()->create(['stock' => null]);

    Livewire::test(EditExtra::class, ['record' => $extra->getRouteKey()])
        ->fillForm(['stock' => 10])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($extra->refresh()->stock)->toBe(10);
});

// Authorization

it('prevents customers from accessing the extras list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListExtras::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load extras index via HTTP', function () {
    $this->get(ExtraResource::getUrl('index'))->assertOk();
});

it('admin can load create extra page via HTTP', function () {
    $this->get(ExtraResource::getUrl('create'))->assertOk();
});

it('admin can load view extra page via HTTP', function () {
    $extra = Extra::factory()->create();
    $this->get(ExtraResource::getUrl('view', ['record' => $extra]))->assertOk();
});

it('admin can load edit extra page via HTTP', function () {
    $extra = Extra::factory()->create();
    $this->get(ExtraResource::getUrl('edit', ['record' => $extra]))->assertOk();
});

it('unauthenticated user is redirected to login from extras', function () {
    Auth::logout();
    $this->get(ExtraResource::getUrl('index'))->assertRedirect('/admin/login');
});
