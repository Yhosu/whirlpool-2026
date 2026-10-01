<?php

namespace App\Filament\Resources\ParticipantCouponResource\Pages;

use App\Filament\Exports\ParticipantCouponExporter;
use App\Filament\Resources\ParticipantCouponResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipantCoupons extends ListRecords
{
    protected static string $resource = ParticipantCouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(ParticipantCouponExporter::class)
                ->label('Exportar Excel')
                ->color('success'),
        ];
    }
}
