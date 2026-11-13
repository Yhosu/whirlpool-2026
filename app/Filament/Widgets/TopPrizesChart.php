<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use App\Models\Prize;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TopPrizesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Premios Válidos Entregados por Canal';

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
        $startDate = ! empty($this->filters['startDate']) ? $this->filters['startDate'] : now()->startOfDay();
        $endDate = ! empty($this->filters['endDate']) ? $this->filters['endDate'] : now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $prizes = Prize::orderBy('amount', 'asc')->get();

        $deliveredCounts = FormModel::whereBetween('created_at', [$start, $end])
            ->whereIn('status', ['used', 'delivered'])
            ->whereNotNull('prize_id')
            ->select('prize_id', 'type', DB::raw('count(*) as total'))
            ->groupBy('prize_id', 'type')
            ->get();

        $labels = [];
        $dismacData = [];
        $informalData = [];

        foreach ($prizes as $prize) {
            $labels[] = $prize->name;
            $dismacData[] = (int) ($deliveredCounts->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->type === 'dismac')?->total ?? 0);
            $informalData[] = (int) ($deliveredCounts->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->type === 'informal-market')?->total ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Dismac (Ecommerce)',
                    'data' => $dismacData,
                    'backgroundColor' => '#E22319',
                ],
                [
                    'label' => 'Mercado Informal',
                    'data' => $informalData,
                    'backgroundColor' => '#2563eb',
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

