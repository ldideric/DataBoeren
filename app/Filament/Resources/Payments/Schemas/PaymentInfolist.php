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
                    ->label(__('common.id')),
                TextEntry::make('reservation.id')
                    ->label(__('payment.fields.reservation'))
                    ->url(fn ($record) => ReservationResource::getUrl('view', ['record' => $record->reservation_id])),
                TextEntry::make('amount')
                    ->label(__('payment.fields.amount'))
                    ->numeric(),
                TextEntry::make('status')
                    ->label(__('common.status'))
                    ->badge(),
                TextEntry::make('method')
                    ->label(__('payment.fields.method')),
                TextEntry::make('stripe_session_id')
                    ->label(__('payment.fields.stripe_session_id'))
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->label(__('payment.fields.paid_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
