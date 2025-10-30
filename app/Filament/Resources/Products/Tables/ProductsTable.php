<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
