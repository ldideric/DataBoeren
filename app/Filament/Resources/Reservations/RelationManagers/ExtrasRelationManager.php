<?php

namespace App\Filament\Resources\Reservations\RelationManagers;

use App\Filament\Resources\Extras\ExtraResource;
use App\Models\Extra;
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
use Illuminate\Database\Eloquent\Model;

class ExtrasRelationManager extends RelationManager
{
    protected static string $relationship = 'extras';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('reservation.extras.title');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('extra_id')
                    ->label(__('reservation.extras.extra'))
                    ->relationship('extra', 'name')
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('reservation.extras.quantity'))
                    ->numeric()
                    ->minValue(1)
                    ->required()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modelLabel(__('reservation.extras.model_label'))
            ->pluralModelLabel(__('reservation.extras.plural_model_label'))
            ->recordUrl(fn ($record) =>  ExtraResource::getUrl('view', ['record' => $record->extra]))
            ->columns([
                TextColumn::make('extra.name')
                    ->label(__('reservation.extras.extra')),
                TextColumn::make('quantity')
                    ->label(__('reservation.extras.quantity'))
                    ->numeric(),
                TextColumn::make('unit_price')
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
                    ->label(__('reservation.extras.unit_price')),
                TextColumn::make('subtotal')
                    ->label(__('reservation.extras.subtotal'))
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $extra = Extra::findOrFail($data['extra_id']);
                        $data['unit_price'] = $extra->price;
                        $data['subtotal']   = $extra->price * $data['quantity'];

                        return $data;
                    }),
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
