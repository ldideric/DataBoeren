<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Booking\Actions\ResolveBookingExtras;
use App\Booking\Queries\FindAvailableCampsite;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\Reservation;
use App\Models\User;
use App\Pricing\Actions\CalculatePrice;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class NewBooking extends Page
{
    protected string $view = 'filament.pages.new-booking';

    protected static ?string $title = 'New Booking';

    protected static string|null|\BackedEnum $navigationIcon = Heroicon::CalendarDateRange;

    protected static ?int $navigationSort = 2;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Customer')
                    ->schema([
                        Toggle::make('existing_customer')
                            ->label('Existing customer')
                            ->live()
                            ->default(false),

                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(User::query()->pluck('email', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Get $get): bool => (bool) $get('existing_customer')),

                        \Filament\Schemas\Components\Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('last_name')
                                    ->label('Last name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(32),
                            ])
                            ->visible(fn (Get $get): bool => ! (bool) $get('existing_customer')),
                    ]),

                Section::make('Stay')
                    ->schema([
                        Select::make('campsite_id')
                            ->label('Campsite')
                            ->options(Campsite::query()->pluck('name', 'id'))
                            ->required(),

                        DatePicker::make('check_in')
                            ->label('Check-in')
                            ->required()
                            ->minDate(today())
                            ->live(),

                        DatePicker::make('check_out')
                            ->label('Check-out')
                            ->required()
                            ->minDate(fn (Get $get): ?string => $get('check_in'))
                            ->afterOrEqual(fn (Get $get): ?string => $get('check_in') ?: 'today'),

                        TextInput::make('num_adults')
                            ->label('Adults')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        TextInput::make('num_children')
                            ->label('Children')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        TextInput::make('num_vehicles')
                            ->label('Vehicles')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Select::make('coupon_id')
                            ->label('Coupon')
                            ->options(Coupon::query()->pluck('code', 'id'))
                            ->searchable()
                            ->nullable(),
                    ]),

                Section::make('Extras')
                    ->schema([
                        Repeater::make('extras')
                            ->label('Extras')
                            ->schema([
                                Select::make('extra_id')
                                    ->label('Extra')
                                    ->options(Extra::orderBy('name')->pluck('name', 'id'))
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                            ])
                            ->nullable()
                            ->addActionLabel('Add extra')
                            ->columns(2),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->fullWidth($this->hasFullWidthFormActions())
                            ->sticky($this->areFormActionsSticky())
                            ->key('form-actions'),
                    ]),
            ]);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Create Booking')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        \Illuminate\Support\Facades\DB::transaction(function () use ($data): void {
            // 1. Resolve customer
            if ((bool) ($data['existing_customer'] ?? false)) {
                $customer = User::findOrFail($data['customer_id']);
            } else {
                $customer = User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        'first_name' => $data['first_name'],
                        'last_name'  => $data['last_name'],
                        'phone'      => $data['phone'],
                        'role'       => UserRole::Customer,
                    ],
                );
            }

            // 2. Parse dates
            $checkIn  = Carbon::parse($data['check_in']);
            $checkOut = Carbon::parse($data['check_out']);

            // 3. Validate campsite availability (throws ValidationException on failure)
            app(FindAvailableCampsite::class)->handle(
                $data['campsite_id'],
                (int) $data['num_adults'] + (int) $data['num_children'],
                (int) $data['num_vehicles'],
                $checkIn,
                $checkOut,
            );

            // 4. Create the reservation
            $reservation = Reservation::create([
                'customer_id'      => $customer->id,
                'campsite_id'      => $data['campsite_id'],
                'booked_by_user_id' => Auth::id(),
                'coupon_id'        => $data['coupon_id'] ?? null,
                'source'           => ReservationSource::Employee,
                'status'           => ReservationStatus::Confirmed,
                'check_in'         => $checkIn,
                'check_out'        => $checkOut,
                'num_adults'       => (int) $data['num_adults'],
                'num_children'     => (int) $data['num_children'],
                'num_vehicles'     => (int) $data['num_vehicles'],
            ]);

            // 5. Resolve and persist extras
            $rawExtras = $data['extras'] ?? [];
            $quantities = collect($rawExtras)
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

            // 6. Calculate and save the order summary
            try {
                app(CalculatePrice::class)->calculate($reservation, $selections)->save();
            } catch (RuntimeException) {
                throw ValidationException::withMessages([
                    'check_in' => 'No pricing set for these dates.',
                ]);
            }

            // 7. Success notification and redirect
            Notification::make()
                ->title('Booking created successfully')
                ->success()
                ->send();

            $this->redirect(ReservationResource::getUrl('view', ['record' => $reservation]));
        });
    }
}
