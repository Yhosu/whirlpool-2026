<?php

namespace App\Filament\Resources\PrizeStockHistoryResource\Pages;

use App\Filament\Resources\PrizeStockHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrizeStockHistory extends ViewRecord
{
    protected static string $resource = PrizeStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
