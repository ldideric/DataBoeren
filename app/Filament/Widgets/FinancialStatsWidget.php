<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialStatsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $monthlyRevenueCents = Payment::where('status', PaymentStatus::Paid)
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('amount');

        $pendingPayments = Payment::where('status', PaymentStatus::Pending)->count();

        return [
            Stat::make('Revenue this month', '€ '.number_format($monthlyRevenueCents / 100, 2, ',', '.'))
                ->description('From paid payments')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Pending payments', $pendingPayments)
                ->description('Awaiting payment')
                ->icon('heroicon-o-credit-card')
                ->color($pendingPayments > 0 ? 'danger' : 'gray'),
        ];
    }
}
