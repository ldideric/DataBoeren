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
                Section::make(__('reservation.sections.reservation'))
                    ->schema([
                        TextEntry::make('customer.email')
                            ->label(__('reservation.fields.customer'))
                            ->url(fn (Reservation $record) => CustomerResource::getUrl('view', ['record' => $record->customer_id])),
                        TextEntry::make('campsite.name')
                            ->label(__('reservation.fields.campsite'))
                            ->url(fn (Reservation $record) => CampsiteResource::getUrl('view', ['record' => $record->campsite_id])),
                        TextEntry::make('check_in')
                            ->label(__('reservation.fields.check_in'))
                            ->date('d/m/Y'),
                        TextEntry::make('check_out')
                            ->label(__('reservation.fields.check_out'))
                            ->date('d/m/Y'),
                        TextEntry::make('num_adults')
                            ->label(__('reservation.fields.num_adults')),
                        TextEntry::make('num_children')
                            ->label(__('reservation.fields.num_children')),
                        TextEntry::make('num_vehicles')
                            ->label(__('reservation.fields.num_vehicles')),
                        TextEntry::make('status')
                            ->label(__('common.status'))
                            ->badge(),
                        TextEntry::make('source')
                            ->label(__('reservation.fields.source'))
                            ->badge(),
                        TextEntry::make('coupon.code')
                            ->label(__('reservation.fields.coupon'))
                            ->placeholder('-')
                            ->url(fn (Reservation $record) => $record->coupon_id
                                ? CouponResource::getUrl('view', ['record' => $record->coupon_id])
                                : null),
                        TextEntry::make('cancellation_reason')
                            ->label(__('reservation.fields.cancellation_reason'))
                            ->placeholder('-')
                            ->columnSpanFull()
                            ->visible(fn (Reservation $record): bool => $record->status->value === 'cancelled'),
                    ])
                    ->columns(3),

                Section::make(__('reservation.sections.order_summary'))
                    ->schema([
                        TextEntry::make('orderSummary.season_name')
                            ->label(__('reservation.order_summary.season_name')),
                        TextEntry::make('orderSummary.num_nights')
                            ->label(__('reservation.order_summary.num_nights')),
                        TextEntry::make('orderSummary.nightly_rate')
                            ->label(__('reservation.order_summary.nightly_rate'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.per_adult_rate')
                            ->label(__('reservation.order_summary.per_adult_rate'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.per_child_rate')
                            ->label(__('reservation.order_summary.per_child_rate'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.last_minute_discount')
                            ->label(__('reservation.order_summary.last_minute_discount'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
                            ->visible(fn (Reservation $record) => (bool) $record->orderSummary?->last_minute_applied),
                        TextEntry::make('orderSummary.coupon_discount')
                            ->label(__('reservation.order_summary.coupon_discount'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.extras_total')
                            ->label(__('reservation.order_summary.extras_total'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                        TextEntry::make('orderSummary.total')
                            ->label(__('reservation.order_summary.total'))
                            ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
                            ->weight(FontWeight::Bold),
                    ])
                    ->columns(3),
            ]);
    }
}
