<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label(__('common.first_name'))
                    ->required(),
                TextInput::make('last_name')
                    ->label(__('common.last_name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('common.email'))
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('phone')
                    ->label(__('common.phone'))
                    ->tel(),
            ]);
    }
}
