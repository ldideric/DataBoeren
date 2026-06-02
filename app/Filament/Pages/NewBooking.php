<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Auth\Services\SignedUrlGenerator;
use App\Booking\Actions\RecordCashPayment;
use App\Booking\Actions\ResolveBookingExtras;
use App\Booking\BookingValidator;
use App\Booking\Queries\FindAvailableCampsite;
use App\Enums\CheckoutMethod;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Filament\Pages\Schemas\BookingForm;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Mail\AwaitingPayment;
use App\Mail\BookingReceived;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Reservation;
use App\Models\User;
use App\Pricing\Actions\CalculatePrice;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class NewBooking extends Page
{
    protected string $view = 'filament.pages.new-booking';

    protected static ?string $title = 'New Booking';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::CalendarDateRange;

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return BookingForm::configure($schema);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save'),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $method = $data['payment_method'] instanceof CheckoutMethod
            ? $data['payment_method']
            : CheckoutMethod::from($data['payment_method']);

        $reservation = DB::transaction(fn (): Reservation => $this->createReservation($data, $method));

        $this->settle($reservation, $method);
    }

    private function createReservation(array $data, CheckoutMethod $method): Reservation
    {
        $customer = (bool) ($data['existing_customer'] ?? false)
            ? User::findOrFail($data['customer_id'])
            : User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name'  => $data['last_name'],
                    'phone'      => $data['phone'],
                    'role'       => UserRole::Customer,
                ],
            );

        $checkIn  = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);

        $campsite = Campsite::findOrFail($data['campsite_id']);

        app(BookingValidator::class)->validateCapacity(
            $campsite,
            (int) $data['num_adults'],
            (int) $data['num_children'],
            (int) $data['num_vehicles'],
            'data.',
        );

        try {
            app(FindAvailableCampsite::class)->handle(
                $data['campsite_id'],
                (int) $data['num_adults'] + (int) $data['num_children'],
                (int) $data['num_vehicles'],
                $checkIn,
                $checkOut,
            );
        } catch (ValidationException) {
            throw ValidationException::withMessages([
                'data.campsite_id' => 'Deze plek is niet (meer) beschikbaar voor deze data.',
            ]);
        }

        $coupon = ! empty($data['coupon_id'])
            ? Coupon::query()->lockForUpdate()->find($data['coupon_id'])
            : null;

        if ($coupon !== null && ! $coupon->isRedeemable()) {
            throw ValidationException::withMessages([
                'data.coupon_id' => 'This coupon is expired or has reached its usage limit.',
            ]);
        }

        $reservation = Reservation::create([
            'customer_id'       => $customer->id,
            'campsite_id'       => $data['campsite_id'],
            'booked_by_user_id' => Auth::id(),
            'coupon_id'         => $coupon?->id,
            'source'            => ReservationSource::Employee,
            'status'            => $method === CheckoutMethod::CashPaid
                ? ReservationStatus::Confirmed
                : ReservationStatus::Pending,
            'check_in'          => $checkIn,
            'check_out'         => $checkOut,
            'num_adults'        => (int) $data['num_adults'],
            'num_children'      => (int) $data['num_children'],
            'num_vehicles'      => (int) $data['num_vehicles'],
        ]);

        $quantities = collect($data['extras'] ?? [])
            ->mapWithKeys(fn (array $row): array => [(string) $row['extra_id'] => (int) $row['quantity']])
            ->all();

        $selections = app(ResolveBookingExtras::class)->resolve($quantities, $checkIn, $checkOut);
        $nights = (int) $checkIn->diffInDays($checkOut);

        foreach ($selections as $line) {
            $reservation->extras()->create([
                'extra_id'   => $line['extra']->id,
                'quantity'   => $line['quantity'],
                'unit_price' => $line['extra']->price,
                'subtotal'   => CalculatePrice::lineSubtotal($line['extra'], $line['quantity'], $nights),
            ]);
        }

        try {
            $summary = app(CalculatePrice::class)->calculate($reservation, $selections);
            $summary->save();
            $reservation->setRelation('orderSummary', $summary);
        } catch (RuntimeException) {
            throw ValidationException::withMessages([
                'data.check_in' => 'No pricing set for these dates.',
            ]);
        }

        $coupon?->increment('uses_count');

        $this->recordPayment($reservation, $method);

        return $reservation;
    }

    private function recordPayment(Reservation $reservation, CheckoutMethod $method): void
    {
        match ($method) {
            CheckoutMethod::CashPaid => $reservation->payments()->create([
                'amount'  => $reservation->orderSummary->total,
                'status'  => PaymentStatus::Paid,
                'method'  => PaymentMethod::Cash,
                'paid_at' => now(),
            ]),
            CheckoutMethod::PayOnArrival => app(RecordCashPayment::class)->handle($reservation),
            CheckoutMethod::CardNow, CheckoutMethod::SendLink => null,
        };
    }

    private function settle(Reservation $reservation, CheckoutMethod $method): void
    {
        $urls = app(SignedUrlGenerator::class);

        if ($method === CheckoutMethod::CardNow) {
            $this->redirect($urls->payment($reservation));

            return;
        }

        match ($method) {
            CheckoutMethod::CashPaid => null,
            CheckoutMethod::PayOnArrival => Mail::to($reservation->customer->email)
                ->send(new BookingReceived($reservation, $urls->bookings($reservation->customer))),
            CheckoutMethod::SendLink => Mail::to($reservation->customer->email)
                ->send(new AwaitingPayment($reservation, $urls->payment($reservation))),
            CheckoutMethod::CardNow => null,
        };

        Notification::make()
            ->title('Booking created successfully')
            ->success()
            ->send();

        $this->redirect(ReservationResource::getUrl('view', ['record' => $reservation]));
    }
}
