<?php

namespace App\Filament\Exports;

use App\Models\Sale;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SaleExporter extends Exporter
{
    protected static ?string $model = Sale::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('participant_id'),
            ExportColumn::make('city_agency'),
            ExportColumn::make('name'),
            ExportColumn::make('sale_date'),
            ExportColumn::make('control_code'),
            ExportColumn::make('business_unit'),
            ExportColumn::make('quantity_items'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('invoice_url'),
            ExportColumn::make('invoice_number'),
            ExportColumn::make('sale_type'),
            ExportColumn::make('active'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de ventas se ha completado y se han exportado '.number_format($export->successful_rows).' '.str('fila')->plural($export->successful_rows).'.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('fila')->plural($failedRowsCount).' fallaron al exportar.';
        }

        return $body;
    }
}
