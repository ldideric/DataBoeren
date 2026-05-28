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
                    ->numeric()
                    ->suffix('ct')
                    ->hint('Store as cents, e.g. 500 = €5.00')
                    ->required(),
                Select::make('stock_type')
                    ->options(StockType::class)
                    ->required(),
                TextInput::make('stock')
                    ->numeric()
                    ->nullable()
                    ->hint('Leave empty for unlimited'),
                TextInput::make('max_per_booking')
                    ->numeric()
                    ->nullable(),
            ]);
    }
}
