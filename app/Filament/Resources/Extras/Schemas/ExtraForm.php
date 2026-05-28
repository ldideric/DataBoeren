<?php

namespace App\Filament\Resources\Extras\Schemas;

use App\Enums\BillingType;
use App\Enums\StockType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ExtraForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('billing_type')
                    ->options(BillingType::class)
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock')
                    ->numeric(),
                Select::make('stock_type')
                    ->options(StockType::class)
                    ->default('rental')
                    ->required(),
                TextInput::make('max_per_booking')
                    ->numeric(),
            ]);
    }
}
