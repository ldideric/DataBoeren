<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->required(),
                Select::make('scope')
                    ->options(CouponScope::class)
                    ->default('accommodation')
                    ->required(),
                Select::make('extra_id')
                    ->relationship('extra', 'name'),
                Select::make('discount_type')
                    ->options(DiscountType::class)
                    ->required(),
                TextInput::make('discount_value')
                    ->required()
                    ->numeric(),
                DatePicker::make('expires_at'),
                TextInput::make('max_uses')
                    ->numeric(),
                TextInput::make('uses_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
