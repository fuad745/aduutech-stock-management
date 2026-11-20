<?php

namespace App\Filament\Widgets;

use App\Models\Batch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpiringBatchesWidget extends BaseWidget
{
    protected static ?string $heading = 'Batch Expiry Alerts';

    protected function getStats(): array
    {
        $expiring30  = Batch::inStock()->expiringSoon(30)->count();
        $expiring60  = Batch::inStock()->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>', now()->addDays(30))
            ->whereDate('expiry_date', '<=', now()->addDays(60))
            ->count();
        $expiring90  = Batch::inStock()->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>', now()->addDays(60))
            ->whereDate('expiry_date', '<=', now()->addDays(90))
            ->count();

        return [
            Stat::make('Expiring ≤ 30 days', (string) $expiring30)
                ->description('Needs immediate attention')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle'),

            Stat::make('Expiring 31–60 days', (string) $expiring60)
                ->description('Plan ahead')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Expiring 61–90 days', (string) $expiring90)
                ->description('Monitor closely')
                ->color('info')
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}
