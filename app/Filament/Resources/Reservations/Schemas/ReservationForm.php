<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'id')
                    ->required(),
                Select::make('campsite_id')
                    ->relationship('campsite', 'name')
                    ->required(),
                TextInput::make('booked_by_user_id'),
                Select::make('coupon_id')
                    ->relationship('coupon', 'title'),
                Select::make('source')
                    ->options(ReservationSource::class)
                    ->required(),
                DatePicker::make('check_in')
                    ->required(),
                DatePicker::make('check_out')
                    ->required(),
                TextInput::make('num_adults')
                    ->required()
                    ->numeric(),
                TextInput::make('num_children')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('num_vehicles')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(ReservationStatus::class)
                    ->required(),
                DateTimePicker::make('cancelled_at'),
                Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
                TextInput::make('cancelled_by_user_id'),
            ]);
    }
}
