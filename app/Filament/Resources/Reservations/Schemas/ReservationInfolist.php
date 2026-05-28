<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Reservation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('customer.id')
                    ->label('Customer'),
                TextEntry::make('campsite.name')
                    ->label('Campsite'),
                TextEntry::make('booked_by_user_id')
                    ->placeholder('-'),
                TextEntry::make('coupon.title')
                    ->label('Coupon')
                    ->placeholder('-'),
                TextEntry::make('source')
                    ->badge(),
                TextEntry::make('check_in')
                    ->date(),
                TextEntry::make('check_out')
                    ->date(),
                TextEntry::make('num_adults')
                    ->numeric(),
                TextEntry::make('num_children')
                    ->numeric(),
                TextEntry::make('num_vehicles')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('cancelled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('cancellation_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancelled_by_user_id')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Reservation $record): bool => $record->trashed()),
            ]);
    }
}
