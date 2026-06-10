<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class MonthlyRevenueWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    public function getHeading(): ?string
    {
        return __('widget.monthly_revenue.heading');
    }

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->startOfMonth()->subMonths($i));

        $revenues = $months->map(fn (Carbon $month) => round(
            Payment::where('status', PaymentStatus::Paid)
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('amount') / 100,
            2,
        ));

        return [
            'datasets' => [
                [
                    'label' => __('widget.monthly_revenue.dataset'),
                    'data' => $revenues->values()->toArray(),
                    'backgroundColor' => '#f59e0b',
                ],
            ],
            'labels' => $months->map(fn (Carbon $month) => $month->format('M Y'))->toArray(),
        ];
    }
}
