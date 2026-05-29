<?php

namespace App\Filament\Resources\Extras\Tables;

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
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
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
                // @todo SelectFilter for billing_type — separate per-night, per-stay, and per-person extras
                // @todo SelectFilter for stock_type — separate rentals (returned) from consumables
                // @todo Filter for low stock (stock is not null AND stock <= 3) — quick reorder check
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
