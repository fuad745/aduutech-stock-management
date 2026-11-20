<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\StockMovement;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('adjust_stock')
                ->label('Adjust Stock')
                ->icon('heroicon-o-arrows-up-down')
                ->color('warning')
                ->form([
                    Select::make('movement_type')
                        ->label('Action')
                        ->options([
                            'addition' => 'Add stock',
                            'removal'  => 'Remove stock',
                            'set'      => 'Set exact quantity',
                        ])
                        ->required()
                        ->live(),
                    Select::make('batch_id')
                        ->label('Batch / Lot (FEFO)')
                        ->placeholder('Select batch (optional)')
                        ->visible(fn ($get) => $get('movement_type') === 'removal')
                        ->options(function () {
                            $record = $this->getRecord();
                            return Batch::where('product_id', $record->id)
                                ->inStock()
                                ->orderBy('expiry_date')
                                ->get()
                                ->mapWithKeys(fn (Batch $b) => [
                                    $b->id => $b->lot_number
                                        . ($b->expiry_date ? ' (exp: ' . $b->expiry_date->format('d/m/Y') . ')' : '')
                                        . ' — qty: ' . $b->quantity,
                                ])
                                ->all();
                        }),
                    Select::make('customer_id')
                        ->label('Customer / Facility')
                        ->placeholder('Select recipient (optional)')
                        ->visible(fn ($get) => $get('movement_type') === 'removal')
                        ->searchable()
                        ->options(fn () => Customer::orderBy('name')->pluck('name', 'id')->all()),
                    TextInput::make('quantity')
                        ->label(fn ($get) => $get('movement_type') === 'set' ? 'New quantity' : 'Quantity')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    Textarea::make('note')
                        ->label('Reason / Note')
                        ->rows(2)
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $record = $this->getRecord();
                    $before = (int) $record->quantity;
                    $qty    = (int) $data['quantity'];

                    $after = match ($data['movement_type']) {
                        'addition' => $before + $qty,
                        'removal'  => max(0, $before - $qty),
                        'set'      => $qty,
                    };

                    $type = $data['movement_type'] === 'set' ? 'adjustment' : $data['movement_type'];

                    StockMovement::create([
                        'product_id'      => $record->id,
                        'batch_id'        => $data['batch_id'] ?? null,
                        'customer_id'     => $data['customer_id'] ?? null,
                        'user_id'         => Auth::id() ?: null,
                        'type'            => $type,
                        'quantity_before' => $before,
                        'quantity_after'  => $after,
                        'quantity_change' => $after - $before,
                        'note'            => $data['note'] ?? null,
                    ]);

                    if (! empty($data['batch_id']) && $data['movement_type'] === 'removal') {
                        $batch = Batch::find($data['batch_id']);
                        if ($batch) {
                            $batch->decrement('quantity', $qty);
                        }
                    }

                    $record->updateQuietly(['quantity' => $after]);
                    $this->refreshFormData(['quantity']);
                }),
        ];
    }
}
