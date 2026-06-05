<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Filament\Resources\Coupons\Filters\DiscountTypeFilter;
use App\Filament\Resources\Coupons\Filters\ExpiredFilter;
use App\Filament\Resources\Coupons\Filters\ScopeFilter;
use App\Filament\Resources\Coupons\Filters\UsageLimitFilter;
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
                    ->label(__('coupon.fields.title'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('coupon.fields.code'))
                    ->copyable()
                    ->fontFamily(FontFamily::Mono)
                    ->searchable(),
                TextColumn::make('scope')
                    ->label(__('coupon.fields.scope'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('formatted_discount')
                    ->label(__('coupon.fields.discount')),
                TextColumn::make('expires_at')
                    ->label(__('coupon.fields.expires_at'))
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->expires_at?->isPast() ? 'danger' : null)
                    ->placeholder(__('coupon.placeholders.no_expiry'))
                    ->sortable(),
                TextColumn::make('uses_count')
                    ->formatStateUsing(fn ($state, Coupon $record) => $state.($record->max_uses ? ' / '.$record->max_uses : null))
                    ->label(__('coupon.fields.uses')),
            ])
            ->filters([
                TrashedFilter::make(),
                ExpiredFilter::make(),
                ScopeFilter::make(),
                DiscountTypeFilter::make(),
                UsageLimitFilter::make(),
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
