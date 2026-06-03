<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('reservation.id')
                    ->label('Reservation')
                    ->url(fn ($record) => ReservationResource::getUrl('view', ['record' => $record->reservation_id])),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('method'),
                TextEntry::make('stripe_session_id')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
