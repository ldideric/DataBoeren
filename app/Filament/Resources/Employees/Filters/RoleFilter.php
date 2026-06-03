<?php

namespace App\Filament\Resources\Employees\Filters;

use App\Enums\UserRole;
use Filament\Tables\Filters\SelectFilter;

class RoleFilter extends SelectFilter
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'role');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('employee.filters.role'))
            ->options(UserRole::class);
    }
}
