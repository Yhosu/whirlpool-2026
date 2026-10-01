<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrizeStockHistoryResource\Pages;
use App\Models\PrizeStockHistory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class PrizeStockHistoryResource extends Resource
{
    protected static ?string $model = PrizeStockHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'CONSUL';

    protected static ?string $navigationLabel = 'Historial de Stock';

    protected static ?string $modelLabel = 'Historial de Stock';

    protected static ?string $pluralModelLabel = 'Historiales de Stock';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->email === 'coranivico@gmail.com';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Fecha')
                    ->content(fn (Model $record) => $record->created_at->format('d/m/Y H:i:s')),
                Placeholder::make('prize')
                    ->label('Premio')
                    ->content(fn (Model $record) => $record->prize->name),
                Placeholder::make('branch')
                    ->label('Sucursal')
                    ->content(fn (Model $record) => "{$record->branch->name} - {$record->branch->city}"),
                Placeholder::make('type')
                    ->label('Tipo')
                    ->content(fn (Model $record) => match ($record->type) {
                        'addition' => 'Adición',
                        'subtraction' => 'Sustracción',
                        default => $record->type,
                    }),
                Placeholder::make('quantity')
                    ->label('Cantidad')
                    ->content(fn (Model $record) => $record->quantity),
                Placeholder::make('user')
                    ->label('Usuario')
                    ->content(fn (Model $record) => $record->user?->name ?? 'Sistema'),
                Placeholder::make('description')
                    ->label('Descripción')
                    ->content(fn (Model $record) => $record->description),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    TextColumn::make('created_at')
                        ->label('Fecha')
                        ->dateTime('d/m/Y H:i')
                        ->sortable(),
                    TextColumn::make('prize.name')
                        ->label('Premio')
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('branch.name')
                        ->label('Sucursal')
                        ->description(fn (Model $record) => $record->branch->city)
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('type')
                        ->label('Tipo')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'addition' => 'success',
                            'subtraction' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'addition' => 'Adición',
                            'subtraction' => 'Sustracción',
                            default => $state,
                        }),
                    TextColumn::make('quantity')
                        ->label('Cantidad')
                        ->numeric()
                        ->sortable()
                        ->color(fn (Model $record) => match ($record->type) {
                            'addition' => 'success',
                            'subtraction' => 'danger',
                            default => 'gray',
                        })
                        ->formatStateUsing(fn (int $state, Model $record) => match ($record->type) {
                            'addition' => '+'.$state,
                            'subtraction' => '-'.abs($state),
                            default => $state,
                        }),
                ]),
                Tables\Columns\Layout\Stack::make([
                    TextColumn::make('user.name')
                        ->label('Usuario')
                        ->icon('heroicon-m-user')
                        ->color('gray')
                        ->placeholder('Realizado por: Sistema')
                        ->formatStateUsing(fn ($state) => "Realizado por: {$state}"),
                    TextColumn::make('description')
                        ->label('Descripción')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->color('gray')
                        ->wrap(),
                ])->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('prize_id')
                    ->label('Premio')
                    ->relationship('prize', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'addition' => 'Adición',
                        'subtraction' => 'Sustracción',
                    ]),
                Filter::make('created_at_range')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Desde'),
                        DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->where('created_at', '>=', Carbon::parse($date)->startOfDay()),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->where('created_at', '<=', Carbon::parse($date)->endOfDay()),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Desde '.Carbon::parse($data['created_from'])->format('d/m/Y'))
                                ->removeField('created_from');
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Hasta '.Carbon::parse($data['created_until'])->format('d/m/Y'))
                                ->removeField('created_until');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Pages\ListPrizeStockHistories $livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $data = $query->get()->map(fn ($item) => [
                            'Fecha' => $item->created_at->format('d/m/Y H:i'),
                            'Premio' => $item->prize?->name ?? 'N/A',
                            'Sucursal' => $item->branch?->name ?? 'N/A',
                            'Ciudad' => $item->branch?->city ?? 'N/A',
                            'Tipo' => $item->type === 'addition' ? 'Adición' : 'Sustracción',
                            'Cantidad' => $item->quantity,
                            'Usuario' => $item->user?->name ?? 'Sistema',
                            'Descripción' => $item->description,
                        ]);

                        return (new FastExcel($data))->download('historial_stock_'.now()->format('Ymd_His').'.xlsx');
                    }),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrizeStockHistories::route('/'),
        ];
    }
}
