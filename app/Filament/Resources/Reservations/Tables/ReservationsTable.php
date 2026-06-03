<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Filament\Resources\Reservations\Actions\AcceptReservationAction;
use App\Filament\Resources\Reservations\Actions\CancelReservationAction;
use App\Filament\Resources\Reservations\Actions\ResendConfirmationAction;
use App\Filament\Resources\Reservations\Actions\SendLoginLinkAction;
use App\Filament\Resources\Reservations\Filters\ArrivalPeriodFilter;
use App\Filament\Resources\Reservations\Filters\BookedByStaffFilter;
use App\Filament\Resources\Reservations\Filters\CampsiteFilter;
use App\Filament\Resources\Reservations\Filters\DeparturePeriodFilter;
use App\Filament\Resources\Reservations\Filters\HasCouponFilter;
use App\Filament\Resources\Reservations\Filters\OnSiteFilter;
use App\Filament\Resources\Reservations\Filters\SourceFilter;
use App\Filament\Resources\Reservations\Filters\StatusFilter;
use Filament\Actions\ActionGroup;
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
                    ->label(__('reservation.fields.customer'))
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%"))),
                TextColumn::make('campsite.name')
                    ->label(__('reservation.fields.campsite'))
                    ->searchable(),
                TextColumn::make('check_in')
                    ->label(__('reservation.fields.check_in'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('reservation.fields.check_out'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('num_adults')
                    ->formatStateUsing(fn ($state, $record) => __('reservation.fields.guests_summary', ['adults' => $state, 'children' => $record->num_children]))
                    ->label(__('reservation.fields.guests')),
                TextColumn::make('status')
                    ->label(__('common.status'))
                    ->badge(),
                TextColumn::make('source')
                    ->label(__('reservation.fields.source'))
                    ->badge(),
                TextColumn::make('orderSummary.total')
                    ->formatStateUsing(fn ($state) => $state !== null ? '€ '.number_format($state / 100, 2, ',', '.') : '—')
                    ->label(__('reservation.fields.total')),
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
                ActionGroup::make([
                    AcceptReservationAction::make(),
                    SendLoginLinkAction::make(),
                    ResendConfirmationAction::make(),
                    CancelReservationAction::make(),
                ]),
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
