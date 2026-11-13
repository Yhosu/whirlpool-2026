<?php

namespace App\Filament\Widgets;

use App\Models\Form as FormModel;
use App\Models\Prize;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PrizesByCategoryWidget extends Widget
{
    use InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.prizes-by-category-widget';

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    public function getViewData(): array
    {
        $startDate = ! empty($this->filters['startDate']) ? $this->filters['startDate'] : now()->startOfDay();
        $endDate = ! empty($this->filters['endDate']) ? $this->filters['endDate'] : now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $prizes = Prize::orderBy('amount', 'asc')->get();

        $records = FormModel::whereBetween('created_at', [$start, $end])
            ->whereNotNull('prize_id')
            ->select('prize_id', 'type', 'status', DB::raw('count(*) as total'))
            ->groupBy('prize_id', 'type', 'status')
            ->get();

        $matrix = [];
        $totals = [
            'dismac_valid' => 0,
            'dismac_invalid' => 0,
            'informal_valid' => 0,
            'informal_invalid' => 0,
            'total_valid' => 0,
            'total_invalid' => 0,
            'total_amount_delivered' => 0,
        ];

        foreach ($prizes as $prize) {
            $dismacValid = (int) ($records->where('prize_id', $prize->id)->where('type', 'dismac')->whereIn('status', ['used', 'delivered'])->sum('total'));
            $dismacInvalid = (int) ($records->where('prize_id', $prize->id)->where('type', 'dismac')->where('status', 'invalidated')->sum('total'));

            $informalValid = (int) ($records->where('prize_id', $prize->id)->where('type', 'informal-market')->whereIn('status', ['used', 'delivered'])->sum('total'));
            $informalInvalid = (int) ($records->where('prize_id', $prize->id)->where('type', 'informal-market')->where('status', 'invalidated')->sum('total'));

            $validSum = $dismacValid + $informalValid;
            $invalidSum = $dismacInvalid + $informalInvalid;
            $amountSum = $validSum * (float) $prize->amount;

            $matrix[] = [
                'id' => $prize->id,
                'name' => $prize->name,
                'amount' => (float) $prize->amount,
                'dismac_valid' => $dismacValid,
                'dismac_invalid' => $dismacInvalid,
                'informal_valid' => $informalValid,
                'informal_invalid' => $informalInvalid,
                'total_valid' => $validSum,
                'total_invalid' => $invalidSum,
                'total_amount' => $amountSum,
            ];

            $totals['dismac_valid'] += $dismacValid;
            $totals['dismac_invalid'] += $dismacInvalid;
            $totals['informal_valid'] += $informalValid;
            $totals['informal_invalid'] += $informalInvalid;
            $totals['total_valid'] += $validSum;
            $totals['total_invalid'] += $invalidSum;
            $totals['total_amount_delivered'] += $amountSum;
        }

        $dismacInvalidNoPrize = (int) FormModel::whereBetween('created_at', [$start, $end])
            ->where('type', 'dismac')
            ->where('status', 'invalidated')
            ->whereNull('prize_id')
            ->count();

        $informalInvalidNoPrize = (int) FormModel::whereBetween('created_at', [$start, $end])
            ->where('type', 'informal-market')
            ->where('status', 'invalidated')
            ->whereNull('prize_id')
            ->count();

        $noPrizeInvalid = [
            'dismac' => $dismacInvalidNoPrize,
            'informal' => $informalInvalidNoPrize,
            'total' => $dismacInvalidNoPrize + $informalInvalidNoPrize,
        ];

        $totals['dismac_invalid_all'] = $totals['dismac_invalid'] + $dismacInvalidNoPrize;
        $totals['informal_invalid_all'] = $totals['informal_invalid'] + $informalInvalidNoPrize;
        $totals['total_invalid_all'] = $totals['total_invalid'] + $noPrizeInvalid['total'];

        return [
            'matrix' => $matrix,
            'noPrizeInvalid' => $noPrizeInvalid,
            'totals' => $totals,
        ];
    }
}

