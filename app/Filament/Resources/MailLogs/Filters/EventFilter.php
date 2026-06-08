<?php

namespace App\Filament\Resources\MailLogs\Filters;

use App\Enums\MailEvent;
use Filament\Tables\Filters\SelectFilter;

class EventFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'event');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mail_log.fields.event'))
            ->options(MailEvent::class);
    }
}
