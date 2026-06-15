<?php

namespace App\Filament\Resources\MailLogs\Filters;

use App\Models\MailLog;
use Filament\Tables\Filters\SelectFilter;

class MailableFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'mailable');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mail_log.fields.mailable'))
            ->options(fn (): array => MailLog::query()
                ->whereNotNull('mailable')
                ->distinct()
                ->orderBy('mailable')
                ->pluck('mailable')
                ->mapWithKeys(fn (string $mailable): array => [$mailable => class_basename($mailable)])
                ->all());
    }
}
