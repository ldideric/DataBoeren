<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Models\Coupon;
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
                    ->badge()
                    ->sortable(),
                TextColumn::make('formatted_discount')
                    ->label('Discount'),
                TextColumn::make('expires_at')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->expires_at?->isPast() ? 'danger' : null)
                    ->placeholder('No expiry')
                    ->sortable(),
                TextColumn::make('uses_count')
                    ->formatStateUsing(fn ($state, Coupon $record) => $state . ($record->max_uses ? ' / ' . $record->max_uses : null))
                    ->label('Uses'),
            ])
            ->filters([
                TrashedFilter::make(),
                // @todo add filter for expired / not expired
                // @todo add filter for scope
                // @todo add filter for discount type
                // @todo add filter for coupons with usage limits
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
