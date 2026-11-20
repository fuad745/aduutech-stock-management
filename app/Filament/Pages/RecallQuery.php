<?php

namespace App\Filament\Pages;

use App\Models\StockMovement;
use Filament\Pages\Page;

class RecallQuery extends Page
{
    protected static ?string $navigationLabel = 'Recall Query';

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.recall-query';

    public string $lotNumber = '';
    public array $results = [];
    public bool $searched = false;

    public function search(): void
    {
        $this->validate([
            'lotNumber' => 'required|string|min:1',
        ]);

        $movements = StockMovement::query()
            ->join('batches', 'batches.id', '=', 'stock_movements.batch_id')
            ->where('batches.lot_number', $this->lotNumber)
            ->where('stock_movements.type', 'removal')
            ->with(['product', 'batch', 'customer', 'user'])
            ->select('stock_movements.*')
            ->orderBy('stock_movements.created_at', 'desc')
            ->get();

        $this->results = $movements->toArray();
        $this->searched = true;
    }

    public function getMovements(): \Illuminate\Support\Collection
    {
        if (empty($this->results)) {
            return collect();
        }

        return StockMovement::query()
            ->join('batches', 'batches.id', '=', 'stock_movements.batch_id')
            ->where('batches.lot_number', $this->lotNumber)
            ->where('stock_movements.type', 'removal')
            ->with(['product', 'batch', 'customer', 'user'])
            ->select('stock_movements.*')
            ->orderBy('stock_movements.created_at', 'desc')
            ->get();
    }
}
