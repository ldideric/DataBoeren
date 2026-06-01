<?php

declare(strict_types=1);

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Payments\Pages\EditPayment;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->withRole(UserRole::Admin)->create();
    $this->actingAs($this->admin);
});

// List page

it('can render the payments list page', function () {
    Livewire::test(ListPayments::class)
        ->assertSuccessful();
});

it('can list payments', function () {
    $payments = Payment::factory()->count(3)->create();

    Livewire::test(ListPayments::class)
        ->assertCanSeeTableRecords($payments);
});

it('can render payment table columns', function () {
    Payment::factory()->create();

    Livewire::test(ListPayments::class)
        ->assertCanRenderTableColumn('reservation.customer.email')
        ->assertCanRenderTableColumn('reservation.check_in')
        ->assertCanRenderTableColumn('amount')
        ->assertCanRenderTableColumn('status')
        ->assertCanRenderTableColumn('method')
        ->assertCanRenderTableColumn('paid_at');
});

it('can search payments by customer email', function () {
    $customer = User::factory()->create(['email' => 'uniek@test.com']);
    $reservation = Reservation::factory()->create(['customer_id' => $customer->id]);
    $payment = Payment::factory()->create(['reservation_id' => $reservation->id]);
    $other = Payment::factory()->create();

    Livewire::test(ListPayments::class)
        ->searchTable('uniek@test.com')
        ->assertCanSeeTableRecords([$payment])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can sort payments by amount', function () {
    $payments = Payment::factory()->count(3)->create();

    Livewire::test(ListPayments::class)
        ->sortTable('amount')
        ->assertCanSeeTableRecords($payments->sortBy('amount'), inOrder: true);
});

it('can sort payments by paid_at', function () {
    $payments = Payment::factory()->count(3)->create();

    Livewire::test(ListPayments::class)
        ->sortTable('paid_at')
        ->assertCanSeeTableRecords($payments->sortBy('paid_at'), inOrder: true);
});

it('can filter payments by status', function () {
    $paid = Payment::factory()->create(['status' => PaymentStatus::Paid]);
    $pending = Payment::factory()->pending()->create();

    Livewire::test(ListPayments::class)
        ->filterTable('status', PaymentStatus::Paid->value)
        ->assertCanSeeTableRecords([$paid])
        ->assertCanNotSeeTableRecords([$pending]);
});

// View page

it('can render the view payment page', function () {
    $payment = Payment::factory()->create();

    Livewire::test(ViewPayment::class, ['record' => $payment->getRouteKey()])
        ->assertSuccessful();
});

// Edit page

it('can render the edit payment page', function () {
    $payment = Payment::factory()->create();

    Livewire::test(EditPayment::class, ['record' => $payment->getRouteKey()])
        ->assertSuccessful();
});

it('can retrieve payment data on the edit page', function () {
    $payment = Payment::factory()->create(['status' => PaymentStatus::Paid]);

    Livewire::test(EditPayment::class, ['record' => $payment->getRouteKey()])
        ->assertSchemaStateSet([
            'status' => PaymentStatus::Paid,
        ]);
});

it('can update a payment status', function () {
    $payment = Payment::factory()->pending()->create();

    Livewire::test(EditPayment::class, ['record' => $payment->getRouteKey()])
        ->fillForm(['status' => PaymentStatus::Paid->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($payment->refresh()->status)->toBe(PaymentStatus::Paid);
});

it('validates required status on payment update', function () {
    $payment = Payment::factory()->create();

    Livewire::test(EditPayment::class, ['record' => $payment->getRouteKey()])
        ->fillForm(['status' => null])
        ->call('save')
        ->assertHasFormErrors(['status' => 'required']);
});

// Authorization

it('prevents customers from accessing the payments list', function () {
    $customer = User::factory()->create();
    $this->actingAs($customer);

    Livewire::test(ListPayments::class)
        ->assertForbidden();
});

it('prevents employees from accessing the payments list', function () {
    $employee = User::factory()->withRole(UserRole::Employee)->create();
    $this->actingAs($employee);

    Livewire::test(ListPayments::class)
        ->assertForbidden();
});

// HTTP routes

it('admin can load payments index via HTTP', function () {
    $this->get(PaymentResource::getUrl('index'))->assertOk();
});

it('admin can load view payment page via HTTP', function () {
    $payment = Payment::factory()->create();
    $this->get(PaymentResource::getUrl('view', ['record' => $payment]))->assertOk();
});

it('admin can load edit payment page via HTTP', function () {
    $payment = Payment::factory()->create();
    $this->get(PaymentResource::getUrl('edit', ['record' => $payment]))->assertOk();
});

it('unauthenticated user is redirected to login from payments', function () {
    Auth::logout();
    $this->get(PaymentResource::getUrl('index'))->assertRedirect('/admin/login');
});
