<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('scope')
                    ->options(CouponScope::class)
                    ->live()
                    ->required(),
                Select::make('extra_id')
                    ->relationship('extra', 'name')
                    ->visible(fn (Get $get) => $get('scope') === CouponScope::Extra->value),
                Select::make('discount_type')
                    ->options(DiscountType::class)
                    ->required(),
                TextInput::make('discount_value')
                    ->required()
                    ->numeric(),
                DatePicker::make('expires_at'),
                TextInput::make('max_uses')
                    ->numeric()
                    ->nullable(),
            ]);
    }
}
