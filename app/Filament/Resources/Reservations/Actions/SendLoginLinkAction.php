<?php

namespace App\Filament\Resources\Reservations\Actions;

use App\Auth\Actions\SendBookingsLink;
use App\Models\Reservation;
use Filament\Actions\Action;

class SendLoginLinkAction
{
    public static function make(): Action
    {
        return Action::make('sendLoginLink')
            ->label('Send login link')
            ->icon('heroicon-o-paper-airplane')
            ->requiresConfirmation()
            ->modalHeading('Send login link')
            ->modalDescription(fn (Reservation $record): string => "Email a sign-in link to {$record->customer->email} so they can view or cancel their bookings.")
            ->action(fn (Reservation $record) => app(SendBookingsLink::class)->handle($record->customer))
            ->successNotificationTitle('Login link sent');
    }
}
