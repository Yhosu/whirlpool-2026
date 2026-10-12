<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormResource\Pages;
use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use App\Models\Form as FormModel;
use App\Services\FileUploadService;
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
use Rap2hpoutre\FastExcel\FastExcel;

class FormResource extends Resource
{
    protected static ?string $model = FormModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Whirlpool';

    protected static ?string $navigationLabel = 'Formularios';

    protected static ?string $modelLabel = 'Formulario';

    protected static ?string $pluralModelLabel = 'Formularios';

    protected static ?int $navigationSort = 1;

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
                        'pending' => 'Pendiente',
                        'used' => 'Utilizado',
                        'invalidated' => 'Invalidado',
                        'delivered' => 'Entregado',
                    ])
                    ->default('registered')
                    ->required()
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->hidden(fn () => ! static::isSpecialUser()),
                Select::make('type')
                    ->label('Tipo de Registro')
                    ->options([
                        'dismac' => 'Dismac',
                        'informal-market' => 'Mercado Informal',
                    ])
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
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('ci_number')
                    ->label('CI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('prize.name')
                    ->label('Premio')
                    ->placeholder('Sin girar')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
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
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

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
                        Tables\Columns\TextColumn::make('invoice_url')
                            ->label('Factura / Boleta')
                            ->formatStateUsing(fn ($state, FormModel $record) => $record->invoice_view_url ? '📄 Ver / Descargar Factura' : 'Sin Factura Adjunta')
                            ->color(fn ($state, FormModel $record) => $record->invoice_view_url ? 'primary' : 'gray')
                            ->url(fn (FormModel $record) => $record->invoice_view_url)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('voucher')
                            ->label('Comprobante de Entrega / Transferencia')
                            ->formatStateUsing(fn ($state) => $state ? '✅ Ver / Descargar Comprobante' : 'Sin Comprobante')
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->url(fn (FormModel $record) => $record->voucher_view_url)
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
                Tables\Actions\Action::make('validar')
                    ->label('Validar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading(fn (FormModel $record) => '¿Está seguro de validar este registro? ('.$record->name.')')
                    ->modalDescription(function (FormModel $record) {
                        $invoiceLink = $record->invoice_view_url ?: '#';
                        $invoiceHtml = $invoiceLink !== '#' ? "<a href='{$invoiceLink}' target='_blank' style='color: #00aa38; text-decoration: underline; font-weight: bold;'>🔍 Ver Factura / Comprobante Adjunto</a>" : "<span style='color: #666;'>Sin adjunto</span>";

                        return new HtmlString("
                            <div style='padding: 1.2rem; background-color: #f7fffa; border-left: 4px solid #00aa38; border-radius: 8px; font-size: 0.95rem; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                                <h4 style='margin: 0 0 8px 0; font-weight: bold; color: #111;'>👤 {$record->name}</h4>
                                <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.9rem;'>
                                    <p style='margin: 0; color: #444;'><b>🎫 CI:</b> {$record->ci_number}</p>
                                    <p style='margin: 0; color: #444;'><b>📱 Celular:</b> {$record->cellphone}</p>
                                    <p style='margin: 0; color: #444;'><b>📍 Ciudad:</b> {$record->city}</p>
                                    <p style='margin: 0; color: #444;'><b>🔢 Código:</b> {$record->code}</p>
                                    <p style='margin: 0; color: #444;'><b>🏪 Canal:</b> {$record->business_unit}</p>
                                    <p style='margin: 0;'><b>📄 Factura:</b> {$invoiceHtml}</p>
                                </div>
                            </div>
                            <p style='margin: 0; color: #555;'>¿Está seguro de validar este formulario? Al confirmarlo, su estado cambiará a <b>Registrado</b> y quedará habilitado para participar en la ruleta / lavadora.</p>
                        ");
                    })
                    ->modalSubmitActionLabel('Sí, validar registro')
                    ->visible(fn (FormModel $record) => $record->status === 'pending')
                    ->action(function (FormModel $record) {
                        $record->update([
                            'status' => 'registered',
                            'user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Formulario Validado con Éxito')
                            ->body('Se validó el formulario correctamente y quedó listo para participar.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make()
                    ->slideOver(),
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\Action::make('entregar_premio')
                    ->label(fn (FormModel $record) => $record->business_unit === 'informal-market' ? 'Confirmar Transferencia' : 'Entregar Premio')
                    ->icon(fn (FormModel $record) => $record->business_unit === 'informal-market' ? 'heroicon-o-banknotes' : 'heroicon-o-check-circle')
                    ->color('success')
                    ->slideOver()
                    ->modalHeading(fn (FormModel $record) => $record->business_unit === 'informal-market'
                        ? 'Confirmar Transferencia Bancaria: '.$record->name
                        : 'Confirmar Entrega de Premio a: '.$record->name)
                    ->visible(fn (FormModel $record) => in_array($record->status, ['used']) || ($record->status === 'registered' && $record->prize_id))
                    ->form(function (FormModel $record) {
                        $prizeName = $record->prize?->name ?? 'Premio Asignado';
                        $prizeAmount = $record->prize?->amount ? 'Bs. '.$record->prize->amount : '';
                        $branchName = $record->branch ? "{$record->branch->name} ({$record->branch->city})" : 'No asignada';
                        $isInformalMarket = $record->business_unit === 'informal-market';

                        return [
                            Placeholder::make('info_entrega')
                                ->label('')
                                ->content(new HtmlString("
                                    <div style='padding: 1.2rem; background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 8px; font-size: 0.95rem; margin-bottom: 10px;'>
                                        <h4 style='margin: 0 0 6px 0; font-weight: bold; color: #166534;'>🎁 {$prizeName} {$prizeAmount}</h4>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>👤 Cliente:</b> {$record->name} (CI: {$record->ci_number})</p>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>📱 Teléfono:</b> {$record->cellphone}</p>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>🔢 Código:</b> {$record->code}</p>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>🏢 Sucursal:</b> {$branchName}</p>
                                        ".($isInformalMarket ? "<p style='margin: 4px 0 0 0; color: #047857; font-weight: 600;'>ℹ️ Suba la foto del comprobante de transferencia bancaria para confirmar la entrega y archivar en S3 optimizado.</p>" : "")."
                                    </div>
                                ")),
                            Forms\Components\FileUpload::make('voucher')
                                ->label($isInformalMarket ? 'Comprobante de Transferencia Bancaria (Foto/PDF)' : 'Comprobante de Entrega / Voucher')
                                ->image()
                                ->directory('temp-vouchers')
                                ->required(),
                            Textarea::make('observations')
                                ->label('Observaciones de Entrega')
                                ->placeholder($isInformalMarket ? 'Ej: Transferencia bancaria realizada con éxito' : 'Ej: Entregado en efectivo en sucursal / Transferencia confirmada'),
                        ];
                    })
                    ->action(function (array $data, FormModel $record) {
                        $voucherPath = $record->voucher;

                        if (!empty($data['voucher'])) {
                            try {
                                $uploadResult = FileUploadService::uploadOptimizedFile($data['voucher'], 'vouchers');
                                $voucherPath = $uploadResult['path'];
                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Error al procesar el comprobante')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }

                        $record->update([
                            'status' => 'delivered',
                            'voucher' => $voucherPath,
                            'observations' => !empty($data['observations'])
                                ? ($record->observations ? $record->observations."\n".$data['observations'] : $data['observations'])
                                : $record->observations,
                            'user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title($record->business_unit === 'informal-market' ? 'Transferencia Bancaria Confirmada' : 'Premio Entregado con Éxito')
                            ->body('El comprobante fue optimizado y guardado en S3. El estado cambió a Entregado.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->slideOver()
                    ->modalHeading('Anulación de Formulario / Premio')
                    ->visible(fn (FormModel $record) => in_array($record->status, ['registered', 'used', 'pending']))
                    ->form([
                        Textarea::make('invalidation_reason')
                            ->label('Motivo de la anulación')
                            ->required()
                            ->placeholder('Escriba el motivo detallado de la anulación aquí...'),
                    ])
                    ->action(function (array $data, FormModel $record) {
                        DB::transaction(function () use ($record, $data) {
                            $previousStatus = $record->status;
                            $alreadyRestored = PrizeStockHistory::where('form_id', $record->id)
                                ->where('type', 'addition')
                                ->exists();

                            $record->update([
                                'status' => 'invalidated',
                                'invalidation_reason' => $data['invalidation_reason'],
                                'user_id' => auth()->id(),
                            ]);

                            if ($record->prize_id && $record->branch_id && $previousStatus === 'used' && ! $alreadyRestored) {
                                PrizeStockHistory::create([
                                    'prize_id' => $record->prize_id,
                                    'branch_id' => $record->branch_id,
                                    'quantity' => 1,
                                    'type' => 'addition',
                                    'form_id' => $record->id,
                                    'user_id' => auth()->id(),
                                    'description' => 'Anulación de premio - Reposición de stock',
                                ]);

                                Notification::make()
                                    ->title('Formulario Anulado')
                                    ->body('Se ha invalidado el formulario y repuesto el stock del premio.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Formulario Anulado')
                                    ->body('Se ha invalidado el formulario.')
                                    ->warning()
                                    ->send();
                            }
                        });
                    }),
                Tables\Actions\Action::make('cambiar_sucursal')
                    ->label('Cambiar Sucursal')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->slideOver()
                    ->requiresConfirmation()
                    ->modalHeading('Cambiar Sucursal / Premio')
                    ->modalDescription('Seleccione la nueva sucursal y premio. Esto restaurará el stock de la sucursal anterior y descontará de la nueva.')
                    ->visible(fn (FormModel $record) => static::isSpecialUser() && in_array($record->status, ['delivered', 'used']))
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
                    ->url(fn (Model $record) => "https://wa.me/{$record->cellphone}?text=".urlencode('Hola, te hablamos de Dismac para revisar tu solicitud de la promoción Whirlpool Special Days...'))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $data = $query->with(['prize', 'branch', 'sale', 'user'])->get()->map(fn ($item) => [
                            'Fecha Registro' => $item->created_at?->format('d/m/Y H:i') ?? '',
                            'Cliente' => $item->name,
                            'CI' => $item->ci_number,
                            'Teléfono' => $item->cellphone,
                            'Ciudad' => $item->city,
                            'Código Cupón' => $item->code,
                            'Código Control' => $item->control_code ?? $item->sale?->control_code ?? '',
                            'Tipo' => match ($item->type) {
                                'dismac' => 'Dismac',
                                'informal-market' => 'Mercado Informal',
                                default => $item->type ?? '',
                            },
                            'Estado' => match ($item->status) {
                                'registered' => 'Registrado',
                                'pending' => 'Pendiente',
                                'used' => 'Utilizado',
                                'delivered' => 'Entregado',
                                'invalidated' => 'Invalidado',
                                default => $item->status ?? '',
                            },
                            'Premio' => $item->prize?->name ?? 'Sin asignar',
                            'Valor Premio (Bs)' => $item->prize?->amount ?? 0,
                            'Sucursal' => $item->branch?->name ?? 'Sin asignar',
                            'Canal UDN' => $item->business_unit ?? '',
                            'Factura / Comprobante' => $item->invoice_view_url ?? '',
                            'Comprobante Voucher' => $item->voucher_view_url ?? '',
                            'Validado Por' => $item->user?->name ?? '',
                            'Observaciones' => $item->observations ?? '',
                            'Motivo Invalidación' => $item->invalidation_reason ?? '',
                        ]);

                        return (new FastExcel($data))->download('formularios_'.now()->format('Ymd_His').'.xlsx');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
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
        return auth()->user()?->isAdmin() || static::isSpecialUser();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    protected static function isSpecialUser(): bool
    {
        return auth()->user()?->isAdmin() || in_array(auth()->user()?->email, ['coranivico@gmail.com', 'admin@dismac.com.bo']);
    }
}
