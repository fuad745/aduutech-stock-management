<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LowStockStats extends BaseWidget
{
    protected function getStats(): array
    {
        $lowThreshold = 5;

        $outOfStock = Product::query()->where('quantity', '<=', 0)->count();
        $lowStock = Product::query()->whereBetween('quantity', [1, $lowThreshold])->count();
        $total = Product::count();

        return [
            Stat::make('Out of Stock', (string) $outOfStock)
                ->description('Quantity = 0')
                ->color('danger')
                ->icon('heroicon-o-no-symbol'),

            Stat::make('Low Stock', (string) $lowStock)
                ->description("Quantity ≤ {$lowThreshold}")
                ->color('warning')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Total Products', (string) $total)
                ->description('Inventory size')
                ->color('success')
                ->icon('heroicon-o-cube'),
        ];
    }
}
