<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Whirlpool';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $modelLabel = 'Venta';

    protected static ?string $pluralModelLabel = 'Ventas';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('participant_id')
                    ->label('ID Participante')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('city_agency')
                    ->label('Agencia / Ciudad')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('sale_date')
                    ->label('Fecha de Venta')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('control_code')
                    ->label('Código de Control')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('business_unit')
                    ->label('Unidad de Negocio'),
                Forms\Components\TextInput::make('quantity_items')
                    ->label('Cantidad de Items')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Monto Total')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('invoice_url')
                    ->label('URL de Factura')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('invoice_number')
                    ->label('Número de Factura')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('sale_type')
                    ->label('Tipo de Venta'),
                Forms\Components\Toggle::make('active')
                    ->label('Válido'),
                Forms\Components\Repeater::make('sale_items')
                    ->relationship('sale_items')
                    ->label('Items de Venta')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Item')
                            ->columnSpan(2)
                            ->disabled(),
                        Forms\Components\TextInput::make('name_brand')
                            ->label('Marca')
                            ->disabled()
                            ->formatStateUsing(function ($state, Get $get) {
                                $code = (string) $get('code_brand');
                                if ($code === '2' || $code === '15') {
                                    return 'WHIRLPOOL';
                                }

                                return $state ?: 'WHIRLPOOL';
                            }),
                        Forms\Components\TextInput::make('code_brand')
                            ->label('Cod. Marca')
                            ->disabled(),
                        Forms\Components\TextInput::make('category')
                            ->label('Categoría')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->disabled(),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->numeric()
                            ->default(1)
                            ->disabled(),
                    ])
                    ->columns(6)
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente / Participante')
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('control_code')
                    ->label('Código Control')
                    ->icon('heroicon-m-qr-code')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('quantity_items')
                    ->label('Items')
                    ->icon('heroicon-m-shopping-cart')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total (Bs.)')
                    ->icon('heroicon-m-banknotes')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('forms_count')
                    ->label('Formularios')
                    ->icon('heroicon-m-document-duplicate')
                    ->counts('forms')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_type')
                    ->label('Tipo Venta')
                    ->icon('heroicon-m-credit-card')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_unit')
                    ->label('Canal')
                    ->icon('heroicon-m-building-storefront')
                    ->badge(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Válido')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Registro')
                    ->icon('heroicon-m-calendar')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('invoice_number')
                            ->label('Factura N°')
                            ->icon('heroicon-m-receipt-percent')
                            ->formatStateUsing(fn ($state) => "Factura N°: {$state}")
                            ->sortable(),
                        Tables\Columns\TextColumn::make('city_agency')
                            ->label('Agencia / Ciudad')
                            ->icon('heroicon-m-map-pin')
                            ->formatStateUsing(fn ($state) => "Agencia / Ciudad: {$state}")
                            ->searchable(),
                        Tables\Columns\TextColumn::make('sale_date')
                            ->label('Fecha Venta')
                            ->icon('heroicon-m-clock')
                            ->formatStateUsing(fn ($state) => "Fecha Venta: {$state}")
                            ->searchable(),
                        Tables\Columns\TextColumn::make('invoice_url')
                            ->label('Factura SIAT')
                            ->icon('heroicon-m-document-text')
                            ->formatStateUsing(fn ($state) => $state ? '📄 Ver Factura SIAT (URL)' : 'Sin Factura URL')
                            ->color(fn ($state) => $state ? 'info' : 'gray')
                            ->url(fn (Model $record) => $record->invoice_url)
                            ->openUrlInNewTab(),
                    ])->space(2),
                ])->collapsible(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('participant')
                    ->relationship('participant', 'name')
                    ->searchable()
                    ->multiple()
                    ->label('Participante'),
                Tables\Filters\SelectFilter::make('business_unit')
                    ->label('Unidad de Negocio')
                    ->options([
                        'ECO' => 'ECO',
                        'RET' => 'RET',
                        'ACE' => 'ACE',
                        'WEB' => 'WEB',
                    ]),
                Tables\Filters\SelectFilter::make('sale_type')
                    ->label('Tipo de Venta')
                    ->options([
                        'CONTADO' => 'CONTADO',
                        'MINICUOTAS' => 'MINICUOTAS',
                    ]),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Válido')
                    ->boolean(),
                Tables\Filters\TernaryFilter::make('valid_promotion')
                    ->label('Válido para Promoción')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('valid_sale_items'),
                        false: fn (Builder $query) => $query->whereDoesntHave('valid_sale_items'),
                        blank: fn (Builder $query) => $query,
                    ),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
            ->filtersLayout(Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->slideOver(),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->slideOver()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $data = $query->with(['participant', 'forms', 'sale_items'])->get()->map(fn ($item) => [
                            'Fecha Registro' => $item->created_at?->format('d/m/Y H:i') ?? '',
                            'Fecha Venta' => $item->sale_date ?? '',
                            'Cliente' => $item->name,
                            'Código Control' => $item->control_code,
                            'Factura N°' => $item->invoice_number,
                            'Agencia / Ciudad' => $item->city_agency,
                            'Canal UDN' => $item->business_unit,
                            'Tipo Venta' => $item->sale_type,
                            'Cantidad Items' => $item->quantity_items,
                            'Total Monto (Bs)' => $item->total_amount,
                            'Intentos Generados' => $item->forms->count(),
                            'Válido' => $item->active ? 'Sí' : 'No',
                            'Factura URL SIAT' => $item->invoice_url,
                        ]);

                        return (new FastExcel($data))->download('ventas_'.now()->format('Ymd_His').'.xlsx');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SaleItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
