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
                TextEntry::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label(__('common.updated_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('purged_at')
                    ->label(__('employee.fields.purged_at'))
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->label(__('common.deleted_at'))
                    ->dateTime()
                    ->visible(fn (User $record): bool => $record->trashed()),
                TextEntry::make('stripe_id')
                    ->label(__('employee.fields.stripe_id'))
                    ->placeholder('-'),
                TextEntry::make('pm_type')
                    ->label(__('employee.fields.pm_type'))
                    ->placeholder('-'),
                TextEntry::make('pm_last_four')
                    ->label(__('employee.fields.pm_last_four'))
                    ->placeholder('-'),
                TextEntry::make('trial_ends_at')
                    ->label(__('employee.fields.trial_ends_at'))
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
