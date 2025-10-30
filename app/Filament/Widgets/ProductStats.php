<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $totalQuantity = (int) Product::sum('quantity');
        $averagePrice = (float) Product::query()
            ->whereNotNull('price')
            ->avg('price');

        return [
            Stat::make('Total Products', number_format($totalProducts))
                ->icon('heroicon-o-cube'),
            Stat::make('Total Quantity', number_format($totalQuantity))
                ->icon('heroicon-o-archive-box'),
            Stat::make('Average Price', number_format($averagePrice ?? 0, 2) . ' ETB')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
