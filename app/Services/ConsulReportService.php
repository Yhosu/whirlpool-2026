<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\Form;
use OpenSpout\Common\Entity\Style\Style;
use Rap2hpoutre\FastExcel\FastExcel;
use Rap2hpoutre\FastExcel\SheetCollection;

class ConsulReportService
{
    public function getSummaryData(?string $dateStart = null, ?string $dateEnd = null): array
    {
        $query = Form::query()
            ->when($dateStart, fn ($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->whereDate('created_at', '<=', $dateEnd));

        $totalForms = (clone $query)->count();
        $totalEntregados = (clone $query)->where('status', 'delivered')->count();
        $totalInvalidos = (clone $query)->where('status', 'invalidated')->count();
        $totalPendientes = (clone $query)->whereIn('status', ['registered', 'pending'])->count();

        // Agrupados por Sucursal (sólo los entregados)
        $porSucursal = (clone $query)
            ->where('status', 'delivered')
            ->selectRaw('branch_id, COUNT(*) as total')
            ->groupBy('branch_id')
            ->with('branch')
            ->get()
            ->sortByDesc('total');

        // Agrupados por Premio (sólo los entregados)
        $porPremio = (clone $query)
            ->where('status', 'delivered')
            ->selectRaw('prize_id, COUNT(*) as total')
            ->groupBy('prize_id')
            ->with('prize')
            ->get()
            ->sortByDesc('total');

        // Agrupados por Ciudad (Global participaciones de todos los estados)
        $porCiudad = (clone $query)
            ->selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->get()
            ->sortByDesc('total');

        return compact(
            'totalForms', 'totalEntregados', 'totalInvalidos', 'totalPendientes',
            'porSucursal', 'porPremio', 'porCiudad'
        );
    }

    public function generateExcelSheets(?string $dateStart = null, ?string $dateEnd = null): SheetCollection
    {
        // Información de Generación
        $infoData = collect([
            ['CONCEPTO' => 'REPORTE', 'DETALLE' => 'SISTEMA DE GESTIÓN CONSUL'],
            ['CONCEPTO' => 'FECHA GENERACIÓN', 'DETALLE' => now()->format('d/m/Y H:i:s')],
            ['CONCEPTO' => 'RANGO INICIAL', 'DETALLE' => $dateStart ?: 'Desde el inicio'],
            ['CONCEPTO' => 'RANGO FINAL', 'DETALLE' => $dateEnd ?: 'Hasta hoy'],
            ['CONCEPTO' => 'USUARIO', 'DETALLE' => auth()->user()->name ?? 'Sistema / Automático'],
        ]);

        // 1. Pestaña Ecommerce
        $ecommerceData = Form::query()
            ->where('business_unit', 'ECO')
            ->when($dateStart, fn ($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->whereDate('created_at', '<=', $dateEnd))
            ->with(['prize', 'branch', 'sale'])
            ->get()
            ->map(fn ($form) => [
                'ID' => $form->id,
                'Fecha                       ' => $form->created_at->format('d/m/Y'),
                'Hora                        ' => $form->created_at->format('H:i'),
                'Cliente / Participante      ' => $form->name,
                'CI                          ' => $form->ci_number,
                'Teléfono                    ' => $form->cellphone,
                'Código Cupón                ' => $form->code,
                'Premio                      ' => optional($form->prize)->name ?? 'S/P',
                'Unidad de Negocio           ' => $form->business_unit,
                'Estado                      ' => match ($form->status) {
                    'delivered' => 'Entregado',
                    'invalidated' => 'Invalidado',
                    'registered' => 'Pendiente',
                    default => $form->status
                },
                'Sucursal Destino            ' => optional($form->branch)->name ?? 'No asignada',
                'Código Control Venta        ' => optional($form->sale)->control_code ?? 'N/A',
            ]);

        // 2. Pestaña Compras Físicas (No Ecommerce)
        $physicalData = Form::query()
            ->where(function ($q) {
                $q->where('business_unit', '!=', 'ECO')
                    ->orWhereNull('business_unit');
            })
            ->when($dateStart, fn ($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->whereDate('created_at', '<=', $dateEnd))
            ->with(['prize', 'branch'])
            ->get()
            ->sortBy(fn ($f) => $f->status === 'delivered' ? 0 : 1)
            ->map(fn ($form) => [
                'ID' => $form->id,
                'Fecha                       ' => $form->created_at->format('d/m/Y'),
                'Hora                        ' => $form->created_at->format('H:i'),
                'Cliente                     ' => $form->name,
                'CI                          ' => $form->ci_number,
                'Teléfono                    ' => $form->cellphone,
                'Unidad de Negocio           ' => $form->business_unit,
                'Estado                      ' => match ($form->status) {
                    'delivered' => 'Entregado',
                    'invalidated' => 'Invalidado',
                    'registered' => 'Pendiente',
                    default => $form->status
                },
                'Premio Asignado             ' => optional($form->prize)->name ?? 'Pendiente/Ninguno',
                'Sucursal de Entrega         ' => optional($form->branch)->name ?? 'Pendiente',
                'Ciudad                      ' => $form->city,
            ]);

        // 3. Pestaña Matriz de Stock
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
            foreach ($prizes as $prize) {
                $row[$prize->name.'          '] = $stockMap->get("{$prize->id}.{$branch->id}", 0);
            }
            $matrixData->push($row);
        }

        // 4. Entregas por Sucursal Matrix
        $deliveryMap = Form::query()
            ->where('status', 'delivered')
            ->when($dateStart, fn ($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->whereDate('created_at', '<=', $dateEnd))
            ->selectRaw('branch_id, prize_id, COUNT(*) as total')
            ->groupBy('branch_id', 'prize_id')
            ->get()
            ->mapWithKeys(fn ($item) => ["{$item->branch_id}.{$item->prize_id}" => $item->total]);

        $deliveryMatrix = collect();
        foreach ($branches as $branch) {
            $row = [
                'Sucursal                    ' => $branch->name,
                'Ciudad                      ' => $branch->city,
            ];
            foreach ($prizes as $prize) {
                $row[$prize->name.'          '] = $deliveryMap->get("{$branch->id}.{$prize->id}", 0);
            }
            $deliveryMatrix->push($row);
        }

        // 5. Pestaña Total Entregados
        $deliveredData = Form::query()
            ->where('status', 'delivered')
            ->when($dateStart, fn ($q) => $q->whereDate('created_at', '>=', $dateStart))
            ->when($dateEnd, fn ($q) => $q->whereDate('created_at', '<=', $dateEnd))
            ->with(['prize', 'branch', 'user'])
            ->get()
            ->map(fn ($form) => [
                'ID' => $form->id,
                'Fecha                       ' => $form->created_at->format('d/m/Y'),
                'Hora                        ' => $form->created_at->format('H:i'),
                'Cliente                     ' => $form->name,
                'CI                          ' => $form->ci_number,
                'Teléfono                    ' => $form->cellphone,
                'Unidad de Negocio           ' => $form->business_unit,
                'Ciudad                      ' => $form->city,
                'Estado                      ' => 'Entregado',
                'Premio                      ' => optional($form->prize)->name ?? 'S/P',
                'Sucursal                    ' => optional($form->branch)->name ?? 'N/A',
                'Usuario Entrega             ' => optional($form->user)->name ?? 'S/U',
                'Observaciones               ' => $form->observations,
            ]);

        return new SheetCollection([
            'INFO REPORTE' => $infoData->map(fn ($item) => [
                'CONCEPTO                    ' => $item['CONCEPTO'],
                'DETALLE DE REPORTE          ' => $item['DETALLE'],
            ]),
            'Forms Ecommerce' => $ecommerceData,
            'Forms Compras Fisicas' => $physicalData,
            'Matriz de Stock' => $matrixData,
            'Entregas por Sucursal' => $deliveryMatrix,
            'TOTAL ENTREGADOS' => $deliveredData,
        ]);
    }

    public function exportToPath(string $path, ?string $dateStart = null, ?string $dateEnd = null): string
    {
        $sheets = $this->generateExcelSheets($dateStart, $dateEnd);
        $headerStyle = (new Style)
            ->setFontBold()
            ->setBackgroundColor('E2E8F0');

        (new FastExcel($sheets))
            ->headerStyle($headerStyle)
            ->export($path);

        return $path;
    }

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
            'Código Cupón                ' => $form->code,
            'Cupón Asignado              ' => optional($form->assignedCode)->code ?? '',
            'Premio                      ' => optional($form->prize)->name ?? 'S/P',
            'Unidad de Negocio           ' => $form->business_unit,
            'Estado                      ' => match ($form->status) {
                'delivered' => 'Entregado',
                'invalidated' => 'Invalidado',
                'registered' => 'Pendiente',
                'pending' => 'Pendiente',
                'used' => 'Utilizado / Girado',
                default => $form->status ?? 'Pendiente',
            },
            'Sucursal Destino            ' => optional($form->branch)->name ?? 'No asignada',
            'Código Control Venta        ' => optional($form->sale)->control_code ?? $form->control_code ?? 'N/A',
            'Usuario Entrega             ' => optional($form->user)->name ?? 'S/U',
            'Observaciones               ' => $form->observations ?? '',
        ];
    }

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
