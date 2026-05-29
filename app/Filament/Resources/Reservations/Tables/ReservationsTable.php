<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Filament\Resources\Reservations\Filters\ArrivalPeriodFilter;
use App\Filament\Resources\Reservations\Filters\BookedByStaffFilter;
use App\Filament\Resources\Reservations\Filters\CampsiteFilter;
use App\Filament\Resources\Reservations\Filters\DeparturePeriodFilter;
use App\Filament\Resources\Reservations\Filters\HasCouponFilter;
use App\Filament\Resources\Reservations\Filters\OnSiteFilter;
use App\Filament\Resources\Reservations\Filters\SourceFilter;
use App\Filament\Resources\Reservations\Filters\StatusFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.first_name')
                    ->formatStateUsing(fn ($_, $record) => $record->customer?->first_name.' '.$record->customer?->last_name)
                    ->label('Customer')
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))),
                TextColumn::make('campsite.name')
                    ->searchable(),
                TextColumn::make('check_in')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('num_adults')
                    ->formatStateUsing(fn ($state, $record) => $state.' adult(s), '.$record->num_children.' child(ren)')
                    ->label('Guests'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('source')
                    ->badge(),
                TextColumn::make('orderSummary.total')
                    ->formatStateUsing(fn ($state) => $state !== null ? '€ '.number_format($state / 100, 2, ',', '.') : '—')
                    ->label('Total'),
            ])
            ->filtersFormColumns(2)
            ->filters([
                StatusFilter::make(),
                SourceFilter::make(),
                TrashedFilter::make(),
                CampsiteFilter::make(),
                ArrivalPeriodFilter::make(),
                DeparturePeriodFilter::make(),
                OnSiteFilter::make(),
                HasCouponFilter::make(),
                BookedByStaffFilter::make(),
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
