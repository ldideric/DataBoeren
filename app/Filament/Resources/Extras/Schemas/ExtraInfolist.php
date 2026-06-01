<?php

namespace App\Filament\Resources\Extras\Schemas;

use App\Models\Extra;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ExtraInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('billing_type')
                    ->badge(),
                TextEntry::make('price')
                    ->money(),
                TextEntry::make('stock')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stock_type')
                    ->badge(),
                TextEntry::make('max_per_booking')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Extra $record): bool => $record->trashed()),
            ]);
    }
}
