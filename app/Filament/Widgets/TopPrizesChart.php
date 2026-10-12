<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TopPrizesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Premios Asignados / Entregados';

    protected static ?int $sort = 5;

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

        $data = FormModel::join('prizes', 'forms.prize_id', '=', 'prizes.id')
            ->whereBetween('forms.created_at', [$start, $end])
            ->select('prizes.name', DB::raw('count(*) as total'))
            ->groupBy('prizes.name')
            ->orderByDesc('total')
            ->get();

        $labels = $data->pluck('name')->toArray();
        $totals = $data->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Premios Asignados',
                    'data' => $totals,
                    'backgroundColor' => [
                        '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
