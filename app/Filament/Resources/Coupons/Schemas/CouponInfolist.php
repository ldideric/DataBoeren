<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Models\Coupon;
use App\Filament\Resources\Extras\ExtraResource;
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
                TextEntry::make('title'),
                TextEntry::make('code'),
                TextEntry::make('scope')
                    ->badge(),
                TextEntry::make('extra.name')
                    ->label('Extra')
                    ->placeholder('-')
                    ->url(fn ($record) => $record->extra_id
                        ? ExtraResource::getUrl('view', ['record' => $record->extra_id])
                        : null),
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
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Coupon $record): bool => $record->trashed()),
            ]);
    }
}
