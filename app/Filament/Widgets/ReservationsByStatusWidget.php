<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Filament\Widgets\ChartWidget;

class ReservationsByStatusWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Reservations by status';

    protected int | string | array $columnSpan = 1;

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
