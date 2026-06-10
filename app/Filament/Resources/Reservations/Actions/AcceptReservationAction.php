<?php

namespace App\Filament\Resources\Reservations\Actions;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Actions\Action;

class AcceptReservationAction
{
    public static function make(): Action
    {
        return Action::make('accept')
            ->label(__('reservation.actions.accept.label'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('reservation.actions.accept.modal_heading'))
            ->modalDescription(__('reservation.actions.accept.modal_description'))
            ->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Pending)
            ->action(function (Reservation $record): void {
                $record->payments()
                    ->where('method', PaymentMethod::Cash)
                    ->where('status', PaymentStatus::Pending)
                    ->update([
                        'status' => PaymentStatus::Paid,
                        'paid_at' => now(),
                    ]);

                // Triggers ReservationObserver -> BookingConfirmed email.
                $record->update(['status' => ReservationStatus::Confirmed]);
            })
            ->successNotificationTitle(__('reservation.actions.accept.success'));
    }
}
