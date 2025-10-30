<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('image')
                            ->label('Product Image')
                            ->disk('public')
                            ->height(200)
                            ->hidden(fn ($record) => blank($record?->image)),
                        TextEntry::make('description')
                            ->html()
                            ->prose()
                            ->placeholder('No description provided')
                            ->columnSpan(1),
                    ]),

                Section::make('Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('product_name')->label('Name'),
                        TextEntry::make('brand'),
                        TextEntry::make('model'),
                        TextEntry::make('type'),
                        TextEntry::make('price')->suffix(' ETB'),
                        TextEntry::make('quantity')->numeric(),
                    ]),

                Section::make('Timestamps')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->dateTime()->placeholder('-'),
                        TextEntry::make('updated_at')->dateTime()->placeholder('-'),
                    ]),

                Section::make('Audit')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('creator.name')->label('Created by')->placeholder('-'),
                        TextEntry::make('updater.name')->label('Last updated by')->placeholder('-'),
                    ]),
            ]);
    }
}
