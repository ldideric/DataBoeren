<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ReservationsByStatusWidget extends ChartWidget
{
    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('widget.reservations_by_status.heading');
    }

    public static function getSort(): int
    {
        return Auth::user()?->isAdmin() ? 2 : 3;
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = collect(ReservationStatus::cases())
            ->mapWithKeys(fn (ReservationStatus $status) => [
                $status->getLabel() => Reservation::where('status', $status)->count(),
            ]);

        return [
            'datasets' => [
                [
                    'data' => $counts->values()->toArray(),
                    'backgroundColor' => ['#f59e0b', '#22c55e', '#ef4444'],
                ],
            ],
            'labels' => $counts->keys()->toArray(),
        ];
    }
}
