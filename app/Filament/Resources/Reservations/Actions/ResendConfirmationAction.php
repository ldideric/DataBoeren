<?php

namespace App\Filament\Resources\Reservations\Actions;

use App\Enums\ReservationStatus;
use App\Mail\BookingConfirmed;
use App\Models\Reservation;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;

class ResendConfirmationAction
{
    public static function make(): Action
    {
        return Action::make('resendConfirmation')
            ->label('Resend confirmation')
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->visible(fn (Reservation $record): bool => $record->status === ReservationStatus::Confirmed)
            ->action(fn (Reservation $record) => Mail::to($record->customer->email)->send(new BookingConfirmed($record)))
            ->successNotificationTitle('Confirmation email re-sent');
    }
}
