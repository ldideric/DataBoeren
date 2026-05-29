<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.first_name')
                    ->formatStateUsing(fn ($state, $record) => $record->customer?->first_name . ' ' . $record->customer?->last_name)
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
                    ->formatStateUsing(fn ($state, $record) => $state . ' adult(s), ' . $record->num_children . ' child(ren)')
                    ->label('Guests'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('source')
                    ->badge(),
                TextColumn::make('orderSummary.total')
                    ->formatStateUsing(fn ($state) => $state !== null ? '€ ' . number_format($state / 100, 2, ',', '.') : '—')
                    ->label('Total'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ReservationStatus::class),
                SelectFilter::make('source')
                    ->options(ReservationSource::class),
                TrashedFilter::make(),
                // @todo SelectFilter for campsite_id (relationship) — show all bookings for a specific pitch
                // @todo Filter for check_in date range — find arrivals within a given week or month
                // @todo TernaryFilter for coupon_id (has coupon / no coupon) — measure promotional uptake
                // @todo Filter for has_booked_by_user_id (staff-created vs. self-service) — overlap with source but useful standalone
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
