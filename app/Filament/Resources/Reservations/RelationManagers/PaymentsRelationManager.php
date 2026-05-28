<?php

namespace App\Filament\Resources\Reservations\RelationManagers;

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
            ->columns([
                TextColumn::make('amount')
                    ->formatStateUsing(fn ($state) => '€ ' . number_format($state / 100, 2, ',', '.')),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('method'),
                TextColumn::make('paid_at')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('stripe_session_id')
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
