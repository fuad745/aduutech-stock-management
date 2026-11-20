<?php

namespace App\Filament\Pages;

use App\Models\Batch;
use App\Models\StockCountItem;
use App\Models\StockCountSession;
use App\Models\StockMovement;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockCount extends Page
{
    protected static ?string $navigationLabel = 'Stock Count';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.stock-count';

    public string $newSessionName = '';
    public bool $showNewSessionForm = false;
    public ?int $activeSessionId = null;

    public function getSessions(): \Illuminate\Support\Collection
    {
        return StockCountSession::with('creator')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getActiveSession(): ?StockCountSession
    {
        if (! $this->activeSessionId) {
            return null;
        }

        return StockCountSession::with(['items.batch.product'])->find($this->activeSessionId);
    }

    public function startNewSession(): void
    {
        $this->validate(['newSessionName' => 'required|string|min:2|max:255']);

        $session = StockCountSession::create([
            'name'   => $this->newSessionName,
            'status' => 'in_progress',
        ]);

        // Snapshot all in-stock batches
        $batches = Batch::where('quantity', '>', 0)->with('product')->get();

        foreach ($batches as $batch) {
            StockCountItem::create([
                'session_id'      => $session->id,
                'batch_id'        => $batch->id,
                'system_quantity' => $batch->quantity,
            ]);
        }

        $this->activeSessionId  = $session->id;
        $this->newSessionName   = '';
        $this->showNewSessionForm = false;
    }

    public function saveCount(int $itemId, ?int $countedQty, ?string $note): void
    {
        $item = StockCountItem::find($itemId);
        if (! $item) {
            return;
        }

        $discrepancy = $countedQty !== null ? ($countedQty - $item->system_quantity) : null;

        $item->update([
            'counted_quantity' => $countedQty,
            'discrepancy'      => $discrepancy,
            'note'             => $note,
        ]);
    }

    public function completeSession(int $sessionId): void
    {
        $session = StockCountSession::with('items.batch')->find($sessionId);
        if (! $session || $session->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($session) {
            foreach ($session->items as $item) {
                if ($item->discrepancy === null || $item->discrepancy === 0) {
                    continue;
                }

                $batch = $item->batch;
                if (! $batch) {
                    continue;
                }

                $before = $batch->quantity;
                $after  = max(0, $before + $item->discrepancy);

                // Create adjustment movement
                StockMovement::create([
                    'product_id'      => $batch->product_id,
                    'batch_id'        => $batch->id,
                    'user_id'         => Auth::id() ?: null,
                    'type'            => 'adjustment',
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'quantity_change' => $item->discrepancy,
                    'note'            => 'Stock count adjustment — ' . $session->name . ($item->note ? ': ' . $item->note : ''),
                ]);

                // Update batch quantity
                $batch->update(['quantity' => $after]);
            }

            $session->update([
                'status'       => 'completed',
                'completed_at' => now(),
                'completed_by' => Auth::id() ?: null,
            ]);
        });

        $this->activeSessionId = null;
    }

    public function openSession(int $sessionId): void
    {
        $this->activeSessionId = $sessionId;
    }
}
