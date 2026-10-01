<?php

namespace App\Filament\Resources\PrizeStockHistoryResource\Pages;

use App\Filament\Resources\PrizeStockHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListPrizeStockHistories extends ListRecords
{
    protected static string $resource = PrizeStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
