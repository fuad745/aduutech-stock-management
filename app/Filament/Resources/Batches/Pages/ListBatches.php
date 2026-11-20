<?php

namespace App\Filament\Resources\Batches\Pages;

use App\Filament\Imports\BatchImporter;
use App\Filament\Resources\Batches\BatchResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListBatches extends ListRecords
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->importer(BatchImporter::class)
                ->label('Import CSV'),
        ];
    }
}
