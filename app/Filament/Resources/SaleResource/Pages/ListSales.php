<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Exports\SaleExporter;
use App\Filament\Resources\SaleResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListSales extends ListRecords
{
    protected static string $resource = SaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(SaleExporter::class)
                ->label('Exportar Excel')
                ->color('success'),
        ];
    }
}
