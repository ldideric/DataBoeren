<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | array | null $columns = 2;

    public static function canView(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $monthlyRevenueCents = Payment::where('status', PaymentStatus::Paid)
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $cashToCollect = Payment::where('status', PaymentStatus::Pending)->count();

        return [
            Stat::make(__('widget.financial.revenue_this_month'), '€ '.number_format($monthlyRevenueCents / 100, 2, ',', '.'))
                ->description(__('widget.financial.from_paid'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->url(PaymentResource::getUrl(parameters: [
                    'filters' => [
                        'status' => ['value' => PaymentStatus::Paid->value],
                        'paid_at' => [
                            'from' => now()->startOfMonth()->toDateString(),
                            'until' => now()->endOfMonth()->toDateString(),
                        ],
                    ],
                ])),

            Stat::make(__('widget.financial.cash_to_collect'), $cashToCollect)
                ->description(__('widget.financial.cash_to_collect_desc'))
                ->icon('heroicon-o-credit-card')
                ->color($cashToCollect > 0 ? 'warning' : 'gray')
                ->url(PaymentResource::getUrl(parameters: [
                    'filters' => ['status' => ['value' => PaymentStatus::Pending->value]],
                ])),
        ];
    }
}
