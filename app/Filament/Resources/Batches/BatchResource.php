<?php

namespace App\Filament\Resources\Batches;

use App\Filament\Resources\Batches\Pages\CreateBatch;
use App\Filament\Resources\Batches\Pages\EditBatch;
use App\Filament\Resources\Batches\Pages\ListBatches;
use App\Filament\Resources\Batches\Pages\ViewBatch;
use App\Models\Batch;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = 'Batches';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?string $recordTitleAttribute = 'lot_number';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'product_name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('lot_number')
                ->label('Lot Number')
                ->required()
                ->maxLength(255),

            DatePicker::make('expiry_date')
                ->label('Expiry Date'),

            DatePicker::make('received_date')
                ->label('Received Date'),

            TextInput::make('supplier')
                ->label('Supplier')
                ->maxLength(255),

            TextInput::make('landed_cost')
                ->label('Landed Cost (ETB)')
                ->numeric()
                ->minValue(0),

            Select::make('storage_condition')
                ->label('Storage Condition')
                ->options([
                    'Room Temperature' => 'Room Temperature',
                    'Refrigerated 2-8°C' => 'Refrigerated 2-8°C',
                    'Frozen -20°C' => 'Frozen -20°C',
                    'Controlled' => 'Controlled',
                ]),

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->minValue(0)
                ->required()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('expiry_date', 'asc')
            ->columns([
                TextColumn::make('product.product_name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lot_number')
                    ->label('Lot Number')
                    ->searchable(),

                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        $record->expiry_date === null             => 'gray',
                        $record->expiry_date->isPast()            => 'danger',
                        $record->expiry_date->diffInDays() <= 30  => 'warning',
                        default                                   => 'success',
                    })
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('supplier')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('received_date')
                    ->label('Received')
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('storage_condition')
                    ->label('Storage')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->relationship('product', 'product_name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('has_stock')
                    ->label('Has Stock')
                    ->trueLabel('In stock')
                    ->falseLabel('Out of stock')
                    ->queries(
                        true:  fn (Builder $q) => $q->where('quantity', '>', 0),
                        false: fn (Builder $q) => $q->where('quantity', '<=', 0),
                        blank: fn (Builder $q) => $q,
                    ),

                Filter::make('expiring_soon')
                    ->label('Expiring within 90 days')
                    ->query(fn (Builder $q) => $q->expiringSoon(90)),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBatches::route('/'),
            'create' => CreateBatch::route('/create'),
            'view'   => ViewBatch::route('/{record}'),
            'edit'   => EditBatch::route('/{record}/edit'),
        ];
    }
}
