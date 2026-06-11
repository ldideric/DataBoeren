<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Filament\Resources\Reservations\ReservationResource;
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
            Stat::make(__('widget.operational.arrivals_today'), $todayArrivals)
                ->description(__('widget.operational.arrivals_today_desc'))
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success')
                ->url(ReservationResource::getUrl(parameters: [
                    'filters' => [
                        'check_in' => ['from' => today()->toDateString(), 'until' => today()->toDateString()],
                        'status' => ['value' => ReservationStatus::Confirmed->value],
                    ],
                ])),

            Stat::make(__('widget.operational.departures_today'), $todayDepartures)
                ->description(__('widget.operational.departures_today_desc'))
                ->icon('heroicon-o-arrow-left-circle')
                ->color('warning')
                ->url(ReservationResource::getUrl(parameters: [
                    'filters' => [
                        'check_out' => ['from' => today()->toDateString(), 'until' => today()->toDateString()],
                        'status' => ['value' => ReservationStatus::Confirmed->value],
                    ],
                ])),

            Stat::make(__('widget.operational.on_site'), $currentlyOnSite)
                ->description(__('widget.operational.on_site_desc'))
                ->icon('heroicon-o-home')
                ->color('info')
                ->url(ReservationResource::getUrl(parameters: [
                    'filters' => ['on_site' => ['isActive' => true]],
                ])),

            Stat::make(__('widget.operational.pending'), $pendingReservations)
                ->description(__('widget.operational.pending_desc'))
                ->icon('heroicon-o-clock')
                ->color($pendingReservations > 0 ? 'warning' : 'gray')
                ->url(ReservationResource::getUrl(parameters: [
                    'filters' => ['status' => ['value' => ReservationStatus::Pending->value]],
                ])),
        ];
    }
}
