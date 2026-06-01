<?php

namespace App\Filament\Resources\Seasons\Filters;

use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ActiveNowFilter extends Filter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'active_now');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Active now')
            ->toggle()
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['isActive'] ?? false, fn ($q) => $q->whereHas(
                    'periods',
                    fn ($p) => $p->where('starts_at', '<=', today())->where('ends_at', '>=', today())
                )));
    }
}
