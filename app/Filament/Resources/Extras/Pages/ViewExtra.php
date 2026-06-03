<?php

namespace App\Filament\Resources\Extras\Pages;

use App\Filament\Resources\Extras\ExtraResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExtra extends ViewRecord
{
    protected static string $resource = ExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
