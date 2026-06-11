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
            ->label(__('reservation.actions.send_login_link.label'))
            ->icon('heroicon-o-paper-airplane')
            ->requiresConfirmation()
            ->modalHeading(__('reservation.actions.send_login_link.modal_heading'))
            ->modalDescription(fn (Reservation $record): string => __('reservation.actions.send_login_link.modal_description', ['email' => $record->customer->email]))
            ->action(fn (Reservation $record) => app(SendBookingsLink::class)->handle($record->customer))
            ->successNotificationTitle(__('reservation.actions.send_login_link.success'));
    }
}
