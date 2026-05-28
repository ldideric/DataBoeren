<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Enums\DiscountType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('code')
                    ->copyable()
                    ->fontFamily(FontFamily::Mono)
                    ->searchable(),
                TextColumn::make('scope')
                    ->badge(),
                TextColumn::make('discount_type')
                    ->badge(),
                TextColumn::make('discount_value')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->discount_type === DiscountType::Percent
                            ? $state . '%'
                            : '€ ' . number_format($state / 100, 2, ',', '.')
                    ),
                TextColumn::make('expires_at')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->expires_at?->isPast() ? 'danger' : null)
                    ->sortable(),
                TextColumn::make('uses_count')
                    ->formatStateUsing(fn ($state, $record) => $state . ' / ' . ($record->max_uses ?? '∞'))
                    ->label('Uses'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
