<?php

namespace App\Filament\Resources\Campsites\Schemas;

use App\Models\Campsite;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CampsiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label(__('common.id')),
                TextEntry::make('name')
                    ->label(__('common.name')),
                TextEntry::make('type')
                    ->label(__('campsite.fields.type'))
                    ->badge(),
                IconEntry::make('has_electricity')
                    ->label(__('campsite.fields.has_electricity'))
                    ->boolean(),
                TextEntry::make('max_people')
                    ->label(__('campsite.fields.max_people'))
                    ->numeric(),
                TextEntry::make('notes')
                    ->label(__('common.notes'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label(__('common.deleted_at'))
                    ->dateTime()
                    ->visible(fn (Campsite $record): bool => $record->trashed()),
            ]);
    }
}
