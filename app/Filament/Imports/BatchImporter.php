<?php

namespace App\Filament\Imports;

use App\Models\Batch;
use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BatchImporter extends Importer
{
    protected static ?string $model = Batch::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('product_name')
                ->label('Product Name')
                ->requiredMapping()
                ->rules(['required', 'string'])
                ->fillRecordUsing(function (Batch $record, string $state): void {
                    $product = Product::where('product_name', $state)->first();
                    if ($product) {
                        $record->product_id = $product->id;
                    }
                }),
            ImportColumn::make('lot_number')
                ->requiredMapping()
                ->rules(['required', 'string']),
            ImportColumn::make('expiry_date')
                ->rules(['nullable', 'date']),
            ImportColumn::make('supplier')
                ->rules(['nullable', 'string']),
            ImportColumn::make('received_date')
                ->rules(['nullable', 'date']),
            ImportColumn::make('landed_cost')
                ->rules(['nullable', 'numeric']),
            ImportColumn::make('storage_condition')
                ->rules(['nullable', 'string']),
            ImportColumn::make('quantity')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:0']),
        ];
    }

    public function resolveRecord(): ?Batch
    {
        // product_id from fillRecordUsing isn't available yet — look it up directly.
        $productId = Product::where('product_name', $this->data['product_name'] ?? '')
            ->value('id');

        return Batch::firstOrNew([
            'lot_number' => $this->data['lot_number'],
            'product_id' => $productId,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Batch import complete. ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed.';
        }

        return $body;
    }
}
