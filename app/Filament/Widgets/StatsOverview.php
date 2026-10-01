<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use App\Models\ParticipantCoupon;
use App\Models\Sale;
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
        return auth()->user()->isAdmin();
    }

    protected static ?string $pollingInterval = null; // para que no se actualice automaticamente

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfDay();
        $endDate = $this->filters['endDate'] ?? now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $totalSales = Sale::whereBetween('created_at', [$start, $end])->count();
        $validSales = Sale::whereHas('valid_sale_items')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $totalParticipants = Participant::whereBetween('created_at', [$start, $end])->count();
        $validParticipants = Participant::whereBetween('created_at', [$start, $end])
            ->whereIn('id', Sale::whereHas('valid_sale_items')->select('participant_id'))
            ->count();
        $totalCoupons = ParticipantCoupon::whereBetween('created_at', [$start, $end])->count();

        return [
            Stat::make('Ventas Registradas', $totalSales)
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),
            Stat::make('Ventas Hisense/Toshiba', $validSales)
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),
            Stat::make('Participantes Registrados', $totalParticipants)
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),
            Stat::make('Participantes Válidos', $validParticipants)
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),
            Stat::make('Cupones Registrados', $totalCoupons)
                ->extraAttributes(['class' => 'flex flex-col items-center justify-center text-center']),
        ];
    }
}
