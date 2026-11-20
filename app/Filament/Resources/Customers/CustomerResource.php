<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\ViewCustomer;
use App\Models\Customer;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            Select::make('type')
                ->required()
                ->options([
                    'hospital'  => 'Hospital',
                    'clinic'    => 'Clinic',
                    'pharmacy'  => 'Pharmacy',
                    'lab'       => 'Laboratory',
                    'other'     => 'Other',
                ]),

            TextInput::make('contact_person')
                ->maxLength(255),

            TextInput::make('phone')
                ->tel()
                ->maxLength(50),

            TextInput::make('email')
                ->email()
                ->maxLength(255),

            TextInput::make('city')
                ->maxLength(255),

            Textarea::make('address')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hospital' => 'info',
                        'clinic'   => 'success',
                        'pharmacy' => 'warning',
                        'lab'      => 'primary',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('city')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('contact_person')
                    ->placeholder('—'),

                TextColumn::make('phone')
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'hospital'  => 'Hospital',
                        'clinic'    => 'Clinic',
                        'pharmacy'  => 'Pharmacy',
                        'lab'       => 'Laboratory',
                        'other'     => 'Other',
                    ]),
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
            'index'  => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'view'   => ViewCustomer::route('/{record}'),
            'edit'   => EditCustomer::route('/{record}/edit'),
        ];
    }
}
