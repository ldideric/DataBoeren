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
                    ->label(__('common.name'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('extra.fields.description'))
                    ->columnSpanFull(),
                Select::make('billing_type')
                    ->label(__('extra.fields.billing_type'))
                    ->options(BillingType::class)
                    ->required(),
                TextInput::make('price')
                    ->label(__('extra.fields.price'))
                    ->numeric()
                    ->suffix('ct')
                    ->hint(__('extra.hints.price'))
                    ->required(),
                Select::make('stock_type')
                    ->label(__('extra.fields.stock_type'))
                    ->options(StockType::class)
                    ->required(),
                TextInput::make('stock')
                    ->label(__('extra.fields.stock'))
                    ->numeric()
                    ->nullable()
                    ->hint(__('extra.hints.stock')),
                TextInput::make('max_per_booking')
                    ->label(__('extra.fields.max_per_booking'))
                    ->numeric()
                    ->nullable(),
            ]);
    }
}
