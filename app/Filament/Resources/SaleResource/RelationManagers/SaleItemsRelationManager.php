<?php

namespace App\Filament\Resources\SaleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SaleItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'sale_items';

    protected static ?string $title = 'Items de Venta';

    protected static ?string $modelLabel = 'Item de Venta';

    protected static ?string $pluralModelLabel = 'Items de Venta';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->numeric(),
                Forms\Components\TextInput::make('code_brand')
                    ->label('Código de Marca')
                    ->numeric(),
                Forms\Components\TextInput::make('name_brand')
                    ->label('Nombre de Marca')
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->numeric(),
                Tables\Columns\TextColumn::make('name_brand')
                    ->label('Marca'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Cantidad')
                    ->numeric(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::Modal)
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public function canCreate(): bool
    {
        return false;
    }

    public function canEdit(Model $record): bool
    {
        return false;
    }

    public function canDelete(Model $record): bool
    {
        return false;
    }
}
