<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Employee Information')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('first_name'),
                        TextEntry::make('last_name'),
                        TextEntry::make('email')
                            ->label('Email address'),
                        TextEntry::make('phone')
                            ->placeholder('No Phone number'),
                        TextEntry::make('email_verified_at')
                            ->dateTime()
                            ->placeholder('Not verified'),
                        TextEntry::make('role')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
