<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayArrivalsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading("Today's arrivals")
            ->query(
                Reservation::query()
                    ->with(['customer', 'campsite'])
                    ->whereDate('check_in', today())
                    ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Pending])
                    ->orderBy('check_in'),
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Guest')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('campsite.name')
                    ->label('Campsite')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guests')
                    ->label('Guests')
                    ->state(fn (Reservation $r) => $r->num_adults.'a / '.$r->num_children.'c'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Check-out')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->paginated(false);
    }
}
