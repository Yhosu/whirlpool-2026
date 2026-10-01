<?php

namespace App\Filament\Resources\ParticipantResource\Pages;

use App\Filament\Exports\ParticipantExporter;
use App\Filament\Resources\ParticipantResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make()
                ->exporter(ParticipantExporter::class)
                ->label('Exportar Excel')
                ->color('success'),
        ];
    }
}
