<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OperationalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayArrivals = Reservation::whereDate('check_in', today())
            ->where('status', ReservationStatus::Confirmed)
            ->count();

        $todayDepartures = Reservation::whereDate('check_out', today())
            ->where('status', ReservationStatus::Confirmed)
            ->count();

        $currentlyOnSite = Reservation::where('check_in', '<=', today())
            ->where('check_out', '>=', today())
            ->where('status', ReservationStatus::Confirmed)
            ->count();

        $pendingReservations = Reservation::where('status', ReservationStatus::Pending)->count();

        return [
            Stat::make('Arrivals today', $todayArrivals)
                ->description('Confirmed check-ins today')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success'),

            Stat::make('Departures today', $todayDepartures)
                ->description('Confirmed check-outs today')
                ->icon('heroicon-o-arrow-left-circle')
                ->color('warning'),

            Stat::make('Currently on site', $currentlyOnSite)
                ->description('Active confirmed stays')
                ->icon('heroicon-o-home')
                ->color('info'),

            Stat::make('Pending reservations', $pendingReservations)
                ->description('Awaiting confirmation')
                ->icon('heroicon-o-clock')
                ->color($pendingReservations > 0 ? 'warning' : 'gray'),
        ];
    }
}
