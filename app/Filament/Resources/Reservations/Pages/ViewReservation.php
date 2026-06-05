<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\Actions\AcceptReservationAction;
use App\Filament\Resources\Reservations\Actions\CancelReservationAction;
use App\Filament\Resources\Reservations\Actions\ResendConfirmationAction;
use App\Filament\Resources\Reservations\Actions\SendLoginLinkAction;
use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AcceptReservationAction::make(),
            SendLoginLinkAction::make(),
            ResendConfirmationAction::make(),
            CancelReservationAction::make(),
            EditAction::make(),
        ];
    }
}
