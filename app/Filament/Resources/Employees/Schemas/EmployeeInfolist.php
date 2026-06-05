<?php

namespace App\Filament\Resources\Employees\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label(__('common.id')),
                TextEntry::make('first_name')
                    ->label(__('common.first_name'))
                    ->placeholder('-'),
                TextEntry::make('last_name')
                    ->label(__('common.last_name'))
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label(__('employee.fields.email_address'))
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->label(__('common.phone'))
                    ->placeholder('-'),
                TextEntry::make('email_verified_at')
                    ->label(__('employee.fields.email_verified_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('role')
                    ->label(__('common.role'))
                    ->badge(),
                TextEntry::make('deleted_at')
                    ->label(__('common.deleted_at'))
                    ->dateTime()
                    ->visible(fn (User $record): bool => $record->trashed()),
            ]);
    }
}
