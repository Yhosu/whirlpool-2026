<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CitiesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Ciudades de Participantes';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected static ?string $pollingInterval = null; // para que no se actualice automaticamente

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfDay();
        $endDate = $this->filters['endDate'] ?? now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $data = Participant::select('city', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('city')
            ->groupBy('city')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Participantes por Ciudad',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#E22319', '#34d399', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#6366f1',
                    ],
                ],
            ],
            'labels' => $data->pluck('city')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
