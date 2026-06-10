<?php

declare(strict_types=1);

namespace App\Filament\Pages\Schemas;

use App\Booking\BookingValidator;
use App\Booking\Queries\PreviewBookingPrice;
use App\Enums\CheckoutMethod;
use App\Enums\UserRole;
use Closure;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Extra;
use App\Models\OrderSummary;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class BookingForm
{
    private static ?string $previewKey = null;

    private static ?OrderSummary $previewOrder = null;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([self::wizard()]);
    }

    public static function wizard(): Wizard
    {
        return Wizard::make([
            self::customerStep(),
            self::stayStep(),
            self::extrasStep(),
            self::summaryStep(),
        ])
            ->submitAction(self::submitButton());
    }

    // Steps

    public static function customerStep(): Step
    {
        return Step::make(__('booking.steps.customer'))
            ->icon(Heroicon::User)
            ->schema([
                self::existingCustomer(),
                self::customerSelect(),
                self::customerDetails(),
            ]);
    }

    public static function stayStep(): Step
    {
        return Step::make(__('booking.steps.stay'))
            ->icon(Heroicon::CalendarDays)
            ->columns(2)
            ->schema([
                self::campsite()->columnSpanFull(),
                self::checkIn(),
                self::checkOut(),
                Grid::make(2)
                    ->schema([
                        self::numAdults(),
                        self::numChildren(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function extrasStep(): Step
    {
        return Step::make(__('booking.steps.extras'))
            ->icon(Heroicon::ShoppingBag)
            ->schema([
                self::extras(),
            ]);
    }

    public static function summaryStep(): Step
    {
        return Step::make(__('booking.steps.summary'))
            ->icon(Heroicon::Banknotes)
            ->schema([
                self::coupon(),
                self::priceSummary(),
                self::paymentMethod(),
            ]);
    }

    // Customer fields

    public static function existingCustomer(): Toggle
    {
        return Toggle::make('existing_customer')
            ->label(__('booking.fields.existing_customer'))
            ->live()
            ->default(false);
    }

    public static function customerSelect(): Select
    {
        return Select::make('customer_id')
            ->label(__('booking.fields.customer'))
            ->options(User::whereRole(UserRole::Customer)->pluck('email', 'id'))
            ->searchable()
            ->preload()
            ->required()
            ->visible(fn (Get $get): bool => (bool) $get('existing_customer'));
    }

    public static function customerDetails(): Grid
    {
        return Grid::make(2)
            ->schema([
                self::firstName(),
                self::lastName(),
                self::email(),
                self::phone(),
            ])
            ->visible(fn (Get $get): bool => ! (bool) $get('existing_customer'));
    }

    public static function firstName(): TextInput
    {
        return TextInput::make('first_name')
            ->label(__('common.first_name'))
            ->required()
            ->maxLength(255);
    }

    public static function lastName(): TextInput
    {
        return TextInput::make('last_name')
            ->label(__('common.last_name'))
            ->required()
            ->maxLength(255);
    }

    public static function email(): TextInput
    {
        return TextInput::make('email')
            ->label(__('common.email'))
            ->email()
            ->required()
            ->maxLength(255);
    }

    public static function phone(): TextInput
    {
        return TextInput::make('phone')
            ->label(__('common.phone'))
            ->tel()
            ->required()
            ->maxLength(32);
    }

    // Stay fields

    public static function campsite(): Select
    {
        return Select::make('campsite_id')
            ->label(__('booking.fields.campsite'))
            ->options(Campsite::query()->pluck('name', 'id'))
            ->required();
    }

    public static function checkIn(): DatePicker
    {
        return DatePicker::make('check_in')
            ->label(__('booking.fields.check_in'))
            ->required()
            ->minDate(today())
            ->live();
    }

    public static function checkOut(): DatePicker
    {
        return DatePicker::make('check_out')
            ->label(__('booking.fields.check_out'))
            ->required()
            ->minDate(fn (Get $get): ?string => $get('check_in'))
            // Strictly after check-in: a same-day, zero-night stay isn't a booking.
            ->after(fn (Get $get): string => $get('check_in') ?: 'today');
    }

    public static function numAdults(): TextInput
    {
        return TextInput::make('num_adults')
            ->label(__('booking.fields.adults'))
            ->numeric()
            ->integer()
            ->minValue(1)
            ->default(1)
            ->required()
            ->rule(self::peopleCapacityRule());
    }

    public static function numChildren(): TextInput
    {
        return TextInput::make('num_children')
            ->label(__('booking.fields.children'))
            ->numeric()
            ->integer()
            ->minValue(0)
            ->default(0)
            ->required()
            ->rule(self::peopleCapacityRule());
    }

    /**
     * Live validation that the guests fit the chosen campsite, so the Stay step
     * blocks before advancing. Filament injects Get into the outer closure and
     * uses the returned closure as the field's validation rule.
     */
    private static function peopleCapacityRule(): Closure
    {
        return static fn (Get $get): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($get): void {
            $campsite = self::selectedCampsite($get);

            if ($campsite !== null && ($message = app(BookingValidator::class)->peopleError(
                $campsite,
                (int) $get('num_adults'),
                (int) $get('num_children'),
            )) !== null) {
                $fail($message);
            }
        };
    }

    private static function selectedCampsite(Get $get): ?Campsite
    {
        $campsiteId = $get('campsite_id');

        return blank($campsiteId) ? null : Campsite::find($campsiteId);
    }

    // Summary & payment fields

    public static function coupon(): Select
    {
        return Select::make('coupon_id')
            ->label(__('booking.fields.coupon'))
            ->options(Coupon::query()->redeemable()->orderBy('code')->pluck('code', 'id'))
            ->helperText(__('booking.fields.coupon_helper'))
            ->searchable()
            ->live()
            ->nullable();
    }

    public static function priceSummary(): Section
    {
        return Section::make(__('booking.summary.heading'))
            ->schema([
                TextEntry::make('summary_empty')
                    ->hiddenLabel()
                    ->state(__('booking.summary.empty'))
                    ->visible(fn (Get $get): bool => self::previewOrder($get) === null),

                TextEntry::make('summary_stay')
                    ->label(fn (Get $get): string => self::stayLabel($get))
                    ->money('EUR', divideBy: 100, locale: 'nl')
                    ->state(fn (Get $get): ?int => ($order = self::previewOrder($get)) ? self::accommodation($order) : null)
                    ->visible(fn (Get $get): bool => self::previewOrder($get) !== null),

                TextEntry::make('summary_last_minute')
                    ->label(__('booking.summary.last_minute'))
                    ->money('EUR', divideBy: 100, locale: 'nl')
                    ->state(fn (Get $get): ?int => ($d = self::previewOrder($get)?->last_minute_discount) ? -$d : null)
                    ->visible(fn (Get $get): bool => (bool) self::previewOrder($get)?->last_minute_discount),

                TextEntry::make('summary_coupon')
                    ->label(__('booking.summary.coupon'))
                    ->money('EUR', divideBy: 100, locale: 'nl')
                    ->state(fn (Get $get): ?int => ($d = self::previewOrder($get)?->coupon_discount) ? -$d : null)
                    ->visible(fn (Get $get): bool => (bool) self::previewOrder($get)?->coupon_discount),

                TextEntry::make('summary_extras')
                    ->label(__('booking.summary.extras'))
                    ->money('EUR', divideBy: 100, locale: 'nl')
                    ->state(fn (Get $get): ?int => self::previewOrder($get)?->extras_total ?: null)
                    ->visible(fn (Get $get): bool => (bool) self::previewOrder($get)?->extras_total),

                TextEntry::make('summary_total')
                    ->label(__('booking.summary.total'))
                    ->weight(FontWeight::Bold)
                    ->money('EUR', divideBy: 100, locale: 'nl')
                    ->state(fn (Get $get): ?int => self::previewOrder($get)?->total)
                    ->visible(fn (Get $get): bool => self::previewOrder($get) !== null),
            ]);
    }

    /**
     * Live price for the current form state, memoised for one render pass so the
     * summary's many entries don't each re-run the pricing queries.
     */
    private static function previewOrder(Get $get): ?OrderSummary
    {
        $data = [
            'campsite_id'  => $get('campsite_id'),
            'check_in'     => $get('check_in'),
            'check_out'    => $get('check_out'),
            'num_adults'   => $get('num_adults'),
            'num_children' => $get('num_children'),
            'coupon_id'    => $get('coupon_id'),
            'extras'       => $get('extras'),
        ];

        $key = md5(serialize($data));

        if ($key !== self::$previewKey) {
            self::$previewKey = $key;
            self::$previewOrder = app(PreviewBookingPrice::class)->fromFormData($data);
        }

        return self::$previewOrder;
    }

    private static function stayLabel(Get $get): string
    {
        $nights = self::previewOrder($get)?->num_nights ?? 0;

        return __('booking.summary.stay', [
            'count' => $nights,
            'unit'  => $nights === 1 ? __('booking.summary.night') : __('booking.summary.nights'),
        ]);
    }

    /** Pre-discount accommodation cost, derived from the frozen summary totals. */
    private static function accommodation(OrderSummary $order): int
    {
        return $order->total
            - (int) ($order->extras_total ?? 0)
            + (int) ($order->last_minute_discount ?? 0)
            + (int) ($order->coupon_discount ?? 0);
    }

    public static function paymentMethod(): Radio
    {
        return Radio::make('payment_method')
            ->label(__('booking.fields.payment'))
            ->options(CheckoutMethod::class)
            ->descriptions(
                collect(CheckoutMethod::cases())
                    ->mapWithKeys(fn (CheckoutMethod $method): array => [$method->value => $method->description()])
                    ->all()
            )
            ->default(CheckoutMethod::CashPaid->value)
            ->required();
    }

    // Extras fields

    public static function extras(): Repeater
    {
        return Repeater::make('extras')
            ->label(__('booking.fields.extras'))
            ->schema([
                self::extraSelect(),
                self::extraQuantity(),
            ])
            ->nullable()
            ->addActionLabel(__('booking.fields.add_extra'))
            ->columns(2);
    }

    public static function extraSelect(): Select
    {
        return Select::make('extra_id')
            ->label(__('booking.fields.extra'))
            ->options(Extra::orderBy('name')->pluck('name', 'id'))
            ->required();
    }

    public static function extraQuantity(): TextInput
    {
        return TextInput::make('quantity')
            ->label(__('booking.fields.quantity'))
            ->numeric()
            ->integer()
            ->minValue(1)
            ->default(1)
            ->required();
    }

    private static function submitButton(): HtmlString
    {
        return new HtmlString(Blade::render(
            '<x-filament::button type="submit">{{ $label }}</x-filament::button>',
            ['label' => __('booking.page.submit')]
        ));
    }
}
