<?php

namespace App\Filament\Resources\MailLogs\Filters;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OccurredAtFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'occurred_at');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('mail_log.fields.occurred_at'))
            ->columnSpanFull()
            ->schema([
                Section::make()
                    ->schema([
                        DateTimePicker::make('from')
                            ->label(__('common.from'))
                            ->seconds(false),
                        DateTimePicker::make('until')
                            ->label(__('common.until'))
                            ->seconds(false),
                    ])
                    ->columns(2),
            ])
            ->query(
                fn (Builder $query, array $data) => $query
                    ->when($data['from'] ?? null, fn ($q, $date) => $q->where('created_at', '>=', $date))
                    ->when($data['until'] ?? null, fn ($q, $date) => $q->where('created_at', '<=', $date))
            )
            ->indicateUsing(
                fn (array $data) => collect()
                    ->when($data['from'] ?? null, fn ($collection) => $collection->push(__('mail_log.filters.occurred_from', ['date' => $data['from']])))
                    ->when($data['until'] ?? null, fn ($collection) => $collection->push(__('mail_log.filters.occurred_until', ['date' => $data['until']])))
                    ->toArray()
            );
    }
}
