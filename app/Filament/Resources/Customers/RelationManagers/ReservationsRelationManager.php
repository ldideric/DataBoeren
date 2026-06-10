<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Resources\Reservations\ReservationResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReservationsRelationManager extends RelationManager
{
    protected static string $relationship = 'reservations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('check_in')
            ->recordUrl(fn ($record) => ReservationResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('campsite.name')
                    ->label(__('customer.reservations.campsite')),
                TextColumn::make('check_in')
                    ->label(__('customer.reservations.check_in'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('customer.reservations.check_out'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('common.status'))
                    ->badge(),
                TextColumn::make('orderSummary.total')
                    ->formatStateUsing(fn ($state) => $state !== null ? '€ ' . number_format($state / 100, 2, ',', '.') : '—')
                    ->label(__('customer.reservations.total')),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
