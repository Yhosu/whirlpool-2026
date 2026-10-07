<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormResource\Pages;
use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use App\Models\Form as FormModel;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'CONSUL';

    protected static ?string $navigationLabel = 'Formularios';

    protected static ?string $modelLabel = 'Formulario';

    protected static ?string $pluralModelLabel = 'Formularios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre Cliente')
                    ->required()
                    ->disabled(fn () => ! static::isSpecialUser()),
                Forms\Components\TextInput::make('ci_number')
                    ->label('CI')
                    ->required()
                    ->disabled(fn () => ! static::isSpecialUser()),
                Forms\Components\TextInput::make('code')
                    ->label('Código Cupón')
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->dehydrated(fn () => static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Forms\Components\TextInput::make('participant_id')
                    ->label('ID Participante')
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Forms\Components\TextInput::make('cellphone')
                    ->label('Celular')
                    ->disabled(fn () => ! static::isSpecialUser()),
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
                    ])
                    ->disabled(fn () => ! static::isSpecialUser()),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'registered' => 'Registrado',
                        'invalidated' => 'Invalidado',
                        'delivered' => 'Entregado',
                    ])
                    ->default('registered')
                    ->required()
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Select::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('branch', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->city}")
                    ->searchable()
                    ->preload()
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Select::make('prize_id')
                    ->label('Premio Entregado')
                    ->relationship('prize', 'name')
                    ->searchable()
                    ->preload()
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Forms\Components\TextInput::make('invoice_url')
                    ->label('URL de Factura (Enlace)')
                    ->url()
                    ->columnSpanFull()
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Forms\Components\FileUpload::make('invoice_image')
                    ->label('Imagen Factura')
                    ->image()
                    ->columnSpanFull(),
                Textarea::make('invalidation_reason')
                    ->label('Razón de invalidación')
                    ->columnSpanFull()
                    ->visible(fn (Model $record) => $record?->status === 'invalidated')
                    ->disabled(fn () => ! static::isSpecialUser()),
                Textarea::make('observations')
                    ->label('Observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "Nombre: {$state}"),
                Tables\Columns\TextColumn::make('ci_number')
                    ->label('CI')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => "Carnet: {$state}"),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'registered' => 'Registrado',
                        'pending' => 'Pendiente',
                        'used' => 'Utilizado',
                        'invalidated' => 'Invalidado',
                        'delivered' => 'Entregado',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'registered' => 'warning',
                        'pending' => 'info',
                        'used' => 'primary',
                        'invalidated' => 'danger',
                        'delivered' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'dismac' => 'Dismac',
                        'informal-market' => 'Mercado Informal',
                        default => $state ? ucfirst($state) : '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'dismac' => 'primary',
                        'informal-market' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Panel::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('cellphone')
                            ->label('Teléfono')
                            ->searchable()
                            ->formatStateUsing(fn ($state) => "Teléfono: {$state}"),
                        Tables\Columns\TextColumn::make('city')
                            ->label('Ciudad')
                            ->searchable()
                            ->formatStateUsing(fn ($state) => "Ciudad: {$state}"),
                        Tables\Columns\TextColumn::make('code')
                            ->label('Código Cupón')
                            ->searchable()
                            ->weight('bold')
                            ->formatStateUsing(fn ($state) => "Código Cupón: {$state}"),
                        Tables\Columns\TextColumn::make('prize.name')
                            ->label('Premio')
                            ->wrap()
                            ->placeholder('Premio: Sin asignación previa')
                            ->formatStateUsing(fn ($state) => "Premio: {$state}"),
                        Tables\Columns\TextColumn::make('branch.name')
                            ->label('Sucursal')
                            ->sortable()
                            ->placeholder('Sucursal: No asignada')
                            ->formatStateUsing(fn ($state, FormModel $record) => "Sucursal: {$state}".($record->branch ? " ({$record->branch->city})" : '')),
                        Tables\Columns\TextColumn::make('invoice_image')
                            ->label('Factura / Boleta (Local)')
                            ->formatStateUsing(fn ($state) => $state ? '📄 Ver / Descargar Factura (Local)' : 'Sin Factura Local')
                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                            ->url(fn (Model $record) => $record->invoice_image ? asset('storage/'.$record->invoice_image) : null)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('invoice_url')
                            ->label('Factura / Boleta (Web)')
                            ->formatStateUsing(fn ($state) => $state ? '📄 Ver / Descargar Factura (URL)' : 'Sin URL Adjunta')
                            ->color(fn ($state) => $state ? 'info' : 'gray')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('voucher')
                            ->label('Comprobante de Entrega')
                            ->formatStateUsing(fn ($state) => $state ? '✅ Ver / Descargar Comprobante' : 'Sin Comprobante')
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->url(fn (Model $record) => $record->voucher ? asset('storage/'.$record->voucher) : null)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('observations')
                            ->label('Observaciones')
                            ->wrap()
                            ->placeholder('Sin observaciones')
                            ->formatStateUsing(fn ($state) => "Observaciones: {$state}"),
                    ])->space(2),
                ])->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'registered' => 'Registrado',
                        'pending' => 'Pendiente',
                        'used' => 'Utilizado',
                        'invalidated' => 'Invalidado',
                        'delivered' => 'Entregado',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Registro')
                    ->options([
                        'dismac' => 'Dismac',
                        'informal-market' => 'Mercado Informal',
                    ]),
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
                Tables\Filters\SelectFilter::make('business_unit')
                    ->label('Canal')
                    ->options([
                        'ECO' => 'ECO',
                        'RET' => 'RET',
                        'ACE' => 'ACE',
                        'WEB' => 'WEB',
                    ]),
                Tables\Filters\Filter::make('control_code')
                    ->form([
                        Forms\Components\TextInput::make('control_code')
                            ->label('Código de Control'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['control_code'],
                            fn (Builder $query, $value): Builder => $query->whereHas('sale', fn ($q) => $q->where('control_code', 'like', "%{$value}%"))
                        );
                    }),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Sucursal')
                    ->relationship('branch', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} - {$record->city}")
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('prize_id')
                    ->label('Premio')
                    ->relationship('prize', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('validar')
                    ->label('Validar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (FormModel $record) => 'Validar Formulario: '.$record->name)
                    ->modalDescription(function (FormModel $record) {
                        $invoiceLink = $record->invoice_url ?: ($record->invoice_image ? (str_starts_with($record->invoice_image, 'http') ? $record->invoice_image : asset('storage/'.$record->invoice_image)) : '#');
                        $invoiceHtml = $invoiceLink !== '#' ? "<a href='{$invoiceLink}' target='_blank' style='color: #00aa38; text-decoration: underline; font-weight: bold;'>🔍 Ver Factura / Comprobante Adjunto</a>" : "<span style='color: #666;'>Sin adjunto</span>";

                        return new HtmlString("
                            <div style='padding: 1.2rem; background-color: #f7fffa; border-left: 4px solid #00aa38; border-radius: 8px; font-size: 0.95rem; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                                <h4 style='margin: 0 0 8px 0; font-weight: bold; color: #111;'>👤 {$record->name}</h4>
                                <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.9rem;'>
                                    <p style='margin: 0; color: #444;'><b>🎫 CI:</b> {$record->ci_number}</p>
                                    <p style='margin: 0; color: #444;'><b>📱 Celular:</b> {$record->cellphone}</p>
                                    <p style='margin: 0; color: #444;'><b>📍 Ciudad:</b> {$record->city}</p>
                                    <p style='margin: 0; color: #444;'><b>🔢 Código:</b> {$record->code}</p>
                                    <p style='margin: 0; grid-column: span 2;'><b>📄 Factura:</b> {$invoiceHtml}</p>
                                </div>
                            </div>
                            <p style='margin: 0; color: #555;'>Al validar este formulario, se asignará un premio aleatorio del stock Ecommerce y su estado cambiará a <b>Registrado</b> para habilitar su participación.</p>
                        ");
                    })
                    ->visible(fn (FormModel $record) => $record->status === 'pending')
                    ->action(function (FormModel $record) {
                        /** @var \App\Interfaces\ParticipantServiceInteface $participantService */
                        $participantService = app(\App\Interfaces\ParticipantServiceInteface::class);

                        DB::transaction(function () use ($record, $participantService) {
                            $prizeAllocation = $participantService->assignRandomEcommercePrize($record->id);
                            $prizeId = $prizeAllocation['prize_id'] ?? null;
                            $branchId = $prizeAllocation['branch_id'] ?? null;

                            if (! $prizeId) {
                                Notification::make()
                                    ->title('Sin stock disponible')
                                    ->body('No se pudo asignar premio: no hay stock disponible en el canal Ecommerce.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $record->update([
                                'status' => 'registered',
                                'branch_id' => $branchId,
                                'prize_id' => $prizeId,
                                'user_id' => auth()->id(),
                            ]);

                            Notification::make()
                                ->title('Formulario Validado con Éxito')
                                ->body('Se validó el formulario y se asignó el premio correctamente.')
                                ->success()
                                ->send();
                        });
                    }),
                Tables\Actions\Action::make('entregar_ecommerce')
                    ->label('Entregar (Ecommerce)')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->modalHeading(fn (FormModel $record) => 'Confirmar entrega a '.$record->name)
                    ->modalDescription(fn (FormModel $record) => new HtmlString("¿Seguro que deseas marcar este premio ecommerce como entregado?<br><br>Cliente: <b>{$record->name}</b><br>Cupón: <b>{$record->code}</b><br>Premio a entregar: <b>".optional($record->prize)->name.'</b>'))
                    ->visible(fn (FormModel $record) => $record->status === 'registered' && $record->business_unit === 'ECO')
                    ->form(function (FormModel $record) {
                        $isCashback = optional($record->prize)->amount > 0;

                        if ($isCashback) {
                            return [
                                Forms\Components\FileUpload::make('voucher')
                                    ->label('Comprobante (Voucher)')
                                    ->image()
                                    ->required()
                                    ->directory('vouchers'),
                            ];
                        }

                        return [
                            Select::make('city')
                                ->label('Ciudad')
                                ->options(function () use ($record) {
                                    return Branch::whereHas('prizes', function ($query) use ($record) {
                                        $query->where('prize_id', $record->prize_id)
                                            ->where('stock', '>', 0);
                                    })
                                        ->pluck('city', 'city');
                                })
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($set) => $set('branch_id', null)),
                            Select::make('branch_id')
                                ->label('Sucursal de Entrega')
                                ->options(function (Get $get) use ($record) {
                                    $city = $get('city');
                                    if (! $city) {
                                        return [];
                                    }

                                    return Branch::where('city', $city)
                                        ->whereHas('prizes', function ($query) use ($record) {
                                            $query->where('prize_id', $record->prize_id)
                                                ->where('stock', '>', 0);
                                        })
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->required(),
                        ];
                    })
                    ->action(function (array $data, FormModel $record) {
                        $prize = $record->prize;
                        $amount = optional($prize)->amount ?? 0;

                        DB::transaction(function () use ($record, $data, $amount) {
                            $updateData = [
                                'status' => 'delivered',
                                'user_id' => auth()->id(),
                            ];

                            if ($amount == 0) {
                                $branchId = $data['branch_id'];
                                $updateData['branch_id'] = $branchId;

                                // La resta de stock se hace vía history
                                PrizeStockHistory::create([
                                    'prize_id' => $record->prize_id,
                                    'branch_id' => $branchId,
                                    'quantity' => -1,
                                    'type' => 'subtraction',
                                    'form_id' => $record->id,
                                    'user_id' => auth()->id(),
                                    'description' => 'Entrega de premio ecommerce física (manual)',
                                ]);
                            } else {
                                if (isset($data['voucher'])) {
                                    $updateData['voucher'] = $data['voucher'];
                                }
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('Premio Ecommerce Entregado')
                                ->success()
                                ->send();
                        });
                    }),
                Tables\Actions\Action::make('anular_ecommerce')
                    ->label('ANULAR')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('Motivo de la anulación')
                    ->visible(fn (FormModel $record) => auth()->user()?->isAdmin() && $record->status === 'registered' && $record->business_unit === 'ECO')
                    ->form([
                        Textarea::make('observations')
                            ->label('Motivo de la anulación')
                            ->required()
                            ->placeholder('Escriba el motivo aquí...'),
                    ])
                    ->action(function (array $data, FormModel $record) {
                        DB::transaction(function () use ($record, $data) {
                            // Verificar si ya se repuso el stock para evitar duplicados
                            $alreadyRestored = PrizeStockHistory::where('form_id', $record->id)
                                ->where('type', 'addition')
                                ->exists();

                            if ($alreadyRestored) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('El stock de este formulario ya ha sido repuesto.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            // Actualizar el formulario
                            $record->update([
                                'status' => 'invalidated',
                                'observations' => $data['observations'],
                                'user_id' => auth()->id(),
                            ]);

                            // Reponer stock si tiene premio y sucursal asignada
                            if ($record->prize_id && $record->branch_id) {
                                PrizeStockHistory::create([
                                    'prize_id' => $record->prize_id,
                                    'branch_id' => $record->branch_id,
                                    'quantity' => 1,
                                    'type' => 'addition',
                                    'form_id' => $record->id,
                                    'user_id' => auth()->id(),
                                    'description' => 'Anulación de premio ecommerce - Reposición de stock',
                                ]);

                                Notification::make()
                                    ->title('Formulario Anulado')
                                    ->body('Se ha invalidado el formulario y repuesto el stock del premio.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Formulario Anulado')
                                    ->body('Se ha invalidado el formulario (no se requirió reposición de stock).')
                                    ->warning()
                                    ->send();
                            }
                        });
                    }),
                Tables\Actions\Action::make('entregar_premio')
                    ->label('Asignar Premio')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->visible(fn (Model $record) => $record->status === 'registered' && $record->business_unit !== 'ECO')
                    ->form(function (Model $record) {
                        $invoiceLink = $record->invoice_image ? asset('storage/'.$record->invoice_image) : ($record->invoice_url ?: '#');
                        $invoiceHtml = $invoiceLink !== '#' ? "<a href='{$invoiceLink}' target='_blank' style='color: #00aa38; text-decoration: underline; font-weight: bold;'>Ver Imagen Adjunta</a>" : "<span style='color: #666;'>Sin adjunto</span>";

                        return [
                            Placeholder::make('kardex')
                                ->label('')
                                ->content(new HtmlString("
                                    <div style='padding: 1.2rem; background-color: #f7fffa; border-left: 4px solid #00aa38; border-radius: 8px; font-size: 0.95rem; margin-bottom: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'>
                                        <h3 style='margin: 0 0 10px 0; font-weight: 800; color: #111; font-size: 1.1rem;'>👤 {$record->name}</h3>
                                        <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 8px;'>
                                            <p style='margin: 0; color: #444;'><b>🎫 CI:</b> {$record->ci_number}</p>
                                            <p style='margin: 0; color: #444;'><b>📱 Teléfono:</b> {$record->cellphone}</p>
                                            <p style='margin: 0; color: #444;'><b>📍 Ciudad:</b> {$record->city}</p>
                                            <p style='margin: 0; color: #444;'><b>📄 Factura:</b> {$invoiceHtml}</p>
                                        </div>
                                    </div>
                                ")),
                            Select::make('branch_id')
                                ->label('Sucursal de Entrega')
                                ->options(fn () => Branch::whereHas('prizes', function ($query) {
                                    $query->where('stock', '>', 0);
                                })->get()->mapWithKeys(fn ($b) => [$b->id => "{$b->name} - {$b->city}"]))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('prize_id', null))
                                ->required(),
                            Select::make('prize_id')
                                ->label('Premio a Entregar')
                                ->options(function (Get $get) {
                                    $branchId = $get('branch_id');
                                    if (! $branchId) {
                                        return [];
                                    }

                                    return PrizeBranch::where('branch_id', $branchId)->where('stock', '>', 0)
                                        ->with('prize')
                                        ->get()
                                        ->mapWithKeys(function ($relation) {
                                            return [$relation->prize_id => "{$relation->prize->name} ({$relation->stock} u)"];
                                        });
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),
                            Forms\Components\FileUpload::make('voucher')
                                ->label('Comprobante (Voucher)')
                                ->image()
                                ->directory('vouchers')
                                ->visible(function (Get $get) {
                                    $prizeId = $get('prize_id');
                                    if (! $prizeId) {
                                        return false;
                                    }
                                    $prize = Prize::find($prizeId);

                                    return $prize && $prize->amount > 0;
                                })
                                ->required(function (Get $get) {
                                    $prizeId = $get('prize_id');
                                    if (! $prizeId) {
                                        return false;
                                    }
                                    $prize = Prize::find($prizeId);

                                    return $prize && $prize->amount > 0;
                                }),
                        ];
                    })
                    ->action(function (array $data, Model $record) {
                        $prizeId = $data['prize_id'];
                        $branchId = $data['branch_id'];

                        DB::transaction(function () use ($record, $prizeId, $branchId) {
                            $stockRelation = PrizeBranch::where('branch_id', $branchId)
                                ->where('prize_id', $prizeId)
                                ->lockForUpdate()
                                ->first();

                            if ($stockRelation && $stockRelation->stock > 0) {
                                PrizeStockHistory::create([
                                    'prize_id' => $prizeId,
                                    'branch_id' => $branchId,
                                    'quantity' => -1,
                                    'type' => 'subtraction',
                                    'form_id' => $record->id,
                                    'user_id' => auth()->id(),
                                    'description' => 'Entrega de premio manual mediante panel',
                                ]);

                                $updateData = [
                                    'status' => 'delivered',
                                    'user_id' => auth()->id(),
                                    'branch_id' => $branchId,
                                    'prize_id' => $prizeId,
                                ];

                                if (isset($data['voucher'])) {
                                    $updateData['voucher'] = $data['voucher'] ?? null;
                                }

                                $record->update($updateData);

                                Notification::make()
                                    ->title('Premio Entregado Exitosamente')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Error')
                                    ->body('No hay stock disponible para este premio en esa sucursal.')
                                    ->danger()
                                    ->send();
                            }
                        });
                    }),
                Tables\Actions\Action::make('invalidar')
                    ->label('Invalidar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Model $record) => in_array($record->status, ['registered', 'pending']) && $record->business_unit !== 'ECO')
                    ->form(fn (Model $record) => [
                        Placeholder::make('kardex_invalidation')
                            ->label('')
                            ->content(new HtmlString("
                                <div style='padding: 1.2rem; background-color: #fff5f5; border-left: 4px solid #e53e3e; border-radius: 8px; font-size: 0.95rem; margin-bottom: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'>
                                    <h3 style='margin: 0 0 10px 0; font-weight: 800; color: #c53030; font-size: 1.1rem;'>⚠️ Invalidar a: {$record->name}</h3>
                                    <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 8px;'>
                                        <p style='margin: 0; color: #444;'><b>🎫 CI:</b> {$record->ci_number}</p>
                                        <p style='margin: 0; color: #444;'><b>📱 Teléfono:</b> {$record->cellphone}</p>
                                        <p style='margin: 0; color: #444;'><b>📍 Ciudad:</b> {$record->city}</p>
                                    </div>
                                </div>
                            ")),
                        Textarea::make('invalidation_reason')
                            ->label('Motivo de invalidación')
                            ->required()
                            ->placeholder('Ej: Comprobante no válido, imagen borrosa, etc.'),
                    ])
                    ->action(function (array $data, Model $record) {
                        $record->update([
                            'status' => 'invalidated',
                            'user_id' => auth()->id(),
                            'invalidation_reason' => $data['invalidation_reason'],
                        ]);
                        Notification::make()
                            ->title('Formulario Invalidado')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('cambiar_sucursal')
                    ->label('Cambiar Sucursal')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar Sucursal / Premio')
                    ->modalDescription('Seleccione la nueva sucursal y premio. Esto restaurará el stock de la sucursal anterior y descontará de la nueva.')
                    ->visible(fn (FormModel $record) => static::isSpecialUser() && $record->status === 'delivered')
                    ->form(function (FormModel $record) {
                        return [
                            Select::make('branch_id')
                                ->label('Nueva Sucursal')
                                ->options(fn () => Branch::whereHas('prizes', function ($query) {
                                    $query->where('stock', '>', 0);
                                })->get()->mapWithKeys(fn ($b) => [$b->id => "{$b->name} - {$b->city}"]))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('prize_id', null))
                                ->required(),
                            Select::make('prize_id')
                                ->label('Nuevo Premio')
                                ->options(function (Get $get) {
                                    $branchId = $get('branch_id');
                                    if (! $branchId) {
                                        return [];
                                    }

                                    return PrizeBranch::where('branch_id', $branchId)->where('stock', '>', 0)
                                        ->with('prize')
                                        ->get()
                                        ->mapWithKeys(function ($relation) {
                                            return [$relation->prize_id => "{$relation->prize->name} ({$relation->stock} u)"];
                                        });
                                })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),
                            Textarea::make('observations')
                                ->label('Observaciones (Motivo del cambio)')
                                ->required()
                                ->placeholder('Ej: El cliente se equivocó de sucursal...'),
                        ];
                    })
                    ->action(function (array $data, FormModel $record) {
                        DB::transaction(function () use ($record, $data) {
                            $oldPrizeId = $record->prize_id;
                            $oldBranchId = $record->branch_id;
                            $newPrizeId = $data['prize_id'];
                            $newBranchId = $data['branch_id'];

                            // Verificar que la nueva relación tenga stock
                            $newStockRelation = PrizeBranch::where('branch_id', $newBranchId)
                                ->where('prize_id', $newPrizeId)
                                ->lockForUpdate()
                                ->first();

                            if (! $newStockRelation || $newStockRelation->stock <= 0) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('No hay stock disponible para el nuevo premio en esa sucursal.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            // 1. Restaurar stock al viejo
                            if ($oldPrizeId && $oldBranchId) {
                                PrizeStockHistory::create([
                                    'prize_id' => $oldPrizeId,
                                    'branch_id' => $oldBranchId,
                                    'quantity' => 1,
                                    'type' => 'addition',
                                    'form_id' => $record->id,
                                    'user_id' => auth()->id(),
                                    'description' => 'Restauración por cambio de sucursal',
                                ]);
                            }

                            // 2. Descontar stock al nuevo
                            PrizeStockHistory::create([
                                'prize_id' => $newPrizeId,
                                'branch_id' => $newBranchId,
                                'quantity' => -1,
                                'type' => 'subtraction',
                                'form_id' => $record->id,
                                'user_id' => auth()->id(),
                                'description' => 'Descuento por cambio de sucursal',
                            ]);

                            // 3. Actualizar registro
                            $currentObservations = $record->observations ? $record->observations."\n\n" : '';
                            $newObservations = $currentObservations.'[Cambio Sucursal '.now()->format('d/m/Y H:i').']: '.$data['observations'];

                            $record->update([
                                'branch_id' => $newBranchId,
                                'prize_id' => $newPrizeId,
                                'observations' => $newObservations,
                            ]);

                            Notification::make()
                                ->title('Sucursal Cambiada Exitosamente')
                                ->success()
                                ->send();
                        });
                    }),
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Model $record) => "https://wa.me/{$record->cellphone}?text=".urlencode('Hola, te hablamos de Dismac para revisar tu solicitud de la promoción Raspa y Gana Hisense...'))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultPaginationPageOption(100)
            ->paginationPageOptions([25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageForms::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return $record->business_unit === 'WEB' || static::isSpecialUser();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    protected static function isSpecialUser(): bool
    {
        return in_array(auth()->user()?->email, ['coranivico@gmail.com', 'admin@dismac.com.bo']);
    }
}
