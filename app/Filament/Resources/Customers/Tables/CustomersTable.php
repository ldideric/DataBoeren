<?php

namespace App\Filament\Resources\Customers\Tables;

use App\Filament\Resources\Customers\Filters\EmailVerifiedFilter;
use App\Filament\Resources\Customers\Filters\PurgedFilter;
use App\Filament\Resources\Customers\Filters\RegistrationDateFilter;
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
                    ->formatStateUsing(fn ($_, $record) => $record->first_name.' '.$record->last_name)
                    ->label(__('common.name'))
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('email')
                    ->label(__('common.email'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(__('common.phone'))
                    ->placeholder(__('customer.placeholders.no_phone'))
                    ->searchable(),
                IconColumn::make('email_verified_at')
                    ->boolean()
                    ->label(__('customer.fields.verified')),
                IconColumn::make('purged_at')
                    ->boolean()
                    ->label(__('customer.fields.purged'))
                    ->trueIcon('heroicon-o-no-symbol')
                    ->trueColor('danger'),
            ])
            ->filters([
                TrashedFilter::make(),
                EmailVerifiedFilter::make(),
                PurgedFilter::make(),
                RegistrationDateFilter::make(),
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
