<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductsByBrandChart extends ChartWidget
{
    protected ?string $heading = 'Brands';
    protected ?string $maxHeight = '220px';

    protected function getData(): array
    {
        $data = Product::query()
            ->selectRaw('brand, COUNT(*) as total')
            ->groupBy('brand')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $data->pluck('total')->all(),
                    'backgroundColor' => [
                        '#60a5fa','#f87171','#34d399','#fbbf24','#a78bfa',
                    ],
                ],
            ],
            'labels' => $data->pluck('brand')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getColumns(): int|array
    {
        return 1;
    }
}
