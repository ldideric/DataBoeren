<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Filament\Resources\Customers\RelationManagers\ReservationsRelationManager;
use App\Models\Reservation;
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

it('can render the customers list page', function () {
    Livewire::test(ListCustomers::class)
        ->assertSuccessful();
});

it('can list customers', function () {
    $customers = User::factory()->count(3)->create();

    Livewire::test(ListCustomers::class)
        ->assertCanSeeTableRecords($customers);
});

it('does not show employees or admins in the customers list', function () {
    $customer = User::factory()->create();
    $employee = User::factory()->withRole(UserRole::Employee)->create();

    Livewire::test(ListCustomers::class)
        ->assertCanSeeTableRecords([$customer])
        ->assertCanNotSeeTableRecords([$employee]);
});

it('can render customer table columns', function () {
    User::factory()->create();

    Livewire::test(ListCustomers::class)
        ->assertCanRenderTableColumn('first_name')
        ->assertCanRenderTableColumn('email')
        ->assertCanRenderTableColumn('phone')
        ->assertCanRenderTableColumn('email_verified_at');
});

it('can search customers by name', function () {
    $customer = User::factory()->create(['first_name' => 'Uniekvoornaam', 'last_name' => 'Achternaam']);
    $other = User::factory()->create(['first_name' => 'Anders', 'last_name' => 'Persoon']);

    Livewire::test(ListCustomers::class)
        ->searchTable('Uniekvoornaam')
        ->assertCanSeeTableRecords([$customer])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can search customers by email', function () {
    $customer = User::factory()->create(['email' => 'uniek@test.com']);
    $other = User::factory()->create(['email' => 'other@test.com']);

    Livewire::test(ListCustomers::class)
        ->searchTable('uniek@test.com')
        ->assertCanSeeTableRecords([$customer])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can bulk delete customers', function () {
    $customers = User::factory()->count(3)->create();

    Livewire::test(ListCustomers::class)
        ->callTableBulkAction(DeleteBulkAction::class, $customers);

    $customers->each(fn ($c) => $this->assertSoftDeleted($c));
});

// Create page

it('can render the create customer page', function () {
    Livewire::test(CreateCustomer::class)
        ->assertSuccessful();
});

it('can create a customer', function () {
    Livewire::test(CreateCustomer::class)
        ->fillForm([
            'first_name' => 'Jan',
            'last_name'  => 'Janssen',
            'email'      => 'jan@example.com',
            'phone'      => '+31612345678',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'first_name' => 'Jan',
        'last_name'  => 'Janssen',
        'email'      => 'jan@example.com',
    ]);
});

it('validates required fields on customer create', function () {
    Livewire::test(CreateCustomer::class)
        ->fillForm([
            'first_name' => null,
            'last_name'  => null,
            'email'      => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required',
        ]);
});

it('validates unique email on customer create', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    Livewire::test(CreateCustomer::class)
        ->fillForm([
            'first_name' => 'Jan',
            'last_name'  => 'Janssen',
            'email'      => 'existing@example.com',
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'unique']);
});

it('validates email format on customer create', function () {
    Livewire::test(CreateCustomer::class)
        ->fillForm([
            'first_name' => 'Jan',
            'last_name'  => 'Janssen',
            'email'      => 'not-an-email',
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'email']);
});

// View page

it('can render the view customer page', function () {
    $customer = User::factory()->create();

    Livewire::test(ViewCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit customer page', function () {
    $customer = User::factory()->create();

    Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve customer data on the edit page', function () {
    $customer = User::factory()->create([
        'first_name' => 'Jan',
        'last_name'  => 'Janssen',
        'email'      => 'jan@example.com',
    ]);

    Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])
        ->assertSchemaStateSet([
            'first_name' => 'Jan',
            'last_name'  => 'Janssen',
            'email'      => 'jan@example.com',
        ]);
});

it('can update a customer', function () {
    $customer = User::factory()->create(['first_name' => 'Oud']);

    Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])
        ->fillForm(['first_name' => 'Nieuw'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($customer->refresh()->first_name)->toBe('Nieuw');
});

it('allows editing own email (unique ignores current record)', function () {
    $customer = User::factory()->create(['email' => 'jan@example.com']);

    Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])
        ->fillForm(['email' => 'jan@example.com'])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('rejects duplicate email on customer update', function () {
    User::factory()->create(['email' => 'taken@example.com']);
    $customer = User::factory()->create(['email' => 'mine@example.com']);

    Livewire::test(EditCustomer::class, ['record' => $customer->getRouteKey()])
        ->fillForm(['email' => 'taken@example.com'])
        ->call('save')
        ->assertHasFormErrors(['email' => 'unique']);
});

// Reservations relation manager

it('can render the customer reservations relation manager', function () {
    $customer = User::factory()->create();

    Livewire::test(ReservationsRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass'   => EditCustomer::class,
    ])
        ->assertSuccessful();
});

it('can list reservations in the customer reservations relation manager', function () {
    $customer = User::factory()->create();
    $reservations = Reservation::factory()->count(2)->create(['customer_id' => $customer->id]);

    Livewire::test(ReservationsRelationManager::class, [
        'ownerRecord' => $customer,
        'pageClass'   => EditCustomer::class,
    ])
        ->assertCanSeeTableRecords($reservations);
});

// Authorization

it('prevents customers from accessing the customers list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListCustomers::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load customers index via HTTP', function () {
    $this->get(CustomerResource::getUrl('index'))->assertOk();
});

it('admin can load create customer page via HTTP', function () {
    $this->get(CustomerResource::getUrl('create'))->assertOk();
});

it('admin can load view customer page via HTTP', function () {
    $customer = User::factory()->create();
    $this->get(CustomerResource::getUrl('view', ['record' => $customer]))->assertOk();
});

it('admin can load edit customer page via HTTP', function () {
    $customer = User::factory()->create();
    $this->get(CustomerResource::getUrl('edit', ['record' => $customer]))->assertOk();
});

it('viewing a non-customer user via customers resource returns 404', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->get(CustomerResource::getUrl('view', ['record' => $employee]))->assertNotFound();
});

it('unauthenticated user is redirected to login from customers', function () {
    Auth::logout();
    $this->get(CustomerResource::getUrl('index'))->assertRedirect('/admin/login');
});
