<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\FormResource;
use App\Models\Form as FormModel;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class LatestFormsTableWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Últimos Formularios Registrados';

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isSubadmin();
    }

    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? now()->startOfDay();
        $endDate = $this->filters['endDate'] ?? now()->endOfDay();

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        return $table
            ->query(
                FormModel::query()
                    ->whereBetween('created_at', [$start, $end])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->icon('heroicon-m-user')
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('ci_number')
                    ->label('CI')
                    ->icon('heroicon-m-identification')
                    ->formatStateUsing(fn ($state) => $state ? "CI: {$state}" : '-'),
                Tables\Columns\TextColumn::make('cellphone')
                    ->label('Celular')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->formatStateUsing(fn ($state) => $state ? "Cel: {$state}" : '-'),
                Tables\Columns\TextColumn::make('city')
                    ->label('Ciudad')
                    ->icon('heroicon-m-map-pin')
                    ->formatStateUsing(fn ($state) => $state ? "Ciudad: {$state}" : '-'),
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->icon('heroicon-m-ticket')
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
                            return new \Illuminate\Support\HtmlString('<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-300 dark:border-emerald-700/60 px-1.5 py-0.5 rounded shadow-sm">📲 Enviar por WhatsApp para girar ruleta</span>');
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
                Tables\Columns\TextColumn::make('control_code')
                    ->label('Cód. Control')
                    ->icon('heroicon-m-qr-code')
                    ->fontFamily('mono')
                    ->getStateUsing(fn (FormModel $record) => $record->control_code ?: $record->sale?->control_code)
                    ->formatStateUsing(fn ($state) => $state ? "Cod. Control: {$state}" : '-')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('prize.name')
                    ->label('Premio')
                    ->icon('heroicon-m-gift')
                    ->badge()
                    ->placeholder('Sin premio')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Canal')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'dismac' => 'Dismac',
                        'informal-market' => 'Mercado Informal',
                        default => $state ? ucfirst($state) : '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'dismac' => 'primary',
                        'informal-market' => 'gray',
                        default => 'gray',
                    }),
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
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
