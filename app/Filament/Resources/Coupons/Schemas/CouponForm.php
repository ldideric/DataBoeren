<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        Section::make('Basic information')
                            ->columnSpanFull()
                            ->schema([
                                TextInput::make('title')
                                    ->required(),
                                TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                        ]),
                        Section::make('Scope of the coupon')
                            ->columnSpanFull()
                            ->schema([
                                Select::make('scope')
                                    ->options(CouponScope::class)
                                    ->live()
                                    ->required(),
                                Select::make('extra_id')
                                    ->relationship('extra', 'name')
                                    ->live()
                                    ->visible(fn (Get $get) => $get('scope') === CouponScope::Extra)
                                    ->required(fn (Get $get) => $get('scope') === CouponScope::Extra),
                            ]),
                            Section::make('Discount details')
                            ->columnSpanFull()
                            ->schema([
                                Select::make('discount_type')
                                    ->options(DiscountType::class)
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('discount_value', null))
                                    ->required(),
                                TextInput::make('discount_value')
                                    ->required()
                                    ->numeric()
                                    ->name('Discount')
                                    ->live()
                                    ->prefix(fn (Get $get) => $get('discount_type') === DiscountType::Flat ? '€' : null)
                                    ->suffix(fn (Get $get) => $get('discount_type') === DiscountType::Percent ? '%' : null)
                                    ->afterStateHydrated(function ($state, Set $set, $record) {
                                        if ($record?->discount_type === DiscountType::Flat && $state !== null) {
                                            $set('discount_value', $state / 100);
                                        }
                                    })
                                    ->dehydrateStateUsing(
                                        fn ($state, Get $get) =>
                                        $get('discount_type') === DiscountType::Flat ? (int) round($state * 100) : $state
                                    ),
                        ]),
                        Section::make('Additional information')
                            ->columnSpanFull()
                            ->schema([
                                DatePicker::make('expires_at'),
                                TextInput::make('max_uses')
                                    ->numeric()
                                    ->nullable(),
                            ]),
                    ]),
            ]);
    }
}
