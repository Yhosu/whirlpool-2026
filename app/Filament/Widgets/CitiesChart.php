<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CitiesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Formularios por Ciudad';

    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 1;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfDay();
        $endDate = $this->filters['endDate'] ?? now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $data = FormModel::select('city', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();

        $labels = $data->pluck('city')->toArray();
        $totals = $data->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Formularios por Ciudad',
                    'data' => $totals,
                    'backgroundColor' => [
                        '#E22319', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
