<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MinicuotasAlDiaResource\Pages;
use App\Models\MundialCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MinicuotasAlDiaResource extends Resource
{
    protected static ?string $model = MundialCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'EL MUNDIAL SE VIVE EN CASA';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Clientes minicuotas al dia';

    protected static ?string $modelLabel = 'Cliente minicuotas al dia';

    protected static ?string $pluralModelLabel = 'Clientes minicuotas al dia';

    protected static ?int $navigationSort = 2;

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
        return false;
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
            ->where('category', 'manual')
            ->with(['participant', 'prize']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código de Cupón')
                    ->icon('heroicon-m-ticket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participant.name')
                    ->label('Participante')
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->icon('heroicon-m-question-mark-circle')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'info',
                        'exchanged' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Grid::make([
                        'default' => 1,
                        'sm' => 2,
                        'md' => 3,
                    ])
                        ->schema([
                            Tables\Columns\TextColumn::make('participant.ci_number')
                                ->label('Documento CI')
                                ->prefix('CI: ')
                                ->icon('heroicon-m-identification')
                                ->formatStateUsing(function ($record) {
                                    if (! $record?->participant) {
                                        return 'N/A';
                                    }
                                    $ci = $record->participant->ci_number ?? '';
                                    $comp = $record->participant->ci_number_complement ?? '';
                                    $exp = $record->participant->ci_number_expedition ?? '';

                                    return trim("$ci $comp $exp") ?: 'N/A';
                                })
                                ->searchable(),
                            Tables\Columns\TextColumn::make('participant.cellphone')
                                ->label('Celular')
                                ->prefix('Celular: ')
                                ->icon('heroicon-m-phone')
                                ->searchable(),
                            Tables\Columns\TextColumn::make('participant.city')
                                ->label('Ciudad')
                                ->prefix('Ciudad: ')
                                ->icon('heroicon-m-map-pin'),
                            Tables\Columns\TextColumn::make('participant.email')
                                ->label('Email')
                                ->prefix('Email: ')
                                ->icon('heroicon-m-envelope')
                                ->default('N/A'),
                            Tables\Columns\TextColumn::make('participant.birth_date')
                                ->label('Fecha Nacimiento')
                                ->prefix('Fecha Nacimiento: ')
                                ->icon('heroicon-m-calendar')
                                ->date('d/m/Y')
                                ->placeholder('N/A'),
                            Tables\Columns\TextColumn::make('business_unit')
                                ->label('Unidad de Negocio')
                                ->prefix('Unidad de Negocio: ')
                                ->icon('heroicon-m-building-office'),
                            Tables\Columns\TextColumn::make('created_at')
                                ->label('Fecha Registro')
                                ->prefix('Fecha Registro: ')
                                ->icon('heroicon-m-clock')
                                ->dateTime('d/m/Y H:i:s'),
                        ]),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'created' => 'Creado',
                        'exchanged' => 'Canjeado',
                    ]),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Filters\Filter::make('created_at_range')
                    ->label('Fecha de Registro')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')->label('Hasta'),
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
            ->deferFilters()
            ->actions([])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMinicuotasAlDias::route('/'),
        ];
    }
}
