<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('method')
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
                SelectFilter::make('status')
                    ->options(PaymentStatus::class),
                // @todo SelectFilter for method (ideal/card/cash) — useful for end-of-day cash reconciliation
                // @todo Filter for paid_at date range — scope payments to a billing or reporting period
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
