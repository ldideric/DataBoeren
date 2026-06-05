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
                    ->label(__('common.id')),
                TextEntry::make('name')
                    ->label(__('common.name')),
                TextEntry::make('description')
                    ->label(__('extra.fields.description'))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('billing_type')
                    ->label(__('extra.fields.billing_type'))
                    ->badge(),
                TextEntry::make('price')
                    ->label(__('extra.fields.price'))
                    ->money(),
                TextEntry::make('stock')
                    ->label(__('extra.fields.stock'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('stock_type')
                    ->label(__('extra.fields.stock_type'))
                    ->badge(),
                TextEntry::make('max_per_booking')
                    ->label(__('extra.fields.max_per_booking'))
                    ->numeric()
                    ->placeholder('-'),
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
                    ->visible(fn (Extra $record): bool => $record->trashed()),
            ]);
    }
}
