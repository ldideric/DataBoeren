<?php

namespace App\Filament\Resources\Campsites\Filters;

use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class MinCapacityFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'min_capacity');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('campsite.filters.min_capacity'))
            ->schema([
                TextInput::make('min_people')
                    ->label(__('campsite.filters.min_people'))
                    ->numeric()
                    ->minValue(1),
            ])
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['min_people'] ?? null, fn ($q, $min) => $q->where('max_people', '>=', $min)))
            ->indicateUsing(fn (array $data) => isset($data['min_people']) && $data['min_people']
                ? __('campsite.filters.min_capacity_indicator', ['count' => $data['min_people']])
                : null);
    }
}
