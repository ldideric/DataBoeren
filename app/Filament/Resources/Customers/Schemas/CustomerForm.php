<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->disabled()
                    ->tel(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('role')
                    ->options(UserRole::class)
                    ->required(),
            ]);
    }
}
