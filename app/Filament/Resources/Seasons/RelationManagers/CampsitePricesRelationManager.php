<?php

namespace App\Filament\Resources\Seasons\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->relationship('campsite', 'name')
                    ->required(),
                TextInput::make('nightly_rate')
                    ->required()
                    ->numeric(),
                TextInput::make('per_adult_rate')
                    ->required()
                    ->numeric(),
                TextInput::make('per_child_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('campsite.name')
                    ->searchable(),
                TextColumn::make('nightly_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('per_adult_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('per_child_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
