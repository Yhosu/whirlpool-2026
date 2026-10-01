<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MundialSaleResource\Pages;
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

class MundialSaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'EL MUNDIAL SE VIVE EN CASA';

    protected static ?string $navigationLabel = 'Ventas con cupones';

    protected static ?string $modelLabel = 'Venta con cupones';

    protected static ?string $pluralModelLabel = 'Ventas con cupones';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->has('mundial_coupons')
            ->with(['participant', 'sale_items', 'mundial_coupons.prize']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Venta')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->prefixIcon('heroicon-m-user')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('participant_name')
                            ->label('Participante')
                            ->prefixIcon('heroicon-m-user')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record?->participant?->name ?? 'N/A'),
                        Forms\Components\TextInput::make('participant_ci')
                            ->label('CI Participante')
                            ->prefixIcon('heroicon-m-identification')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record?->participant?->ci_number ?? 'N/A'),
                        Forms\Components\TextInput::make('participant_cellphone')
                            ->label('Celular Participante')
                            ->prefixIcon('heroicon-m-phone')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record?->participant?->cellphone ?? 'N/A'),
                        Forms\Components\TextInput::make('city_agency')
                            ->label('Agencia / Ciudad')
                            ->prefixIcon('heroicon-m-map-pin')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('sale_date')
                            ->label('Fecha de Venta')
                            ->prefixIcon('heroicon-m-calendar')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('control_code')
                            ->label('Código de Control')
                            ->prefixIcon('heroicon-m-qr-code')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('business_unit')
                            ->label('Unidad de Negocio')
                            ->prefixIcon('heroicon-m-building-office'),
                        Forms\Components\TextInput::make('quantity_items')
                            ->label('Cantidad de Items')
                            ->prefixIcon('heroicon-m-shopping-bag')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Monto Total')
                            ->prefixIcon('heroicon-m-banknotes')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('invoice_url')
                            ->label('URL de Factura')
                            ->prefixIcon('heroicon-m-link')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Número de Factura')
                            ->prefixIcon('heroicon-m-document-text')
                            ->numeric()
                            ->default(null),
                        Forms\Components\TextInput::make('sale_type')
                            ->label('Tipo de Venta')
                            ->prefixIcon('heroicon-m-credit-card'),
                        Forms\Components\Toggle::make('active')
                            ->label('Válido'),
                    ])->columns(2),

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
                                if ($code === '10') {
                                    return 'TOSHIBA';
                                }
                                if ($code === '404') {
                                    return 'HISENSE';
                                }

                                return $state;
                            }),
                        Forms\Components\TextInput::make('code_brand')
                            ->label('Cod. Marca')
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
                    ->columns(5)
                    ->columnSpanFull()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false),

                Forms\Components\Repeater::make('mundial_coupons')
                    ->relationship('mundial_coupons')
                    ->label('Cupones del Mundial Generados')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->prefixIcon('heroicon-m-ticket')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->label('Estado')
                            ->prefixIcon('heroicon-m-question-mark-circle')
                            ->disabled(),
                        Forms\Components\TextInput::make('prize_name')
                            ->label('Premio')
                            ->prefixIcon('heroicon-m-gift')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn ($record) => $record?->prize?->name ?? '-'),
                        Forms\Components\Toggle::make('active')
                            ->label('Activo')
                            ->disabled(),
                    ])
                    ->columns(4)
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
                    ->label('Participante')
                    ->prefix('Participante: ')
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_items')
                    ->label('Items')
                    ->prefix('Items: ')
                    ->icon('heroicon-m-shopping-bag')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_type')
                    ->label('Tipo Venta')
                    ->prefix('Tipo Venta: ')
                    ->icon('heroicon-m-credit-card')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->prefix('Total: ')
                    ->icon('heroicon-m-banknotes')
                    ->numeric()
                    ->suffix(' Bs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mundial_coupons_count')
                    ->label('Cupones')
                    ->prefix('Cupones: ')
                    ->icon('heroicon-m-ticket')
                    ->counts('mundial_coupons')
                    ->sortable(),

                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('invoice_number')
                            ->label('Factura')
                            ->prefix('Factura: ')
                            ->icon('heroicon-m-document-text')
                            ->sortable(),
                        Tables\Columns\TextColumn::make('invoice_url')
                            ->label('Factura Link')
                            ->icon('heroicon-m-link')
                            ->formatStateUsing(fn ($state) => $state ? 'Link de factura' : '-')
                            ->url(fn ($record) => $record->invoice_url)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('participant.ci_number')
                            ->label('CI')
                            ->prefix('CI: ')
                            ->icon('heroicon-m-identification')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('participant.cellphone')
                            ->label('Celular')
                            ->prefix('Celular: ')
                            ->icon('heroicon-m-phone')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('city_agency')
                            ->label('Ciudad/Agencia')
                            ->prefix('Ciudad/Agencia: ')
                            ->icon('heroicon-m-map-pin')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('control_code')
                            ->label('Cód. Control')
                            ->prefix('Cód. Control: ')
                            ->icon('heroicon-m-qr-code')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('business_unit')
                            ->label('UDN')
                            ->icon('heroicon-m-building-office'),
                        Tables\Columns\TextColumn::make('active')
                            ->label('Estado')
                            ->formatStateUsing(fn ($state) => $state ? 'Válido' : 'No Válido')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                        Tables\Columns\TextColumn::make('created_at')
                            ->label('Registrado')
                            ->prefix('Registrado: ')
                            ->icon('heroicon-m-clock')
                            ->dateTime()
                            ->sortable(),
                    ]),
                    Tables\Columns\TextColumn::make('mundial_coupons.code')
                        ->label('Cupones Generados')
                        ->badge()
                        ->searchable(),
                ])
                    ->collapsible()
                    ->collapsed(false),
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
                Tables\Filters\Filter::make('coupon_code')
                    ->label('Código de Cupón')
                    ->form([
                        Forms\Components\TextInput::make('code')
                            ->label('Código de Cupón'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['code'],
                            fn (Builder $query, $code): Builder => $query->whereHas(
                                'mundial_coupons',
                                fn (Builder $q) => $q->where('code', 'like', "%{$code}%")
                            )
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (empty($data['code'])) {
                            return null;
                        }

                        return 'Cupón: '.$data['code'];
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->label('Fecha de Registro')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
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
            ->deferFilters()
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->slideOver(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMundialSales::route('/'),
        ];
    }
}
