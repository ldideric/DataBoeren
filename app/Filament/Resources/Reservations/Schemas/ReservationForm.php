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
                    ->label(__('reservation.fields.customer'))
                    ->relationship('customer', 'email')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('campsite_id')
                    ->label(__('reservation.fields.campsite'))
                    ->relationship('campsite', 'name')
                    ->required(),
                DatePicker::make('check_in')
                    ->label(__('reservation.fields.check_in'))
                    ->required(),
                DatePicker::make('check_out')
                    ->label(__('reservation.fields.check_out'))
                    ->required()
                    ->afterOrEqual(fn (Get $get) => $get('check_in') ?: 'today'),
                TextInput::make('num_adults')
                    ->label(__('reservation.fields.num_adults'))
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('num_children')
                    ->label(__('reservation.fields.num_children'))
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(0),
                TextInput::make('num_vehicles')
                    ->label(__('reservation.fields.num_vehicles'))
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                Select::make('coupon_id')
                    ->label(__('reservation.fields.coupon'))
                    ->relationship('coupon', 'code')
                    ->searchable()
                    ->nullable(),
                Hidden::make('source')
                    ->default(ReservationSource::Employee->value),
                Select::make('status')
                    ->label(__('common.status'))
                    ->options(ReservationStatus::class)
                    ->default(ReservationStatus::Confirmed->value)
                    ->required(),
                Textarea::make('cancellation_reason')
                    ->label(__('reservation.fields.cancellation_reason'))
                    ->visible(fn (Get $get) => $get('status') === ReservationStatus::Cancelled->value)
                    ->required(fn (Get $get) => $get('status') === ReservationStatus::Cancelled->value)
                    ->columnSpanFull(),
            ]);
    }
}
