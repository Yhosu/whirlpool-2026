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

    protected static ?string $heading = 'Formularios por Canal y Validez';

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

        $base = FormModel::whereBetween('created_at', [$start, $end]);

        $dismacValid = (clone $base)->where('type', 'dismac')->whereIn('status', ['used', 'delivered'])->count();
        $dismacInvalid = (clone $base)->where('type', 'dismac')->where('status', 'invalidated')->count();

        $informalValid = (clone $base)->where('type', 'informal-market')->whereIn('status', ['used', 'delivered'])->count();
        $informalPending = (clone $base)->where('type', 'informal-market')->whereIn('status', ['registered', 'pending'])->count();
        $informalInvalid = (clone $base)->where('type', 'informal-market')->where('status', 'invalidated')->count();

        $items = [
            'Dismac (Válidos)' => [$dismacValid, '#E22319'],
            'Dismac (Invalidados)' => [$dismacInvalid, '#f87171'],
            'Informal (Válidos)' => [$informalValid, '#2563eb'],
            'Informal (Pendientes)' => [$informalPending, '#f59e0b'],
            'Informal (Invalidados)' => [$informalInvalid, '#64748b'],
        ];

        $labels = [];
        $values = [];
        $bgColors = [];

        foreach ($items as $label => [$val, $color]) {
            if ($val > 0 || ($dismacValid + $dismacInvalid + $informalValid + $informalPending + $informalInvalid) === 0) {
                $labels[] = $label;
                $values[] = $val;
                $bgColors[] = $color;
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
