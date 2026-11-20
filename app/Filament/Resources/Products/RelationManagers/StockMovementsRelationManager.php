<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $title = 'Stock History';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'initial'    => 'info',
                        'addition'   => 'success',
                        'removal'    => 'danger',
                        'adjustment' => 'warning',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('quantity_before')
                    ->label('Before')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('quantity_after')
                    ->label('After')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('quantity_change')
                    ->label('Change')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray'))
                    ->formatStateUsing(fn (int $state): string => ($state > 0 ? '+' : '') . $state),

                TextColumn::make('note')
                    ->placeholder('—')
                    ->wrap(),

                TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('—'),
            ])
            ->paginated([10, 25, 50]);
    }
}
