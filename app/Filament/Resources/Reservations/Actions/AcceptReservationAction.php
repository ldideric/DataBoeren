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
            ->label('Accept')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Accept pending reservation')
            ->modalDescription('Confirm this reservation and mark any pending on-site payment as paid. The customer receives a confirmation email.')
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
            ->successNotificationTitle('Reservation accepted');
    }
}
