<?php

namespace App\Filament\Widgets;

use App\Models\StockMovement;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class StockMovementsByEmployeeWidget extends Widget
{
    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.stock-movements-by-employee-widget';

    public function getEmployeeStats(): \Illuminate\Support\Collection
    {
        return StockMovement::query()
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
            ->orderByDesc('total_movements')
            ->get();
    }
}
