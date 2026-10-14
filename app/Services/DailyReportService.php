<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Form;
use App\Models\Prize;
use App\Models\PrizeBranch;
use OpenSpout\Common\Entity\Style\Style;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class DailyReportService
{
    /**
     * Obtener resumen de métricas para el reporte diario adaptado al modelo Form.
     */
    public function getSummaryData(?string $dateStart = null, ?string $dateEnd = null): array
    {
        $query = Form::query()
            ->when($dateStart, fn ($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->whereDate('created_at', '<=', $dateEnd));

        $totalForms = (clone $query)->count();
        $totalUtilizados = (clone $query)->where('status', 'used')->count();
        $totalEntregados = (clone $query)->where('status', 'delivered')->count();
        $totalPendientes = (clone $query)->whereIn('status', ['registered', 'pending'])->count();
        $totalInvalidos = (clone $query)->where('status', 'invalidated')->count();
        $totalConPremio = (clone $query)->whereNotNull('prize_id')->count();
        $totalDismac = (clone $query)->where('type', 'dismac')->count();
        $totalInformal = (clone $query)->where('type', 'informal-market')->count();

        // Agrupados por Premio asignado
        $porPremio = (clone $query)
            ->whereNotNull('prize_id')
            ->selectRaw('prize_id, COUNT(*) as total')
            ->groupBy('prize_id')
            ->with('prize')
            ->get()
            ->sortByDesc('total');

        // Agrupados por Sucursal
        $porSucursal = (clone $query)
            ->whereNotNull('branch_id')
            ->selectRaw('branch_id, COUNT(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get()
            ->sortByDesc('total');

        // Agrupados por Ciudad
        $porCiudad = (clone $query)
            ->whereNotNull('city')
            ->selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->get()
            ->sortByDesc('total');

        return compact(
            'totalForms',
            'totalUtilizados',
            'totalEntregados',
            'totalPendientes',
            'totalInvalidos',
            'totalConPremio',
            'totalDismac',
            'totalInformal',
            'porSucursal',
            'porPremio',
            'porCiudad'
        );
    }

    /**
     * Formatear fila de Form reutilizando el template predefinido.
     */
    public function formatFormRow(Form $form): array
    {
        return [
            'ID' => $form->id,
            'Fecha                       ' => $form->created_at ? $form->created_at->format('d/m/Y') : '',
            'Hora                        ' => $form->created_at ? $form->created_at->format('H:i') : '',
            'Cliente / Participante      ' => $form->name,
            'CI                          ' => $form->ci_number,
            'Teléfono                    ' => $form->cellphone,
            'Ciudad                      ' => $form->city,
            'Código Mercado Informal     ' => $form->code,
            'Voucher Asignado            ' => optional($form->assignedCode)->code ?? '',
            'Premio                      ' => optional($form->prize)->name ?? 'S/P',
            'Unidad de Negocio           ' => $form->business_unit,
            'Tipo de Registro            ' => match ($form->type) {
                'dismac' => 'Dismac',
                'informal-market' => 'Mercado Informal',
                default => $form->type ?? '',
            },
            'Estado                      ' => match ($form->status) {
                'delivered' => 'Entregado',
                'invalidated' => 'Invalidado',
                'registered' => 'Registrado',
                'pending' => 'Pendiente',
                'used' => 'Utilizado / Girado',
                default => $form->status ?? '',
            },
            'Sucursal Destino            ' => optional($form->branch)->name ?? 'No asignada',
            'Código Control Venta        ' => optional($form->sale)->control_code ?? $form->control_code ?? 'N/A',
            'Usuario Entrega             ' => optional($form->user)->name ?? 'S/U',
            'Observaciones               ' => $form->observations ?? '',
        ];
    }

    /**
     * Generar las 3 pestañas requeridas para el Excel:
     * 1. Registros del día anterior
     * 2. Total a la fecha
     * 3. Stock a la fecha
     */
    public function generateDailyReportSheets(?string $yesterday = null): SheetCollection
    {
        $targetDate = $yesterday ?: now()->subDay()->toDateString();

        // 1. Pestaña: Registros del día anterior
        $yesterdayForms = Form::query()
            ->whereDate('created_at', $targetDate)
            ->with(['prize', 'branch', 'sale', 'user', 'assignedCode'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($form) => $this->formatFormRow($form));

        // 2. Pestaña: Total a la fecha
        $totalForms = Form::query()
            ->whereDate('created_at', '<=', $targetDate)
            ->with(['prize', 'branch', 'sale', 'user', 'assignedCode'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($form) => $this->formatFormRow($form));

        // 3. Pestaña: Stock a la fecha
        $prizes = Prize::orderBy('name')->get();
        $branches = Branch::orderBy('city')->orderBy('name')->get();
        $stockMap = PrizeBranch::all()
            ->mapWithKeys(fn ($item) => ["{$item->prize_id}.{$item->branch_id}" => $item->stock]);

        $matrixData = collect();
        foreach ($branches as $branch) {
            $row = [
                'Sucursal                    ' => $branch->name,
                'Ciudad                      ' => $branch->city,
                'Canal                       ' => $branch->channel,
            ];
            $totalStock = 0;
            foreach ($prizes as $prize) {
                $stock = (int) $stockMap->get("{$prize->id}.{$branch->id}", 0);
                $row[$prize->name.'          '] = $stock;
                $totalStock += $stock;
            }
            $row['TOTAL STOCK            '] = $totalStock;
            $matrixData->push($row);
        }

        return new SheetCollection([
            'Día Anterior' => $yesterdayForms,
            'Total a la Fecha' => $totalForms,
            'Stock a la Fecha' => $matrixData,
        ]);
    }

    /**
     * Exportar el Excel con estilo a la ruta indicada.
     */
    public function exportDailyReportToPath(string $path, ?string $yesterday = null): string
    {
        $sheets = $this->generateDailyReportSheets($yesterday);
        $headerStyle = (new Style)
            ->setFontBold()
            ->setBackgroundColor('E2E8F0');

        (new FastExcel($sheets))
            ->headerStyle($headerStyle)
            ->export($path);

        return $path;
    }
}
