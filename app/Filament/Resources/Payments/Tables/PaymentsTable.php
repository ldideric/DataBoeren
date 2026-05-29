<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Filament\Resources\Payments\Filters\MethodFilter;
use App\Filament\Resources\Payments\Filters\PaidAtFilter;
use App\Filament\Resources\Payments\Filters\StatusFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservation.customer.email')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('reservation.check_in')
                    ->date('d/m/Y')
                    ->label('Check-in'),
                TextColumn::make('amount')
                    ->formatStateUsing(fn ($state) => '€ '.number_format($state / 100, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('method')
                    ->badge()
                    ->searchable(),
                TextColumn::make('paid_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('stripe_session_id')
                    ->copyable()
                    ->limit(20)
                    ->fontFamily(FontFamily::Mono)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                StatusFilter::make(),
                MethodFilter::make(),
                PaidAtFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
