<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookedReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookedReservations';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('employee.relations.booked_reservations');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->recordUrl(fn ($record) => ReservationResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('customer.first_name')
                    ->formatStateUsing(fn ($_, $record) => $record->customer?->first_name.' '.$record->customer?->last_name)
                    ->label(__('reservation.fields.customer')),
                TextColumn::make('campsite.name')
                    ->label(__('reservation.fields.campsite')),
                TextColumn::make('check_in')
                    ->label(__('reservation.fields.check_in'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('reservation.fields.check_out'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('common.status'))
                    ->badge(),
                TextColumn::make('orderSummary.total')
                    ->formatStateUsing(fn ($state) => $state !== null ? '€ '.number_format($state / 100, 2, ',', '.') : '—')
                    ->label(__('reservation.fields.total')),
            ])
            ->defaultSort('check_in', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
