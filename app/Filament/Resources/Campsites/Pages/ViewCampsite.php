<?php

namespace App\Filament\Resources\Campsites\Pages;

use App\Filament\Resources\Campsites\CampsiteResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCampsite extends ViewRecord
{
    protected static string $resource = CampsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
