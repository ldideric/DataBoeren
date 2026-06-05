<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TodayArrivalsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function getSort(): int
    {
        return Auth::user()?->isAdmin() ? 4 : 2;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('widget.today_arrivals.heading'))
            ->query(
                Reservation::query()
                    ->with(['customer', 'campsite'])
                    ->whereDate('check_in', today())
                    ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Pending])
                    ->orderBy('check_in'),
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('widget.today_arrivals.guest'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('campsite.name')
                    ->label(__('widget.today_arrivals.campsite'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('guests')
                    ->label(__('widget.today_arrivals.guests'))
                    ->state(fn (Reservation $r) => $r->num_adults.'a / '.$r->num_children.'c'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label(__('widget.today_arrivals.check_out'))
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('common.status'))
                    ->badge(),
            ])
            ->paginated(false);
    }
}
