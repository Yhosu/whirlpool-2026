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

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Hisense';

    protected static ?string $navigationLabel = 'Ventas';

    protected static ?string $modelLabel = 'Venta';

    protected static ?string $pluralModelLabel = 'Ventas';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre / Participante')
                    ->prefix('Participante: ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_items')
                    ->label('Items')
                    ->prefix('Items: ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_type')
                    ->label('Tipo Venta')
                    ->prefix('Tipo Venta: ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->prefix('Total: ')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('coupons_count')
                    ->label('Cupones')
                    ->prefix('Cupones: ')
                    ->getStateUsing(fn ($record) => $record->coupons()->count()),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('invoice_number')
                            ->label('Factura N°')
                            ->prefix('Factura N°: ')
                            ->sortable(),
                        Tables\Columns\TextColumn::make('city_agency')
                            ->label('Agencia')
                            ->prefix('Agencia: ')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('sale_date')
                            ->label('Fecha Venta')
                            ->prefix('Fecha Venta: ')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('control_code')
                            ->label('Ctrl Código')
                            ->prefix('Ctrl Código: ')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('business_unit')
                            ->label('UDN')
                            ->prefix('UDN: '),
                        Tables\Columns\TextColumn::make('active')
                            ->label('Válido')
                            ->formatStateUsing(fn ($state) => $state ? 'Válido: Sí' : 'Válido: No')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                        Tables\Columns\TextColumn::make('created_at')
                            ->label('Registrado')
                            ->prefix('Registrado: ')
                            ->dateTime()
                            ->sortable(),
                    ]),
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
                Tables\Filters\TernaryFilter::make('consul_promotion')
                    ->label('Tiene Productos Consul')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('consul_valid_sale_items'),
                        false: fn (Builder $query) => $query->whereDoesntHave('consul_valid_sale_items'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::Modal)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->slideOver(),
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
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
        return true;
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
