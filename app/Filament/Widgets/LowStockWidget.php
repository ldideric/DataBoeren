<?php

namespace App\Filament\Widgets;

use App\Models\Extra;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

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
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('stock')
                    ->badge()
                    ->color(fn (int $state) => $state === 0 ? 'danger' : 'warning'),

                Tables\Columns\TextColumn::make('stock_type')
                    ->label('Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->state(fn (Extra $e) => '€ '.number_format($e->price / 100, 2, ',', '.')),
            ])
            ->paginated(false)
            ->emptyStateHeading('All extras are well stocked')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
