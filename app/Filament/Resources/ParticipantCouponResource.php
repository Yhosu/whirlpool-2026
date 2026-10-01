<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantCouponResource\Pages;
use App\Models\ParticipantCoupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ParticipantCouponResource extends Resource
{
    protected static ?string $model = ParticipantCoupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Hisense';

    protected static ?string $navigationLabel = 'Cupones';

    protected static ?string $modelLabel = 'Cupón';

    protected static ?string $pluralModelLabel = 'Cupones';

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
                Forms\Components\TextInput::make('sale_id')
                    ->label('ID Venta')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('prize_id')
                    ->label('ID Premio')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('participant_id')
                    ->label('ID Participante')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('business_unit')
                    ->label('Unidad de Negocio'),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('status')
                    ->label('Estado'),
                Forms\Components\Toggle::make('active')
                    ->label('Activo'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('participant.name')
                    ->label('Participante')
                    ->prefix('Participante: ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('participant.cellphone')
                    ->label('Teléfono')
                    ->prefix('Teléfono: '),
                Tables\Columns\TextColumn::make('participant.ci_number')
                    ->label('CI')
                    ->prefix('CI: ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->prefix('Código: ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('participant.city')
                    ->label('Ciudad')
                    ->prefix('Ciudad: '),
                Tables\Columns\TextColumn::make('prize.name')
                    ->label('Premio')
                    ->prefix('Premio: '),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->prefix('Estado: ')
                    ->badge(),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('sale.control_code')
                            ->label('Venta / Factura')
                            ->prefix('Factura: ')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('business_unit')
                            ->label('Unidad de Negocio')
                            ->prefix('Unidad: '),
                        Tables\Columns\TextColumn::make('active')
                            ->label('Activo')
                            ->formatStateUsing(fn ($state) => $state ? 'Activo: Sí' : 'Activo: No')
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'created' => 'Creado',
                        'exchanged' => 'Canjeado',
                    ]),
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Filters\TernaryFilter::make('prize_id')
                    ->label('Con premio')
                    ->nullable()
                    ->placeholder('Todos')
                    ->trueLabel('Sí')
                    ->falseLabel('No'),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::Modal)
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParticipantCoupons::route('/'),
        ];
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
}
