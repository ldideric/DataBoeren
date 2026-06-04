<?php

namespace App\Filament\Resources\Campsites\Tables;

use App\Filament\Resources\Campsites\Filters\CampsiteTypeFilter;
use App\Filament\Resources\Campsites\Filters\ElectricityFilter;
use App\Filament\Resources\Campsites\Filters\MinCapacityFilter;
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
                    ->label(__('common.name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('campsite.fields.type'))
                    ->badge()
                    ->searchable(),
                IconColumn::make('has_electricity')
                    ->label(__('campsite.fields.has_electricity'))
                    ->boolean(),
                TextColumn::make('max_people')
                    ->label(__('campsite.fields.max_people'))
                    ->suffix(__('campsite.suffix.people'))
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('common.notes'))
                    ->limit(40)
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                TrashedFilter::make(),
                CampsiteTypeFilter::make(),
                ElectricityFilter::make(),
                MinCapacityFilter::make(),
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
