<?php

namespace App\Filament\Resources\MailLogs\Filters;

use App\Enums\MailEvent;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FailuresFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'failures');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mail_log.filters.failures'))
            ->query(fn (Builder $query): Builder => $query->whereIn('event', [
                MailEvent::Failed->value,
                MailEvent::Retrying->value,
            ]));
    }
}
