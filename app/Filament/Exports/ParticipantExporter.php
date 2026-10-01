<?php

namespace App\Filament\Exports;

use App\Models\Participant;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParticipantExporter extends Exporter
{
    protected static ?string $model = Participant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('cellphone'),
            ExportColumn::make('birth_date'),
            ExportColumn::make('ci_number'),
            ExportColumn::make('document_type'),
            ExportColumn::make('ci_number_complement'),
            ExportColumn::make('ci_number_expedition'),
            ExportColumn::make('city'),
            ExportColumn::make('status'),
            ExportColumn::make('email'),
            ExportColumn::make('date'),
            ExportColumn::make('time'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de participantes se ha completado y se han exportado '.number_format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).'.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron al exportar.';
        }

        return $body;
    }
}
