<?php

namespace App\Filament\Resources\Reservations\Actions;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\Facades\Auth;

class CancelReservationAction
{
    public static function make(): Action
    {
        return Action::make('cancel')
            ->label(__('reservation.actions.cancel.label'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (Reservation $record): bool => $record->status !== ReservationStatus::Cancelled)
            ->schema([
                Textarea::make('cancellation_reason')
                    ->label(__('reservation.actions.cancel.reason'))
                    ->required()
                    ->maxLength(255),
            ])
            ->action(function (array $data, Reservation $record): void {
                // Triggers ReservationObserver -> BookingCancelled email.
                $record->update([
                    'status' => ReservationStatus::Cancelled,
                    'cancelled_at' => now(),
                    'cancellation_reason' => $data['cancellation_reason'],
                    'cancelled_by_user_id' => Auth::id(),
                ]);
            })
            ->successNotificationTitle(__('reservation.actions.cancel.success'));
    }
}
