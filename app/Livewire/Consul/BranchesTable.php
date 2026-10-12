<?php

namespace App\Livewire\Consul;

use App\Models\Branch;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;

class BranchesTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Branch::query())
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city')
                    ->label('Ciudad')
                    ->badge(),
                TextColumn::make('channel')
                    ->label('Canal'),
            ])
            ->filters([
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
                    ->multiple(),
                SelectFilter::make('channel')
                    ->label('Canal')
                    ->options([
                        'RETAIL' => 'RETAIL',
                        'MAYORISTA' => 'MAYORISTA',
                        'ECOMMERCE' => 'ECOMMERCE',
                    ])
                    ->multiple(),
            ])
            ->filtersLayout(FiltersLayout::Modal)
            ->headerActions([
                CreateAction::make()
                    ->model(Branch::class)
                    ->label('Crear Sucursal')
                    ->form([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Select::make('city')
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
                            ]),
                        Select::make('channel')
                            ->label('Canal')
                            ->options([
                                'RETAIL' => 'RETAIL',
                                'MAYORISTA' => 'MAYORISTA',
                                'ECOMMERCE' => 'ECOMMERCE',
                            ]),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),
                        TextInput::make('schedule')
                            ->label('Horario')
                            ->maxLength(255),
                    ])
                    ->slideOver(),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Select::make('city')
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
                            ]),
                        Select::make('channel')
                            ->label('Canal')
                            ->options([
                                'RETAIL' => 'RETAIL',
                                'MAYORISTA' => 'MAYORISTA',
                                'ECOMMERCE' => 'ECOMMERCE',
                            ]),
                        TextInput::make('address')
                            ->label('Dirección')
                            ->maxLength(255),
                        TextInput::make('schedule')
                            ->label('Horario')
                            ->maxLength(255),
                    ])
                    ->slideOver(),
                DeleteAction::make(),
            ]);
    }

    public function render()
    {
        return view('livewire.consul.branches-table');
    }
}
