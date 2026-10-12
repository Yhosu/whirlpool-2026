<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FormStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Formularios por Estado';

    protected static ?int $sort = 2;

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

        $data = FormModel::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('status')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = [
            'registered' => 'Registrado',
            'pending' => 'Pendiente',
            'used' => 'Utilizado / Girado',
            'delivered' => 'Entregado',
            'invalidated' => 'Invalidado',
        ];

        $colors = [
            'registered' => '#f59e0b', // warning
            'pending' => '#3b82f6',    // info
            'used' => '#a855f7',       // purple
            'delivered' => '#10b981',  // success
            'invalidated' => '#ef4444',// danger
        ];

        $labels = [];
        $values = [];
        $bgColors = [];

        foreach ($statusLabels as $key => $label) {
            $count = $data->get($key, 0);
            if ($count > 0 || $data->isEmpty()) {
                $labels[] = $label;
                $values[] = $count;
                $bgColors[] = $colors[$key];
            }
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
