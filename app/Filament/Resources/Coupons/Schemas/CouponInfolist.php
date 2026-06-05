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
                    ->label(__('common.id')),
                TextEntry::make('title')
                    ->label(__('coupon.fields.title')),
                TextEntry::make('code')
                    ->label(__('coupon.fields.code')),
                TextEntry::make('scope')
                    ->label(__('coupon.fields.scope'))
                    ->badge(),
                TextEntry::make('extra.name')
                    ->label(__('coupon.fields.extra'))
                    ->placeholder('-')
                    ->url(fn ($record) => $record->extra_id
                        ? ExtraResource::getUrl('view', ['record' => $record->extra_id])
                        : null),
                TextEntry::make('discount_type')
                    ->label(__('coupon.fields.discount_type'))
                    ->badge(),
                TextEntry::make('discount_value')
                    ->label(__('coupon.fields.discount_value'))
                    ->numeric(),
                TextEntry::make('expires_at')
                    ->label(__('coupon.fields.expires_at'))
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('max_uses')
                    ->label(__('coupon.fields.max_uses'))
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('uses_count')
                    ->label(__('coupon.fields.uses'))
                    ->numeric(),
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
                    ->visible(fn (Coupon $record): bool => $record->trashed()),
            ]);
    }
}
