<?php

namespace App\Filament\Resources\Campsites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CampsitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                IconColumn::make('has_electricity')
                    ->boolean(),
                TextColumn::make('max_people')
                    ->suffix(' pers.')
                    ->sortable(),
                TextColumn::make('max_vehicles')
                    ->suffix(' voertuig(en)')
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(40)
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                TrashedFilter::make(),
                // @todo SelectFilter for type — show only tent/caravan/glamping pitches
                // @todo TernaryFilter for has_electricity — staff often need electric pitches quickly
                // @todo Filter for max_people (min value) — find pitches that fit large groups
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
