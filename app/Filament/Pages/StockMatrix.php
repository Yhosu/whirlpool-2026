<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PrizeStockHistoryResource;
use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Rap2hpoutre\FastExcel\FastExcel;

class StockMatrix extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Whirlpool';

    protected static ?string $navigationLabel = 'Matriz de Stock';

    protected static ?string $title = 'Matriz de Inventario Real-Time';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.stock-matrix';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('adjust_stock')
                ->label('Ajustar / Agregar Stock')
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

                    $data = $branches->map(function ($branch) use ($prizes, $stockMap) {
                        $row = [
                            'Ciudad' => $branch->city,
                            'Sucursal' => $branch->name,
                            'Canal' => $branch->channel,
                        ];

                        $total = 0;
                        foreach ($prizes as $prize) {
                            $stock = $stockMap["{$prize->id}.{$branch->id}"] ?? 0;
                            $row[$prize->name] = $stock;
                            $total += $stock;
                        }

                        $row['TOTAL'] = $total;

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
