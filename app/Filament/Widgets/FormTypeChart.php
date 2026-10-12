<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FormTypeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Formularios por Canal / Tipo';

    protected static ?int $sort = 3;

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

        $data = FormModel::select('type', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('type')
            ->groupBy('type')
            ->pluck('total', 'type');

        $typeLabels = [
            'dismac' => 'Dismac',
            'informal-market' => 'Mercado Informal',
        ];

        $colors = [
            'dismac' => '#E22319',
            'informal-market' => '#64748b',
        ];

        $labels = [];
        $values = [];
        $bgColors = [];

        foreach ($typeLabels as $key => $label) {
            $count = $data->get($key, 0);
            $labels[] = $label;
            $values[] = $count;
            $bgColors[] = $colors[$key];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $values,
                    'backgroundColor' => $bgColors,
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
