<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Resources\Employees\Filters\RoleFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmployeesTable
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
                TextColumn::make('role')
                    ->label(__('common.role'))
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
                RoleFilter::make(),
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
