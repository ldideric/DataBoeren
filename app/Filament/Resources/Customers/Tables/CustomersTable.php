<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->formatStateUsing(fn ($state, $record) => $record->first_name . ' ' . $record->last_name)
                    ->label('Name')
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->placeholder('None')
                    ->searchable(),
                IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label('Verified'),
                IconColumn::make('purged_at')
                    ->boolean()
                    ->label('Purged')
                    ->trueIcon('heroicon-o-no-symbol')
                    ->trueColor('danger'),
            ])
            ->filters([
                TrashedFilter::make(),
                // @todo TernaryFilter for email_verified_at — distinguish fully registered vs. guest-checkout customers
                // @todo TernaryFilter for purged_at — hide or isolate GDPR-purged accounts
                // @todo Filter for created_at date range — find customers who registered in a specific period
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
