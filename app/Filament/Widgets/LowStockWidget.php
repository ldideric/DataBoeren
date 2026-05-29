<?php

namespace App\Filament\Widgets;

use App\Models\Extra;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LowStockWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    public function getColumnSpan(): int | string | array
    {
        return Auth::user()?->isAdmin() ? 'full' : 1;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Low stock extras')
            ->description('Extras with 3 or fewer items remaining')
            ->query(
                Extra::query()
                    ->whereNotNull('stock')
                    ->where('stock', '<=', 3)
                    ->orderBy('stock'),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state) => $state === 0 ? 'danger' : 'warning'),
            ])
            ->paginated(false)
            ->emptyStateHeading('All extras are well stocked')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
