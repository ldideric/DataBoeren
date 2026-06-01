<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
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
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Select::make('role')
                    ->options([
                        UserRole::Employee->value => 'Employee',
                        UserRole::Admin->value    => 'Admin',
                    ])
                    ->required()
                    ->disabled(fn () => ! auth()->user()->isAdmin()),
            ]);
    }
}
