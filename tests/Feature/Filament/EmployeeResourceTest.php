<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\Pages\ViewEmployee;
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

it('can render the employees list page', function () {
    Livewire::test(ListEmployees::class)
        ->assertSuccessful();
});

it('can list employees', function () {
    $employees = User::factory()->withRole(UserRole::Employee)->count(3)->create();

    Livewire::test(ListEmployees::class)
        ->assertCanSeeTableRecords($employees);
});

it('lists admin users in the employees list', function () {
    $admin = User::factory()->withRole(UserRole::Admin)->create();

    Livewire::test(ListEmployees::class)
        ->assertCanSeeTableRecords([$admin]);
});

it('does not show customers in the employees list', function () {
    $customer = User::factory()->create();
    $employee = User::factory()->withRole(UserRole::Employee)->create();

    Livewire::test(ListEmployees::class)
        ->assertCanSeeTableRecords([$employee])
        ->assertCanNotSeeTableRecords([$customer]);
});

it('can render employee table columns', function () {
    User::factory()->withRole(UserRole::Employee)->create();

    Livewire::test(ListEmployees::class)
        ->assertCanRenderTableColumn('first_name')
        ->assertCanRenderTableColumn('email')
        ->assertCanRenderTableColumn('role');
});

it('can search employees by name', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create([
        'first_name' => 'Uniekvoornaam',
        'last_name'  => 'Persoon',
    ]);
    $other = User::factory()->withRole(UserRole::Employee)->create([
        'first_name' => 'Anders',
        'last_name'  => 'Werknemer',
    ]);

    Livewire::test(ListEmployees::class)
        ->searchTable('Uniekvoornaam')
        ->assertCanSeeTableRecords([$employee])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can search employees by email', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create(['email' => 'uniek@bedrijf.com']);
    $other = User::factory()->withRole(UserRole::Employee)->create(['email' => 'ander@bedrijf.com']);

    Livewire::test(ListEmployees::class)
        ->searchTable('uniek@bedrijf.com')
        ->assertCanSeeTableRecords([$employee])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can bulk delete employees', function () {
    $employees = User::factory()->withRole(UserRole::Employee)->count(3)->create();

    Livewire::test(ListEmployees::class)
        ->callTableBulkAction(DeleteBulkAction::class, $employees);

    $employees->each(fn ($e) => $this->assertSoftDeleted($e));
});

// Create page

it('can render the create employee page', function () {
    Livewire::test(CreateEmployee::class)
        ->assertSuccessful();
});

it('can create an employee', function () {
    Livewire::test(CreateEmployee::class)
        ->fillForm([
            'first_name' => 'Piet',
            'last_name'  => 'Pietersen',
            'email'      => 'piet@bedrijf.com',
            'phone'      => '+31611112222',
            'role'       => UserRole::Employee->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'first_name' => 'Piet',
        'last_name'  => 'Pietersen',
        'email'      => 'piet@bedrijf.com',
    ]);
});

it('validates required fields on employee create', function () {
    Livewire::test(CreateEmployee::class)
        ->fillForm([
            'first_name' => null,
            'last_name'  => null,
            'email'      => null,
            'role'       => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'first_name' => 'required',
            'last_name'  => 'required',
            'email'      => 'required',
            'role'       => 'required',
        ]);
});

it('validates email format on employee create', function () {
    Livewire::test(CreateEmployee::class)
        ->fillForm([
            'first_name' => 'Piet',
            'last_name'  => 'Pietersen',
            'email'      => 'geen-email',
            'role'       => UserRole::Employee->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'email']);
});

it('validates unique email on employee create', function () {
    User::factory()->create(['email' => 'bestaand@bedrijf.com']);

    Livewire::test(CreateEmployee::class)
        ->fillForm([
            'first_name' => 'Piet',
            'last_name'  => 'Pietersen',
            'email'      => 'bestaand@bedrijf.com',
            'role'       => UserRole::Employee->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'unique']);
});

// View page

it('can render the view employee page', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();

    Livewire::test(ViewEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit employee page', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve employee data on the edit page', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create([
        'first_name' => 'Piet',
        'last_name'  => 'Pietersen',
        'email'      => 'piet@bedrijf.com',
    ]);

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertSchemaStateSet([
            'first_name' => 'Piet',
            'last_name'  => 'Pietersen',
            'email'      => 'piet@bedrijf.com',
        ]);
});

it('can update an employee', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create(['first_name' => 'Oud']);

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->fillForm(['first_name' => 'Nieuw'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($employee->refresh()->first_name)->toBe('Nieuw');
});

it('allows editing own email (unique ignores current record)', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create(['email' => 'piet@bedrijf.com']);

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->fillForm(['email' => 'piet@bedrijf.com'])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('rejects duplicate email on employee update', function () {
    User::factory()->create(['email' => 'bezet@bedrijf.com']);
    $employee = User::factory()->withRole(UserRole::Employee)->create(['email' => 'mijn@bedrijf.com']);

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->fillForm(['email' => 'bezet@bedrijf.com'])
        ->call('save')
        ->assertHasFormErrors(['email' => 'unique']);
});

it('prevents employees from accessing the employee edit page', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->actingAs($employee);

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertForbidden();
});

it('admin can change the role field', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();

    Livewire::test(EditEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertFormFieldEnabled('role');
});

// Authorization

it('prevents customers from accessing the employees list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListEmployees::class)
        ->assertForbidden();
});

it('prevents employees from accessing the employees list', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->actingAs($employee);

    Livewire::test(ListEmployees::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load employees index via HTTP', function () {
    $this->get(EmployeeResource::getUrl('index'))->assertOk();
});

it('admin can load create employee page via HTTP', function () {
    $this->get(EmployeeResource::getUrl('create'))->assertOk();
});

it('admin can load view employee page via HTTP', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->get(EmployeeResource::getUrl('view', ['record' => $employee]))->assertOk();
});

it('admin can load edit employee page via HTTP', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->get(EmployeeResource::getUrl('edit', ['record' => $employee]))->assertOk();
});

it('unauthenticated user is redirected to login from employees', function () {
    Auth::logout();
    $this->get(EmployeeResource::getUrl('index'))->assertRedirect('/admin/login');
});
