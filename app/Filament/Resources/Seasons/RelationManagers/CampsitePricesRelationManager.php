<?php

namespace App\Filament\Resources\Seasons\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Campsites\CampsiteResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CampsitePricesRelationManager extends RelationManager
{
    protected static string $relationship = 'campsitePrices';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('campsite_id')
                    ->label(__('season.fields.campsite'))
                    ->relationship('campsite', 'name')
                    ->required(),
                TextInput::make('nightly_rate')
                    ->label(__('season.fields.nightly_rate'))
                    ->numeric()
                    ->suffix('ct')
                    ->required(),
                TextInput::make('per_adult_rate')
                    ->label(__('season.fields.per_adult_rate'))
                    ->numeric()
                    ->suffix('ct')
                    ->required(),
                TextInput::make('per_child_rate')
                    ->label(__('season.fields.per_child_rate'))
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
                TextColumn::make('campsite.name')
                    ->label(__('season.fields.campsite'))
                    ->url(fn ($record) => CampsiteResource::getUrl('view', ['record' => $record->campsite_id])),
                TextColumn::make('nightly_rate')
                    ->label(__('season.fields.nightly_rate'))
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                TextColumn::make('per_adult_rate')
                    ->label(__('season.fields.per_adult_rate'))
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                TextColumn::make('per_child_rate')
                    ->label(__('season.fields.per_child_rate'))
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
