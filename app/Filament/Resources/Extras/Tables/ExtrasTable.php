<?php

namespace App\Filament\Resources\Extras\Tables;

use App\Filament\Resources\Extras\Filters\BillingTypeFilter;
use App\Filament\Resources\Extras\Filters\LowStockFilter;
use App\Filament\Resources\Extras\Filters\StockTypeFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ExtrasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(20)
                    ->placeholder('None')
                    ->tooltip(fn ($record) => $record->description),
                TextColumn::make('billing_type')
                    ->badge(),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => '€ '.number_format($state / 100, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('stock_type')
                    ->badge(),
                TextColumn::make('stock')
                    ->placeholder('Unlimited')
                    ->sortable(),
                TextColumn::make('max_per_booking')
                    ->placeholder('Unlimited')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
                BillingTypeFilter::make(),
                StockTypeFilter::make(),
                LowStockFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
