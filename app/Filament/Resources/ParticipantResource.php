<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Hisense';

    protected static ?string $navigationLabel = 'Participantes';

    protected static ?string $modelLabel = 'Participante';

    protected static ?string $pluralModelLabel = 'Participantes';

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
                Forms\Components\Section::make('Información Personal')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cellphone')
                            ->label('Celular')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Fecha de Nacimiento'),
                    ])->columns(2),

                Forms\Components\Section::make('Documentación')
                    ->schema([
                        Forms\Components\TextInput::make('ci_number')
                            ->label('Número de CI')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ci_number_complement')
                            ->label('Complemento CI')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ci_number_expedition')
                            ->label('Expedición CI')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('document_type')
                            ->label('Tipo de Documento'),
                    ])->columns(2),

                Forms\Components\Section::make('Detalles de la Venta')
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad'),
                        Forms\Components\TextInput::make('city_agency')
                            ->label('Agencia'),
                        Forms\Components\TextInput::make('status')
                            ->label('Estado')
                            ->required(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Monto Total')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Número de Factura')
                            ->numeric(),
                        Forms\Components\TextInput::make('sale_date')
                            ->label('Fecha de Venta'),
                    ])->columns(2),

                Forms\Components\Section::make('Metadatos')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Fecha'),
                        Forms\Components\TextInput::make('time')
                            ->label('Hora'),
                        Forms\Components\Textarea::make('invoice_url')
                            ->label('URL de Factura')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->prefix('Nombre: ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ci_number')
                    ->label('CI')
                    ->prefix('CI: ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cellphone')
                    ->label('Celular')
                    ->prefix('Celular: ')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->prefix('Estado: ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completado' => 'success',
                        'pendiente' => 'warning',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\TextColumn::make('city')
                            ->label('Ciudad')
                            ->prefix('Ciudad: ')
                            ->sortable(),
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
                Tables\Filters\SelectFilter::make('city')
                    ->label('Ciudad')
                    ->options([
                        'Chuquisaca' => 'Chuquisaca',
                        'Cochabamba' => 'Cochabamba',
                        'El Alto' => 'El Alto',
                        'La Paz' => 'La Paz',
                        'Montero' => 'Montero',
                        'Santa Cruz' => 'Santa Cruz',
                        'Tarija' => 'Tarija',
                    ]),
                Tables\Filters\SelectFilter::make('document_type')
                    ->label('Tipo de Documento')
                    ->options([
                        'CI' => 'CI',
                        'PE' => 'PE',
                        'NIT' => 'NIT',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'registered' => 'Registrado',
                        'completado' => 'Completado',
                        'pendiente' => 'Pendiente',
                        'cancelado' => 'Cancelado',
                    ]),
                Tables\Filters\Filter::make('created_at')
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
                    }),
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
            'index' => Pages\ListParticipants::route('/'),
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
