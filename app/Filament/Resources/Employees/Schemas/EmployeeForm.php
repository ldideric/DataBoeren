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
                Select::make('role')
                    ->label(__('common.role'))
                    ->options([
                        UserRole::Employee->value => UserRole::Employee->getLabel(),
                        UserRole::Admin->value    => UserRole::Admin->getLabel(),
                    ])
                    ->required()
                    ->disabled(fn () => ! auth()->user()->isAdmin()),
            ]);
    }
}
