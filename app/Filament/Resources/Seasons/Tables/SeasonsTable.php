<?php

namespace App\Filament\Resources\Seasons\Tables;

use App\Filament\Resources\Seasons\Filters\ActiveNowFilter;
use App\Filament\Resources\Seasons\Filters\MissingPricesFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeasonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->searchable(),
                TextColumn::make('periods_count')
                    ->counts('periods')
                    ->label(__('season.fields.periods')),
            ])
            ->filters([
                ActiveNowFilter::make(),
                MissingPricesFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
