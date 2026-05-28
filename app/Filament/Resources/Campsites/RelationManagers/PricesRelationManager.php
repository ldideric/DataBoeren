<?php

namespace App\Filament\Resources\Campsites\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('season_id')
                    ->relationship('season', 'name')
                    ->required(),
                TextInput::make('nightly_rate')
                    ->numeric()
                    ->suffix('ct')
                    ->required(),
                TextInput::make('per_adult_rate')
                    ->numeric()
                    ->suffix('ct')
                    ->required(),
                TextInput::make('per_child_rate')
                    ->numeric()
                    ->suffix('ct')
                    ->required()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('season.name'),
                TextColumn::make('nightly_rate')
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                TextColumn::make('per_adult_rate')
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                TextColumn::make('per_child_rate')
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
