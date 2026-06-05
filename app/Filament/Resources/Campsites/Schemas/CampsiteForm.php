<?php

namespace App\Filament\Resources\Campsites\Schemas;

use App\Enums\CampsiteType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CampsiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('common.name'))
                    ->required(),
                Select::make('type')
                    ->label(__('campsite.fields.type'))
                    ->options(CampsiteType::class)
                    ->required(),
                Toggle::make('has_electricity')
                    ->label(__('campsite.fields.has_electricity'))
                    ->required(),
                TextInput::make('max_people')
                    ->label(__('campsite.fields.max_people'))
                    ->required()
                    ->numeric(),
                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->columnSpanFull(),
            ]);
    }
}
