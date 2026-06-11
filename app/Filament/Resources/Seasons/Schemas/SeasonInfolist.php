<?php

namespace App\Filament\Resources\Seasons\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SeasonInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label(__('common.id')),
                TextEntry::make('name')
                    ->label(__('common.name')),
                TextEntry::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
