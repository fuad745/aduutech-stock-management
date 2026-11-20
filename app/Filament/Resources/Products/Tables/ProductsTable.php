<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Batch;
use App\Models\StockMovement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('product_name')
                    ->searchable(),
                TextColumn::make('brand')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('price')
                    ->searchable()->suffix(' ETB')->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            SelectFilter::make('brand')
                ->options(fn () => \App\Models\Product::query()->select('brand')->distinct()->pluck('brand', 'brand')->all()),
            SelectFilter::make('type')
                ->options([
                    'Dialysis consumables' => 'Dialysis consumables',
                    'Medical devices' => 'Medical devices',
                    'Reagents' => 'Reagents',

                ]),
            TernaryFilter::make('has_image')
                ->label('Has image')
                ->trueLabel('With image')
                ->falseLabel('Without image')
                ->queries(
                    true: fn (Builder $query) => $query->whereNotNull('image')->where('image', '!=', ''),
                    false: fn (Builder $query) => $query->whereNull('image')->orWhere('image', ''),
                    blank: fn (Builder $query) => $query,
                ),
            SelectFilter::make('stock')
                ->label('Stock Status')
                ->options([
                    'out' => 'Out of stock',
                    'low' => 'Low stock (≤ 5)',
                    'in' => 'In stock (> 5)',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return match ($data['value'] ?? null) {
                        'out' => $query->where('quantity', '=', 0),
                        'low' => $query->whereBetween('quantity', [1, 5]),
                        'in' => $query->where('quantity', '>', 5),
                        default => $query,
                    };
                }),
            ])
            ->recordActions([
                ViewAction::make(),
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
                            ->options(function ($livewire) {
                                $record = $livewire->getMountedTableActionRecord();
                                if (! $record) {
                                    return [];
                                }
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
                    ->action(function (array $data, $record): void {
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
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
