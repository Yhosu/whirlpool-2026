<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use Filament\Pages\Page;

class StockMatrix extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'CONSUL';

    protected static ?string $navigationLabel = 'Matriz de Stock';

    protected static ?string $title = 'Matriz de Inventario Real-Time';

    protected static string $view = 'filament.pages.stock-matrix';

    public function getViewData(): array
    {
        $prizes = Prize::orderBy('name')->get();
        // Group branches by city
        $cities = Branch::orderBy('city')
            ->orderBy('name')
            ->get()
            ->groupBy('city');

        // Fetch all stock into a flat map for quick access: prize_id.branch_id => stock
        $stockRecords = PrizeBranch::all();

        $stockMap = $stockRecords
            ->mapWithKeys(fn ($item) => ["{$item->prize_id}.{$item->branch_id}" => $item->stock])
            ->toArray();

        // Calculate totals per prize
        $prizeTotals = [];
        foreach ($prizes as $prize) {
            $prizeTotals[$prize->id] = $stockRecords->where('prize_id', $prize->id)->sum('stock');
        }

        return [
            'prizes' => $prizes,
            'cities' => $cities,
            'stockMap' => $stockMap,
            'prizeTotals' => $prizeTotals,
        ];
    }
}
