<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name'),
                TextInput::make('last_name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password(),
                Select::make('role')
                    ->options(UserRole::class)
                    ->required(),
                DateTimePicker::make('purged_at'),
                TextInput::make('stripe_id'),
                TextInput::make('pm_type'),
                TextInput::make('pm_last_four'),
                DateTimePicker::make('trial_ends_at'),
            ]);
    }
}
