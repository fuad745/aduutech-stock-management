<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('product_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('brand')
                            ->maxLength(255),
                        TextInput::make('model')
                            ->maxLength(255),
                        TextInput::make('type')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Inventory & Pricing')
                    ->columns(2)
                    ->schema([
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('ETB')
                            ->minValue(0)
                            ->required(),
                    ]),

                Section::make('Media & Description')
                    ->columns(1)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Product Image')
                            ->image()
                            ->imageEditor()
                            ->directory('products')
                            ->disk('public')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('600')
                            ->maxSize(4096)
                            ->nullable()
                            ->downloadable()
                            ->openable()
                            ->preserveFilenames(),
                        Textarea::make('description')
                            ->rows(6)
                            ->placeholder('Describe the product...')
                            ->columnSpanFull()
                            ->nullable(),
                    ]),
            ]);
    }
}
