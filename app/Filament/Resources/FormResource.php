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
use Filament\Tables\Enums\FiltersLayout;
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
                Forms\Components\TextInput::make('assigned_code_info')
                    ->label('Cupón Dismac Asignado (Relación)')
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn (?FormModel $record) => $record?->assignedCode ? "{$record->assignedCode->code} (Valor: Bs. {$record->assignedCode->value})" : 'Sin cupón asignado')
                    ->visible(fn (?FormModel $record) => $record && ($record->type === 'dismac' || $record->code_id)),
                Forms\Components\TextInput::make('control_code')
                    ->label('Código de Control')
                    ->disabled(fn () => ! static::isSpecialUser())
                    ->placeholder('Sin código de control')
                    ->formatStateUsing(fn (?FormModel $record) => $record?->control_code ?: $record?->sale?->control_code),
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
                        'Nacional' => 'Nacional',
                    ])
                    ->disabled(fn () => ! static::isSpecialUser()),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'registered' => 'Registrado',
                        'pending' => 'Pendiente',
                        'used' => 'Utilizado / Girado',
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
                    ->disk('public')
                    ->directory('temp-invoices')
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
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('ci_number')
                    ->label('CI')
                    ->icon('heroicon-m-identification')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ? "CI: {$state}" : '-'),
                Tables\Columns\TextColumn::make('cellphone')
                    ->label('Celular')
                    ->icon('heroicon-m-phone')
                    ->searchable()
                    ->copyable()
                    ->formatStateUsing(fn ($state) => $state ? "Cel: {$state}" : '-'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->icon('heroicon-m-map-pin')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ? "Ciudad: {$state}" : '-'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->icon('heroicon-m-ticket')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->formatStateUsing(function ($state, FormModel $record) {
                        if ($record->type === 'dismac' && ! $state && $record->assignedCode) {
                            return "Cupón: {$record->assignedCode->code}";
                        }
                        return $state ? "Cód: {$state}" : '-';
                    })
                    ->color(function (FormModel $record) {
                        if (($record->type === 'informal-market' || $record->business_unit === 'informal-market') && $record->status === 'pending') {
                            return 'success';
                        }
                        return null;
                    })
                    ->description(function (FormModel $record) {
                        if ($record->type === 'dismac' && $record->code && $record->assignedCode) {
                            return "Cupón Dismac: {$record->assignedCode->code}";
                        }
                        if (($record->type === 'informal-market' || $record->business_unit === 'informal-market') && $record->status === 'pending' && $record->code) {
                            return new HtmlString('<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-700/60 px-1.5 py-0.5 rounded shadow-sm">📲 Enviar por WhatsApp para girar ruleta</span>');
                        }
                        return null;
                    })
                    ->tooltip(function (FormModel $record) {
                        if (($record->type === 'informal-market' || $record->business_unit === 'informal-market') && $record->status === 'pending' && $record->code) {
                            return 'Código pendiente: Enviar por WhatsApp al cliente para que ingrese y pueda girar la ruleta.';
                        }
                        return null;
                    })
                    ->copyMessage(fn (FormModel $record) => (($record->type === 'informal-market' || $record->business_unit === 'informal-market') && $record->status === 'pending') ? '¡Código copiado! Listo para enviar por WhatsApp al cliente' : 'Código copiado'),
                Tables\Columns\TextColumn::make('assignedCode.code')
                    ->label('Cupón Dismac')
                    ->icon('heroicon-m-ticket')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->color('primary')
                    ->placeholder('-')
                    ->formatStateUsing(fn ($state) => $state ? "Cupón: {$state}" : '-'),
                Tables\Columns\TextColumn::make('control_code')
                    ->label('Cód. Control')
                    ->icon('heroicon-m-qr-code')
                    ->fontFamily('mono')
                    ->copyable()
                    ->placeholder('-')
                    ->getStateUsing(fn (FormModel $record) => $record->control_code ?: $record->sale?->control_code)
                    ->formatStateUsing(fn ($state) => $state ? "Cod. Control: {$state}" : '-')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function ($sub) use ($search) {
                            $sub->where('control_code', 'like', "%{$search}%")
                                ->orWhereHas('sale', fn ($q) => $q->where('control_code', 'like', "%{$search}%"));
                        });
                    }),
                Tables\Columns\TextColumn::make('prize.name')
                    ->label('Premio')
                    ->icon('heroicon-m-gift')
                    ->placeholder('Sin girar')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'registered' => 'Registrado',
                        'pending' => 'Pendiente',
                        'used' => 'Utilizado / Girado',
                        'invalidated' => 'Invalidado',
                        'delivered' => 'Entregado',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'registered' => 'warning',
                        'pending' => 'info',
                        'used' => 'purple',
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
                    ->icon('heroicon-m-calendar')
                    ->formatStateUsing(fn ($state) => $state ? 'Fecha: '.\Illuminate\Support\Carbon::parse($state)->format('d/m/Y H:i') : '-')
                    ->sortable(),

                Panel::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('branch.name')
                            ->label('Sucursal')
                            ->icon('heroicon-m-map')
                            ->placeholder('Sucursal: No asignada')
                            ->formatStateUsing(fn ($state, ?FormModel $record) => "Sucursal: {$state}".($record?->branch?->city ? " ({$record->branch->city})" : '')),
                        Tables\Columns\TextColumn::make('business_unit')
                            ->label('Canal / UDN')
                            ->icon('heroicon-m-building-storefront')
                            ->placeholder('Canal UDN: No especificado')
                            ->formatStateUsing(fn ($state) => "Canal UDN: ".($state ?: 'No especificado')),
                        Tables\Columns\TextColumn::make('invoice_url')
                            ->label('Factura / Nota de Venta')
                            ->icon('heroicon-m-document-text')
                            ->formatStateUsing(fn ($state, ?FormModel $record) => ($record && $record->invoice_view_url) ? 'Factura/Nota de Venta: 📄 Ver / Descargar Documento' : 'Factura/Nota de Venta: Sin documento adjunto')
                            ->color(fn ($state, ?FormModel $record) => ($record && $record->invoice_view_url) ? 'primary' : 'gray')
                            ->url(fn (?FormModel $record) => $record?->invoice_view_url)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('voucher')
                            ->label('Comprobante de Entrega / Transferencia')
                            ->icon('heroicon-m-check-badge')
                            ->formatStateUsing(function ($state, ?FormModel $record) {
                                if ($record && $record->voucher_view_url) {
                                    return 'Comprobante: ✅ Ver / Descargar Voucher';
                                }
                                if ($record && $record->status === 'delivered') {
                                    return 'Comprobante: Entregado sin comprobante digital';
                                }
                                return 'Comprobante: Sin comprobante';
                            })
                            ->color(fn ($state, ?FormModel $record) => ($record && $record->voucher_view_url) ? 'success' : 'gray')
                            ->url(fn (?FormModel $record) => $record?->voucher_view_url)
                            ->openUrlInNewTab(),
                        Tables\Columns\TextColumn::make('user.name')
                            ->label('Validación')
                            ->icon('heroicon-m-shield-check')
                            ->formatStateUsing(function ($state, ?FormModel $record) {
                                if (! $record) {
                                    return '-';
                                }

                                if ($record->type === 'dismac') {
                                    return $state ? "Validado Por: {$state}" : 'Validación: Automática (Registro Dismac)';
                                }

                                if ($state) {
                                    return "Validado Por: {$state}";
                                }

                                if ($record->status === 'pending') {
                                    return 'Validación: Pendiente de revisión';
                                }

                                return 'Validación: Registrado / Aprobado';
                            }),
                        Tables\Columns\TextColumn::make('invalidation_reason')
                            ->label('Motivo de Invalidación')
                            ->icon(fn ($state) => $state ? 'heroicon-m-exclamation-triangle' : null)
                            ->color('danger')
                            ->placeholder('')
                            ->formatStateUsing(fn ($state) => $state ? "Motivo Invalidación: {$state}" : null),
                        Tables\Columns\TextColumn::make('observations')
                            ->label('Observaciones')
                            ->icon('heroicon-m-chat-bubble-left-ellipsis')
                            ->wrap()
                            ->placeholder('Observaciones: Sin observaciones')
                            ->formatStateUsing(fn ($state) => "Observaciones: ".($state ?: 'Sin observaciones')),
                    ])->space(2),
                ])->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'registered' => 'Registrado',
                        'pending' => 'Pendiente',
                        'used' => 'Utilizado / Girado',
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
                        'Nacional' => 'Nacional',
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
                            fn (Builder $query, $value): Builder => $query->where(function ($sub) use ($value) {
                                $sub->where('control_code', 'like', "%{$value}%")
                                    ->orWhereHas('sale', fn ($q) => $q->where('control_code', 'like', "%{$value}%"));
                            })
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
            ], layout: FiltersLayout::Modal)
            ->filtersFormColumns(2)
            ->filtersFormWidth('2xl')
            ->actions([
                Tables\Actions\Action::make('validar')
                    ->label('Validar')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->requiresConfirmation()
                    ->modalHeading(fn (?FormModel $record) => '¿Está seguro de validar este registro? ('.($record?->name ?? '').')')
                    ->modalDescription(function (?FormModel $record) {
                        if (! $record) {
                            return '';
                        }
                        $invoiceLink = $record->invoice_view_url ?: '#';
                        $invoiceHtml = $invoiceLink !== '#' ? "<a href='{$invoiceLink}' target='_blank' style='color: #00aa38; text-decoration: underline; font-weight: bold;'>🔍 Ver Factura / Comprobante Adjunto</a>" : "<span style='color: #666;'>Sin adjunto</span>";

                        return new HtmlString("
                            <div style='padding: 1.2rem; background-color: #f7fffa; border-left: 4px solid #00aa38; border-radius: 8px; font-size: 0.95rem; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);'>
                                <h4 style='margin: 0 0 8px 0; font-weight: bold; color: #111;'>👤 {$record->name}</h4>
                                <div style='display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.9rem;'>
                                    <p style='margin: 0; color: #444;'><b>🎫 CI:</b> {$record->ci_number}</p>
                                    <p style='margin: 0; color: #444;'><b>📱 Celular:</b> {$record->cellphone}</p>
                                    <p style='margin: 0; color: #444;'><b>📍 Ciudad:</b> {$record->city}</p>
                                    <p style='margin: 0; color: #444;'><b>🏪 Canal:</b> {$record->business_unit}</p>
                                    <p style='margin: 0;'><b>📄 Factura:</b> {$invoiceHtml}</p>
                                    <div style='grid-column: span 2; background: #e8f5e9; padding: 6px 10px; border-radius: 6px; border: 1px dashed #4caf50; margin-top: 4px;'>
                                        <p style='margin: 0; color: #166534;'><b>🔢 Código Ruleta:</b> <span style='font-family: monospace; font-size: 1.05rem; font-weight: bold;'>{$record->code}</span></p>
                                        <p style='margin: 2px 0 0 0; font-size: 0.82rem; color: #2e7d32;'>📲 Este es el código que se debe enviar por WhatsApp al cliente ({$record->cellphone}) para que ingrese y gire la ruleta.</p>
                                    </div>
                                </div>
                            </div>
                            <p style='margin: 0; color: #555;'>¿Está seguro de validar este formulario? Al confirmarlo, su estado cambiará a <b>Registrado</b> y quedará habilitado para participar en la ruleta / lavadora.</p>
                        ");
                    })
                    ->modalSubmitActionLabel('Sí, validar registro')
                    ->visible(fn (?FormModel $record) => $record?->status === 'pending')
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
                    ->slideOver()
                    ->visible(fn () => auth()->user()?->isAdmin() ?? false),
                Tables\Actions\Action::make('entregar_premio')
                    ->label('Confirmar Transferencia')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->slideOver()
                    ->modalHeading(fn (?FormModel $record) => 'Confirmar Transferencia Bancaria: '.($record?->name ?? ''))
                    ->visible(fn (?FormModel $record) => $record && ($record->type === 'informal-market' || $record->business_unit === 'informal-market') && (in_array($record->status, ['used']) || ($record->status === 'registered' && $record->prize_id)))
                    ->form(function (FormModel $record) {
                        $prizeName = $record->prize?->name ?? 'Premio Asignado';
                        $displayPrize = $prizeName;
                        if ($record->prize?->amount && ! preg_match('/(?:Bs|\$)\b/i', $prizeName)) {
                            $displayPrize .= ' (Bs. '.$record->prize->amount.')';
                        }
                        $branchName = $record->branch ? "{$record->branch->name} ({$record->branch->city})" : 'No asignada';
                        $isInformalMarket = $record->business_unit === 'informal-market';

                        return [
                            Placeholder::make('info_entrega')
                                ->label('')
                                ->content(new HtmlString("
                                    <div style='padding: 1.2rem; background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 8px; font-size: 0.95rem; margin-bottom: 10px;'>
                                        <h4 style='margin: 0 0 6px 0; font-weight: bold; color: #166534;'>🎁 {$displayPrize}</h4>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>👤 Cliente:</b> {$record->name} (CI: {$record->ci_number})</p>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>📱 Teléfono:</b> {$record->cellphone}</p>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>🔢 Código:</b> {$record->code}</p>
                                        <p style='margin: 0 0 4px 0; color: #374151;'><b>🏢 Sucursal:</b> {$branchName}</p>
                                        ".($isInformalMarket ? "<p style='margin: 4px 0 0 0; color: #047857; font-weight: 600;'>ℹ️ Suba la foto del comprobante de transferencia bancaria para confirmar la entrega y archivar en S3 optimizado.</p>" : "")."
                                    </div>
                                ")),
                            Forms\Components\FileUpload::make('voucher')
                                ->label($isInformalMarket ? 'Comprobante de Transferencia Bancaria (Foto/PDF)' : 'Comprobante de Entrega / Voucher')
                                ->disk('public')
                                ->directory('temp-vouchers')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'application/pdf'])
                                ->maxSize(10240)
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
                Tables\Actions\Action::make('marcar_entregado')
                    ->label('Marcar como entregado')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->slideOver()
                    ->modalHeading(fn (?FormModel $record) => 'Marcar como Entregado: '.($record?->name ?? ''))
                    ->modalDescription('¿Desea marcar este cupón como entregado?')
                    ->visible(fn (?FormModel $record) => $record && ($record->type === 'dismac' || $record->business_unit === 'dismac') && in_array($record->status, ['used']))
                    ->form(function (FormModel $record) {
                        $couponCode = $record->assignedCode?->code ?? $record->assigned_code?->code ?? 'XXXXX';
                        $branchName = $record->branch?->name ?? ($record->city ?: 'X');
                        $placeholderText = "Ej: De la sucursal {$branchName} me dijeron que el cupón {$couponCode} ya fue canjeado exitosamente";

                        return [
                            Placeholder::make('info_dismac')
                                ->label('')
                                ->content(new HtmlString("
                                    <div style='padding: 1rem; background-color: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 8px; font-size: 0.95rem; margin-bottom: 8px;'>
                                        <h4 style='margin: 0 0 4px 0; font-weight: bold; color: #166534;'>🎟️ Cupón Dismac: {$couponCode}</h4>
                                        <p style='margin: 0 0 2px 0; color: #374151;'><b>👤 Cliente:</b> {$record->name} (CI: {$record->ci_number})</p>
                                        <p style='margin: 0 0 2px 0; color: #374151;'><b>🏢 Sucursal:</b> {$branchName}</p>
                                        <p style='margin: 8px 0 0 0; color: #047857; font-size: 0.88rem; font-weight: 500;'>
                                            ¿Desea confirmar que el cliente ya utilizó y canjeó este cupón en Dismac? El estado se actualizará a <b>Entregado</b>.
                                        </p>
                                    </div>
                                ")),
                            Textarea::make('observations')
                                ->label('Observaciones (Opcional)')
                                ->placeholder($placeholderText)
                                ->rows(3),
                        ];
                    })
                    ->modalSubmitActionLabel('Sí, marcar como entregado')
                    ->action(function (array $data, FormModel $record) {
                        $record->update([
                            'status' => 'delivered',
                            'observations' => ! empty($data['observations'])
                                ? ($record->observations ? $record->observations."\n".$data['observations'] : $data['observations'])
                                : $record->observations,
                            'user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Cupón Dismac Marcado como Entregado')
                            ->body('El formulario se actualizó correctamente a estado Entregado.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('anular')
                    ->label('Anular')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->slideOver()
                    ->modalHeading('Anulación de Formulario / Premio')
                    ->visible(fn (?FormModel $record) => $record && in_array($record->status, ['registered', 'used', 'pending']))
                    ->form([
                        Textarea::make('invalidation_reason')
                            ->label('Motivo de la anulación')
                            ->required()
                            ->placeholder('Escriba el motivo detallado de la anulación aquí...'),
                    ])
                    ->action(function (array $data, FormModel $record) {
                        DB::transaction(function () use ($record, $data) {
                            $freshRecord = FormModel::where('id', $record->id)->lockForUpdate()->first();
                            if (! $freshRecord || in_array($freshRecord->status, ['invalidated', 'delivered'])) {
                                return;
                            }

                            $previousStatus = $freshRecord->status;
                            $alreadyRestored = PrizeStockHistory::where('form_id', $freshRecord->id)
                                ->where('type', 'addition')
                                ->exists();

                            $assignedCode = $freshRecord->assignedCode 
                                ?: ($freshRecord->code_id ? \App\Models\Code::find($freshRecord->code_id) : null);

                            $observations = $freshRecord->observations;
                            if ($assignedCode) {
                                $releaseNote = "[Cupón Dismac {$assignedCode->code} liberado tras anulación]";
                                $observations = $observations ? "{$observations}\n{$releaseNote}" : $releaseNote;
                            }

                            $freshRecord->update([
                                'status' => 'invalidated',
                                'invalidation_reason' => $data['invalidation_reason'],
                                'observations' => $observations,
                                'user_id' => auth()->id(),
                            ]);

                            // Si tenía premio, sucursal, el estado previo era 'used' (consumió stock) y no se ha repuesto antes:
                            if ($freshRecord->prize_id && $freshRecord->branch_id && $previousStatus === 'used' && ! $alreadyRestored) {
                                PrizeStockHistory::create([
                                    'prize_id' => $freshRecord->prize_id,
                                    'branch_id' => $freshRecord->branch_id,
                                    'quantity' => 1,
                                    'type' => 'addition',
                                    'form_id' => $freshRecord->id,
                                    'user_id' => auth()->id(),
                                    'description' => 'Anulación de premio - Reposición de stock'.($assignedCode ? " (Cupón {$assignedCode->code} liberado)" : ''),
                                ]);

                                // Si es tipo Dismac y tenía cupón asignado, liberarlo en la tabla codes
                                if ($assignedCode && ($assignedCode->is_used || $assignedCode->status === 'used')) {
                                    $assignedCode->update([
                                        'is_used' => false,
                                        'status' => 'available',
                                        'used_at' => null,
                                        'form_id' => null,
                                    ]);
                                }

                                Notification::make()
                                    ->title('Formulario Anulado')
                                    ->body('Se ha invalidado el formulario, repuesto el stock del premio'.($assignedCode ? ' y liberado el cupón Dismac.' : '.'))
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
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($livewire) {
                        $query = $livewire->getFilteredTableQuery();
                        $data = $query->with(['prize', 'branch', 'sale', 'user', 'assignedCode'])->get()->map(fn ($item) => [
                            'Fecha Registro' => $item->created_at?->format('d/m/Y H:i') ?? '',
                            'Cliente' => $item->name,
                            'CI' => $item->ci_number,
                            'Teléfono' => $item->cellphone,
                            'Ciudad' => $item->city,
                            'Código Cupón' => $item->code,
                            'Cupón Dismac Asignado' => $item->assignedCode?->code ?? '',
                            'Valor Cupón Dismac (Bs)' => $item->assignedCode?->value ?? '',
                            'Código Control' => $item->control_code ?? $item->sale?->control_code ?? '',
                            'Tipo' => match ($item->type) {
                                'dismac' => 'Dismac',
                                'informal-market' => 'Mercado Informal',
                                default => $item->type ?? '',
                            },
                            'Estado' => match ($item->status) {
                                'registered' => 'Registrado',
                                'pending' => 'Pendiente',
                                'used' => 'Utilizado / Girado',
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
                            'Validado Por' => $item->user?->name ?? ($item->type === 'dismac' ? 'Automático (Dismac)' : ($item->status === 'pending' ? 'Pendiente' : 'Aprobado')),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['branch', 'prize', 'sale.valid_sale_items', 'saleItem', 'user', 'assignedCode']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    protected static function isSpecialUser(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
