<?php

namespace App\Livewire\Consul;

use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class StockTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(PrizeBranch::query())
            ->columns([
                TextColumn::make('prize.name')
                    ->label('Premio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branch.name')
                    ->label('Sucursal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('prize_id')
                    ->label('Premio')
                    ->relationship('prize', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('city')
                    ->label('Ciudad')
                    ->options([
                        'Chuquisaca' => 'Chuquisaca',
                        'Cochabamba' => 'Cochabamba',
                        'El Alto' => 'El Alto',
                        'La Paz' => 'La Paz',
                        'Montero' => 'Montero',
                        'Santa Cruz' => 'Santa Cruz',
                        'Tarija' => 'Tarija',
                        'Nacional' => 'Nacional',
                    ])
                    ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn ($query, $value) => $query->whereHas('branch', fn ($query) => $query->where('city', $value))
                    )
                    ),
                TernaryFilter::make('has_stock')
                    ->label('Disponibilidad')
                    ->placeholder('Todos')
                    ->trueLabel('Con Stock (> 0)')
                    ->falseLabel('Sin Stock (0)')
                    ->queries(
                        true: fn (Builder $query) => $query->where('stock', '>', 0),
                        false: fn (Builder $query) => $query->where('stock', '<=', 0),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->filtersLayout(FiltersLayout::Modal)
            ->headerActions([
                Action::make('init_stock')
                    ->label('Distribuir / Iniciar Stock')
                    ->icon('heroicon-o-truck')
                    ->form([
                        Select::make('prize_id')
                            ->label('Premio')
                            ->options(Prize::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Select::make('branch_id')
                            ->label('Sucursal')
                            ->options(Branch::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        TextInput::make('quantity')
                            ->label('Cantidad a asignar')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('description')
                            ->label('Nota/Descripción')
                            ->placeholder('Ej: Asignación inicial'),
                    ])
                    ->action(function (array $data): void {
                        PrizeStockHistory::create([
                            'prize_id' => $data['prize_id'],
                            'branch_id' => $data['branch_id'],
                            'quantity' => $data['quantity'],
                            'type' => 'addition',
                            'user_id' => auth()->id(),
                            'description' => $data['description'] ?? 'Asignación de stock manual',
                        ]);
                    })
                    ->slideOver(),
            ])
            ->actions([
                Action::make('add_stock')
                    ->label('Agregar Stock')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Cantidad a agregar')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('description')
                            ->label('Nota/Descripción')
                            ->placeholder('Ej: Reabastecimiento semanal'),
                    ])
                    ->action(function (PrizeBranch $record, array $data): void {
                        PrizeStockHistory::create([
                            'prize_id' => $record->prize_id,
                            'branch_id' => $record->branch_id,
                            'quantity' => $data['quantity'],
                            'type' => 'addition',
                            'user_id' => auth()->id(),
                            'description' => $data['description'] ?? 'Carga de stock manual',
                        ]);
                    }),
                Action::make('decrease_stock')
                    ->label('Disminuir Stock')
                    ->icon('heroicon-o-minus')
                    ->color('danger')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Cantidad a disminuir')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->rules([
                                fn (Get $get, PrizeBranch $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                                    if ($value > $record->stock) {
                                        $fail("La cantidad no puede ser mayor al stock actual ({$record->stock}).");
                                    }
                                },
                            ]),
                        TextInput::make('description')
                            ->label('Nota/Descripción')
                            ->required()
                            ->placeholder('Ej: Retiro por daño, error de carga, etc.'),
                    ])
                    ->action(function (PrizeBranch $record, array $data): void {
                        PrizeStockHistory::create([
                            'prize_id' => $record->prize_id,
                            'branch_id' => $record->branch_id,
                            'quantity' => $data['quantity'],
                            'type' => 'subtraction',
                            'user_id' => auth()->id(),
                            'description' => $data['description'],
                        ]);
                    }),
            ]);
    }

    public function render()
    {
        return view('livewire.consul.stock-table');
    }
}
