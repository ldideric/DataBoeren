<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Filament\Resources\Campsites\CampsiteResource;
use App\Filament\Resources\Coupons\CouponResource;
use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Reservation;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Reservation')
                    ->schema([
                        TextEntry::make('customer.email')
                            ->label('Customer')
                            ->url(fn (Reservation $record) => CustomerResource::getUrl('view', ['record' => $record->customer_id])),
                        TextEntry::make('campsite.name')
                            ->label('Campsite')
                            ->url(fn (Reservation $record) => CampsiteResource::getUrl('view', ['record' => $record->campsite_id])),
                        TextEntry::make('check_in')
                            ->date('d/m/Y'),
                        TextEntry::make('check_out')
                            ->date('d/m/Y'),
                        TextEntry::make('num_adults')
                            ->label('Adults'),
                        TextEntry::make('num_children')
                            ->label('Children'),
                        TextEntry::make('num_vehicles')
                            ->label('Vehicles'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('source')
                            ->badge(),
                        TextEntry::make('coupon.code')
                            ->label('Coupon')
                            ->placeholder('-')
                            ->url(fn (Reservation $record) => $record->coupon_id
                                ? CouponResource::getUrl('view', ['record' => $record->coupon_id])
                                : null),
                        TextEntry::make('cancellation_reason')
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->visible(fn (Reservation $record): bool => $record->status->value === 'cancelled'),
                    ])
                    ->columns(3),

                Section::make('Order Summary')
                    ->schema([
                        TextEntry::make('orderSummary.season_name'),
                        TextEntry::make('orderSummary.num_nights'),
                        TextEntry::make('orderSummary.nightly_rate')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.per_adult_rate')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.per_child_rate')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.last_minute_discount')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
                            ->visible(fn (Reservation $record) => (bool) $record->orderSummary?->last_minute_applied),
                        TextEntry::make('orderSummary.coupon_discount')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.extras_total')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.total')
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(3),
            ]);
    }
}
