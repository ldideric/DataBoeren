<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('campsite_id')
                    ->relationship('campsite', 'name')
                    ->required(),
                DatePicker::make('check_in')
                    ->required(),
                DatePicker::make('check_out')
                    ->required()
                    ->afterOrEqual(fn (Get $get) => $get('check_in') ?: 'today'),
                TextInput::make('num_adults')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('num_children')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(0),
                TextInput::make('num_vehicles')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Select::make('coupon_id')
                    ->relationship('coupon', 'code')
                    ->searchable()
                    ->nullable(),
                Hidden::make('source')
                    ->default(ReservationSource::Employee->value),
                Select::make('status')
                    ->options(ReservationStatus::class)
                    ->default(ReservationStatus::Confirmed->value)
                    ->required(),
                Textarea::make('cancellation_reason')
                    ->visible(fn (Get $get) => $get('status') === ReservationStatus::Cancelled->value)
                    ->required(fn (Get $get) => $get('status') === ReservationStatus::Cancelled->value)
                    ->columnSpanFull(),
            ]);
    }
}
