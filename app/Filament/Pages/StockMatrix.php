<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PrizeStockHistoryResource;
use App\Models\Branch;
use App\Models\Form as FormModel;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class StockMatrix extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Whirlpool';

    protected static ?string $navigationLabel = 'Matriz de Stock';

    protected static ?string $title = 'Matriz de Inventario Real-Time';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.stock-matrix';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isSubadmin();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Recargar')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    // Acción manual a petición que refresca la vista
                    Notification::make()
                        ->title('Matriz Actualizada')
                        ->body('Datos de stock sincronizados correctamente.')
                        ->success()
                        ->send();
                }),
            Action::make('adjust_stock')
                ->label('Adicionar / Ajustar Stock')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->slideOver()
                ->form([
                    Select::make('prize_id')
                        ->label('Premio')
                        ->options(Prize::orderBy('amount', 'asc')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('branch_id')
                        ->label('Sucursal')
                        ->options(Branch::all()->mapWithKeys(fn ($b) => [$b->id => "{$b->name} ({$b->channel}) - {$b->city}"]))
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('type')
                        ->label('Tipo de Movimiento')
                        ->options([
                            'addition' => 'Adición (Aumentar Stock)',
                            'subtraction' => 'Sustracción (Disminuir Stock)',
                        ])
                        ->default('addition')
                        ->required(),
                    TextInput::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->required(),
                    Textarea::make('description')
                        ->label('Motivo / Observación')
                        ->placeholder('Ej: Ingreso de nuevo lote promocional')
                        ->required(),
                ])
                ->action(function (array $data) {
                    PrizeStockHistory::create([
                        'prize_id' => $data['prize_id'],
                        'branch_id' => $data['branch_id'],
                        'quantity' => (int) $data['quantity'],
                        'type' => $data['type'],
                        'user_id' => auth()->id(),
                        'description' => $data['description'],
                    ]);

                    Notification::make()
                        ->title('Movimiento de Stock Registrado')
                        ->body('El inventario ha sido actualizado correctamente.')
                        ->success()
                        ->send();
                }),
            Action::make('view_history')
                ->label('Ver Historial de Movimientos')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->url(fn () => PrizeStockHistoryResource::getUrl()),
            Action::make('export_matrix')
                ->label('Exportar Matriz Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $prizes = Prize::orderBy('amount', 'asc')->get();
                    $branches = Branch::orderBy('city')->orderBy('name')->get();
                    $stockRecords = PrizeBranch::all();
                    $stockMap = $stockRecords
                        ->mapWithKeys(fn ($item) => ["{$item->prize_id}.{$item->branch_id}" => $item->stock])
                        ->toArray();

                    $initialStockRecords = PrizeStockHistory::where('type', 'addition')
                        ->whereNull('form_id')
                        ->select('prize_id', 'branch_id', DB::raw('SUM(quantity) as total_initial'))
                        ->groupBy('prize_id', 'branch_id')
                        ->get();

                    $initialStockMap = [];
                    foreach ($branches as $branch) {
                        foreach ($prizes as $prize) {
                            $key = "{$prize->id}.{$branch->id}";
                            $histInitial = (int) ($initialStockRecords->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->branch_id == $branch->id)?->total_initial ?? 0);
                            $currStock = (int) ($stockMap[$key] ?? 0);
                            $initialStockMap[$key] = max($histInitial, $currStock);
                        }
                    }

                    $validDeliveredRecords = FormModel::whereIn('status', ['used', 'delivered'])
                        ->whereNotNull('prize_id')
                        ->whereNotNull('branch_id')
                        ->select('prize_id', 'branch_id', DB::raw('COUNT(*) as total'))
                        ->groupBy('prize_id', 'branch_id')
                        ->get();

                    $invalidatedRecords = FormModel::where('status', 'invalidated')
                        ->whereNotNull('prize_id')
                        ->whereNotNull('branch_id')
                        ->select('prize_id', 'branch_id', DB::raw('COUNT(*) as total'))
                        ->groupBy('prize_id', 'branch_id')
                        ->get();

                    $data = $branches->map(function ($branch) use ($prizes, $stockMap, $initialStockMap, $validDeliveredRecords, $invalidatedRecords) {
                        $row = [
                            'Ciudad' => $branch->city,
                            'Sucursal' => $branch->name,
                            'Canal' => $branch->channel,
                        ];

                        $totalActual = 0;
                        $totalInicial = 0;
                        $totalValidDelivered = 0;
                        $totalInvalidated = 0;
                        foreach ($prizes as $prize) {
                            $stock = $stockMap["{$prize->id}.{$branch->id}"] ?? 0;
                            $initial = $initialStockMap["{$prize->id}.{$branch->id}"] ?? 0;
                            $valid = (int) ($validDeliveredRecords->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->branch_id == $branch->id)?->total ?? 0);
                            $invalid = (int) ($invalidatedRecords->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->branch_id == $branch->id)?->total ?? 0);

                            $row[$prize->name.' (Actual)'] = $stock;
                            $row[$prize->name.' (Inicial)'] = $initial;
                            $row[$prize->name.' (Válidos Entregados)'] = $valid;
                            $row[$prize->name.' (Invalidados)'] = $invalid;

                            $totalActual += $stock;
                            $totalInicial += $initial;
                            $totalValidDelivered += $valid;
                            $totalInvalidated += $invalid;
                        }

                        $row['TOTAL ACTUAL'] = $totalActual;
                        $row['TOTAL INICIAL'] = $totalInicial;
                        $row['TOTAL VÁLIDOS ENTREGADOS'] = $totalValidDelivered;
                        $row['TOTAL INVALIDADOS'] = $totalInvalidated;

                        return $row;
                    });

                    return (new FastExcel($data))->download('matriz_stock_'.now()->format('Ymd_His').'.xlsx');
                }),
        ];
    }

    public function getViewData(): array
    {
        $prizes = Prize::orderBy('amount', 'asc')->get();
        // Group branches by city
        $allBranches = Branch::orderBy('city')
            ->orderBy('name')
            ->get();
        $cities = $allBranches->groupBy('city');

        // Fetch all stock into a flat map for quick access: prize_id.branch_id => stock
        $stockRecords = PrizeBranch::all();

        $stockMap = $stockRecords
            ->mapWithKeys(fn ($item) => ["{$item->prize_id}.{$item->branch_id}" => (int) $item->stock])
            ->toArray();

        // Calculate initial stock from additions in history (excluding form invalidation restorations)
        $initialStockRecords = PrizeStockHistory::where('type', 'addition')
            ->whereNull('form_id')
            ->select('prize_id', 'branch_id', DB::raw('SUM(quantity) as total_initial'))
            ->groupBy('prize_id', 'branch_id')
            ->get();

        $initialStockMap = [];
        foreach ($allBranches as $branch) {
            foreach ($prizes as $prize) {
                $key = "{$prize->id}.{$branch->id}";
                $histInitial = (int) ($initialStockRecords->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->branch_id == $branch->id)?->total_initial ?? 0);
                $currStock = (int) ($stockMap[$key] ?? 0);
                $initialStockMap[$key] = max($histInitial, $currStock);
            }
        }

        // Conteo de premios entregados válidos (formularios en used o delivered)
        $validDeliveredRecords = FormModel::whereIn('status', ['used', 'delivered'])
            ->whereNotNull('prize_id')
            ->whereNotNull('branch_id')
            ->select('prize_id', 'branch_id', DB::raw('COUNT(*) as total'))
            ->groupBy('prize_id', 'branch_id')
            ->get();

        $validDeliveredMap = [];
        foreach ($allBranches as $branch) {
            foreach ($prizes as $prize) {
                $key = "{$prize->id}.{$branch->id}";
                $validDeliveredMap[$key] = (int) ($validDeliveredRecords->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->branch_id == $branch->id)?->total ?? 0);
            }
        }

        // Conteo de premios anulados / invalidados
        $invalidatedRecords = FormModel::where('status', 'invalidated')
            ->whereNotNull('prize_id')
            ->whereNotNull('branch_id')
            ->select('prize_id', 'branch_id', DB::raw('COUNT(*) as total'))
            ->groupBy('prize_id', 'branch_id')
            ->get();

        $invalidatedMap = [];
        foreach ($allBranches as $branch) {
            foreach ($prizes as $prize) {
                $key = "{$prize->id}.{$branch->id}";
                $invalidatedMap[$key] = (int) ($invalidatedRecords->firstWhere(fn ($r) => $r->prize_id == $prize->id && $r->branch_id == $branch->id)?->total ?? 0);
            }
        }

        // Calculate totals per prize
        $prizeTotals = [];
        $prizeInitialTotals = [];
        $prizeValidDeliveredTotals = [];
        $prizeInvalidatedTotals = [];
        foreach ($prizes as $prize) {
            $prizeTotals[$prize->id] = (int) $stockRecords->where('prize_id', $prize->id)->sum('stock');
            $sumInitial = 0;
            $sumValid = 0;
            $sumInv = 0;
            foreach ($allBranches as $branch) {
                $sumInitial += $initialStockMap["{$prize->id}.{$branch->id}"] ?? 0;
                $sumValid += $validDeliveredMap["{$prize->id}.{$branch->id}"] ?? 0;
                $sumInv += $invalidatedMap["{$prize->id}.{$branch->id}"] ?? 0;
            }
            $prizeInitialTotals[$prize->id] = $sumInitial;
            $prizeValidDeliveredTotals[$prize->id] = $sumValid;
            $prizeInvalidatedTotals[$prize->id] = $sumInv;
        }

        $globalActualTotal = collect($prizeTotals)->sum();
        $globalInitialTotal = collect($prizeInitialTotals)->sum();
        $globalConsumedTotal = collect($prizeValidDeliveredTotals)->sum();
        $globalInvalidatedTotal = collect($prizeInvalidatedTotals)->sum();

        $totalAllInvalidatedForms = FormModel::where('status', 'invalidated')->count();
        $invalidatedWithoutPrize = FormModel::where('status', 'invalidated')->whereNull('prize_id')->count();

        return [
            'prizes' => $prizes,
            'cities' => $cities,
            'stockMap' => $stockMap,
            'initialStockMap' => $initialStockMap,
            'validDeliveredMap' => $validDeliveredMap,
            'invalidatedMap' => $invalidatedMap,
            'prizeTotals' => $prizeTotals,
            'prizeInitialTotals' => $prizeInitialTotals,
            'prizeValidDeliveredTotals' => $prizeValidDeliveredTotals,
            'prizeInvalidatedTotals' => $prizeInvalidatedTotals,
            'globalActualTotal' => $globalActualTotal,
            'globalInitialTotal' => $globalInitialTotal,
            'globalConsumedTotal' => $globalConsumedTotal,
            'globalInvalidatedTotal' => $globalInvalidatedTotal,
            'totalAllInvalidatedForms' => $totalAllInvalidatedForms,
            'invalidatedWithoutPrize' => $invalidatedWithoutPrize,
            'totalBranches' => $allBranches->count(),
        ];
    }
}
