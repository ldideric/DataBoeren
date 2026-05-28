<?php

namespace App\Filament\Resources\Campsites\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
            ->columns([
                TextColumn::make('customer.first_name')
                    ->formatStateUsing(fn ($state, $record) => $record->customer?->first_name . ' ' . $record->customer?->last_name)
                    ->label('Customer'),
                TextColumn::make('check_in')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('check_out')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('orderSummary.total')
                    ->formatStateUsing(fn ($state) => $state !== null ? '€ ' . number_format($state / 100, 2, ',', '.') : '—')
                    ->label('Total'),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
