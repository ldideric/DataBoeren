<?php

namespace App\Filament\Resources\Reservations\RelationManagers;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->recordUrl(fn ($record) => PaymentResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('amount')
                    ->label(__('reservation.payments.amount'))
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                TextColumn::make('status')
                    ->label(__('common.status'))
                    ->badge(),
                TextColumn::make('method')
                    ->label(__('reservation.payments.method')),
                TextColumn::make('paid_at')
                    ->label(__('reservation.payments.paid_at'))
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('stripe_session_id')
                    ->label(__('reservation.payments.stripe_session_id'))
                    ->copyable()
                    ->limit(20)
                    ->fontFamily(FontFamily::Mono),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
