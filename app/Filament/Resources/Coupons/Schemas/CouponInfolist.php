<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('code'),
                TextEntry::make('discount_type')
                    ->badge(),
                TextEntry::make('discount_value')
                    ->numeric(),
                TextEntry::make('expires_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('max_uses')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('uses_count')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
