<?php

namespace App\Filament\Resources\Campsites\Pages;

use App\Filament\Resources\Campsites\CampsiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCampsites extends ListRecords
{
    protected static string $resource = CampsiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
