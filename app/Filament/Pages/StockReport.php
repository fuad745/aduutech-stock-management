<?php

namespace App\Filament\Pages;

use App\Models\StockMovement;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class StockReport extends Page
{
    protected static ?string $navigationLabel = 'Stock Report';

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.stock-report';

    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    public function getEmployeeStats(): \Illuminate\Support\Collection
    {
        $query = StockMovement::query()
            ->select([
                'stock_movements.user_id',
                DB::raw('COALESCE(users.name, \'System / API\') as employee_name'),
                DB::raw('COUNT(*) as total_movements'),
                DB::raw('SUM(CASE WHEN quantity_change > 0 THEN quantity_change ELSE 0 END) as total_added'),
                DB::raw('SUM(CASE WHEN quantity_change < 0 THEN ABS(quantity_change) ELSE 0 END) as total_removed'),
                DB::raw('SUM(quantity_change) as net_change'),
                DB::raw('MAX(stock_movements.created_at) as last_movement'),
            ])
            ->leftJoin('users', 'users.id', '=', 'stock_movements.user_id')
            ->groupBy('stock_movements.user_id', 'users.name')
            ->orderByDesc('total_movements');

        if ($this->dateFrom) {
            $query->whereDate('stock_movements.created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('stock_movements.created_at', '<=', $this->dateTo);
        }

        return $query->get();
    }

    public function getSummary(): array
    {
        $base = StockMovement::query();

        if ($this->dateFrom) {
            $base->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $base->whereDate('created_at', '<=', $this->dateTo);
        }

        $totalMovements = (clone $base)->count();
        $totalAdded     = (clone $base)->where('quantity_change', '>', 0)->sum('quantity_change');
        $totalRemoved   = abs((clone $base)->where('quantity_change', '<', 0)->sum('quantity_change'));

        return [
            'total_movements' => $totalMovements,
            'total_added'     => $totalAdded,
            'total_removed'   => $totalRemoved,
        ];
    }
}
