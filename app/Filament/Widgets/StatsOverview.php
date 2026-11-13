<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    protected static ?string $pollingInterval = null;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfDay();
        $endDate = $this->filters['endDate'] ?? now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $baseQuery = FormModel::whereBetween('created_at', [$start, $end]);

        $totalForms = (clone $baseQuery)->count();
        $withPrize = (clone $baseQuery)->whereNotNull('prize_id')->count();
        $scratchedCount = (clone $baseQuery)->where('is_scratched', true)->count();

        $deliveredCount = (clone $baseQuery)->where('status', 'delivered')->count();
        $usedCount = (clone $baseQuery)->where('status', 'used')->count();
        $deliveredOrUsed = $deliveredCount + $usedCount;

        $registeredCount = (clone $baseQuery)->where('status', 'registered')->count();
        $onlyPendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $pendingTotal = $registeredCount + $onlyPendingCount;

        $invalidatedCount = (clone $baseQuery)->where('status', 'invalidated')->count();
        $invalidatedWithPrize = (clone $baseQuery)->where('status', 'invalidated')->whereNotNull('prize_id')->count();
        $invalidatedWithoutPrize = (clone $baseQuery)->where('status', 'invalidated')->whereNull('prize_id')->count();

        $uniqueClients = (clone $baseQuery)->whereNotNull('ci_number')->distinct('ci_number')->count('ci_number');

        $dismacQuery = (clone $baseQuery)->where('type', 'dismac');
        $dismacTotal = (clone $dismacQuery)->count();
        $dismacValid = (clone $dismacQuery)->whereIn('status', ['used', 'delivered'])->count();
        $dismacInvalid = (clone $dismacQuery)->where('status', 'invalidated')->count();

        $informalQuery = (clone $baseQuery)->where('type', 'informal-market');
        $informalTotal = (clone $informalQuery)->count();
        $informalValid = (clone $informalQuery)->whereIn('status', ['used', 'delivered'])->count();
        $informalPending = (clone $informalQuery)->whereIn('status', ['registered', 'pending'])->count();
        $informalInvalid = (clone $informalQuery)->where('status', 'invalidated')->count();
        $informalInvalidNoPrize = (clone $informalQuery)->where('status', 'invalidated')->whereNull('prize_id')->count();

        $withVoucher = (clone $baseQuery)->whereNotNull('voucher')->count();

        return [
            Stat::make('Total Formularios', $totalForms)
                ->description('Registros en el periodo')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Premios Asignados', $withPrize)
                ->description("{$scratchedCount} ruletas giradas")
                ->descriptionIcon('heroicon-m-gift')
                ->color('success')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Canjeados / Entregados', $deliveredOrUsed)
                ->description("{$deliveredCount} entregados | {$usedCount} utilizados/girados")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Pendientes de Validación', $pendingTotal)
                ->description("{$registeredCount} reg. | {$onlyPendingCount} pend.")
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Invalidados / Rechazados', $invalidatedCount)
                ->description("{$invalidatedWithPrize} con premio | {$invalidatedWithoutPrize} sin premio")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Clientes Únicos (CI)', $uniqueClients)
                ->description('Participantes distintos')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Canal Dismac (Ecommerce)', "{$dismacValid} Válidos")
                ->description("{$dismacInvalid} inv. | Total: {$dismacTotal}")
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),

            Stat::make('Mercado Informal', "{$informalValid} Válidos")
                ->description("{$informalInvalid} inv. ({$informalInvalidNoPrize} sin premio) | {$informalPending} pend.")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('gray')
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),
        ];
    }
}
