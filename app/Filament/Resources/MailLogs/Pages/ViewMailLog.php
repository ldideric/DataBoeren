<?php

namespace App\Filament\Resources\MailLogs\Pages;

use App\Filament\Resources\MailLogs\MailLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMailLog extends ViewRecord
{
    protected static string $resource = MailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
