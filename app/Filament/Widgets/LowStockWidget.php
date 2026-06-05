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
            ->heading(__('widget.low_stock.heading'))
            ->description(__('widget.low_stock.description'))
            ->query(
                Extra::query()
                    ->whereNotNull('stock')
                    ->where('stock', '<=', 3)
                    ->orderBy('stock'),
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('common.name')),
                Tables\Columns\TextColumn::make('stock')
                    ->label(__('extra.fields.stock'))
                    ->badge()
                    ->color(fn (int $state) => $state === 0 ? 'danger' : 'warning'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('widget.low_stock.empty_heading'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
