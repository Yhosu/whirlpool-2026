<?php

namespace App\Filament\Exports;

use App\Models\ParticipantCoupon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParticipantCouponExporter extends Exporter
{
    protected static ?string $model = ParticipantCoupon::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('sale_id'),
            ExportColumn::make('prize_id'),
            ExportColumn::make('participant_id'),
            ExportColumn::make('business_unit'),
            ExportColumn::make('code'),
            ExportColumn::make('status'),
            ExportColumn::make('active'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de cupones se ha completado y se han exportado '.number_format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).'.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron al exportar.';
        }

        return $body;
    }
}
