<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Pages\NewBooking;
use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('newBooking')
                ->label('New Booking')
                ->icon('heroicon-o-plus')
                ->url(NewBooking::getUrl()),
        ];
    }
}
