<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Whirlpool';

    protected static ?string $navigationLabel = 'Sucursales';

    protected static ?string $modelLabel = 'Sucursal';

    protected static ?string $pluralModelLabel = 'Sucursales';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('city')
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
                Forms\Components\Select::make('channel')
                    ->label('Canal')
                    ->options([
                        'RETAIL' => 'RETAIL',
                        'MAYORISTA' => 'MAYORISTA',
                        'ECOMMERCE' => 'ECOMMERCE',
                    ]),
                Forms\Components\TextInput::make('address')
                    ->label('Dirección')
                    ->maxLength(255),
                Forms\Components\TextInput::make('schedule')
                    ->label('Horario')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Canal')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBranches::route('/'),
        ];
    }
}
