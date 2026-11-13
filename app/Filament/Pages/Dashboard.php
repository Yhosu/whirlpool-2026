<?php

namespace App\Filament\Pages;

use App\Filament\Resources\FormResource;
use App\Models\Form as FormModel;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Rap2hpoutre\FastExcel\FastExcel;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Whirlpool';

    protected static ?string $navigationLabel = 'Dashboard';

    use HasFiltersForm;

    public static function canAccess(): bool
    {
        return true;
    }

    public function mount(): void
    {
        if (! (auth()->user()->isAdmin() || auth()->user()->isSubadmin())) {
            redirect(FormResource::getUrl());
        }
    }

    public function filtersForm(Form $form): Form
    {
        if (! (auth()->user()->isAdmin() || auth()->user()->isSubadmin())) {
            return $form->schema([]);
        }

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Placeholder::make('totals')
                            ->hiddenLabel()
                            ->content(function (Get $get) {
                                $start = $get('startDate') ? Carbon::parse($get('startDate'))->startOfDay() : now()->startOfDay();
                                $end = $get('endDate') ? Carbon::parse($get('endDate'))->endOfDay() : now()->endOfDay();

                                $query = FormModel::whereBetween('created_at', [$start, $end]);
                                $totalForms = (clone $query)->count();
                                $withPrize = (clone $query)->whereNotNull('prize_id')->count();
                                $delivered = (clone $query)->whereIn('status', ['delivered', 'used'])->count();
                                $pending = (clone $query)->whereIn('status', ['registered', 'pending'])->count();
                                $invalidated = (clone $query)->where('status', 'invalidated')->count();
                                $uniqueClients = (clone $query)->whereNotNull('ci_number')->distinct('ci_number')->count('ci_number');

                                return new HtmlString("
                                    <div class='flex flex-wrap items-center justify-center gap-4 text-center py-1 text-sm font-semibold text-gray-700 dark:text-gray-200'>
                                        <div>Total Formularios: <span class='text-primary-600 font-bold text-base'>{$totalForms}</span></div>
                                        <span class='text-gray-400 font-normal'>|</span>
                                        <div>Premios Asignados: <span class='text-emerald-600 font-bold text-base'>{$withPrize}</span></div>
                                        <span class='text-gray-400 font-normal'>|</span>
                                        <div>Canjeados / Entregados: <span class='text-emerald-600 font-bold text-base'>{$delivered}</span></div>
                                        <span class='text-gray-400 font-normal'>|</span>
                                        <div>Pendientes: <span class='text-amber-600 font-bold text-base'>{$pending}</span></div>
                                        <span class='text-gray-400 font-normal'>|</span>
                                        <div>Invalidados: <span class='text-rose-600 font-bold text-base'>{$invalidated}</span></div>
                                        <span class='text-gray-400 font-normal'>|</span>
                                        <div>Clientes Únicos (CI): <span class='text-blue-600 font-bold text-base'>{$uniqueClients}</span></div>
                                    </div>
                                ");
                            }),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('startDate')
                                    ->label('Desde')
                                    ->default(now()->startOfDay())
                                    ->live(),
                                DatePicker::make('endDate')
                                    ->label('Hasta')
                                    ->default(now()->endOfDay())
                                    ->live(),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        if (! (auth()->user()->isAdmin() || auth()->user()->isSubadmin())) {
            return [];
        }

        return [
            Action::make('downloadReport')
                ->label('Descargar Reporte Formularios')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $startDate = $this->filters['startDate'] ?? now()->startOfDay();
                    $endDate = $this->filters['endDate'] ?? now()->endOfDay();

                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();

                    $forms = FormModel::with(['prize', 'branch', 'user'])
                        ->whereBetween('created_at', [$start, $end])
                        ->get()
                        ->map(function ($f) {
                            return [
                                'ID' => $f->id,
                                'Código Cupón' => $f->code,
                                'Cliente' => $f->name,
                                'CI' => $f->ci_number,
                                'Teléfono' => $f->cellphone,
                                'Ciudad' => $f->city,
                                'Canal / Tipo' => match ($f->type) {
                                    'dismac' => 'Dismac',
                                    'informal-market' => 'Mercado Informal',
                                    default => $f->type ?? '-',
                                },
                                'Estado' => match ($f->status) {
                                    'registered' => 'Registrado',
                                    'pending' => 'Pendiente',
                                    'used' => 'Utilizado / Girado',
                                    'delivered' => 'Entregado',
                                    'invalidated' => 'Invalidado',
                                    default => $f->status,
                                },
                                'Premio' => $f->prize?->name ?? 'Sin premio',
                                'Sucursal' => $f->branch?->name ?? 'No asignada',
                                'Ruleta Girada' => $f->is_scratched ? 'Sí' : 'No',
                                'URL Factura' => $f->invoice_view_url ?? '',
                                'Comprobante Voucher' => $f->voucher_view_url ?? '',
                                'Razón Invalidación' => $f->invalidation_reason ?? '',
                                'Observaciones' => $f->observations ?? '',
                                'Fecha Registro' => $f->created_at?->format('Y-m-d H:i:s'),
                            ];
                        });

                    return (new FastExcel($forms))->download('Reporte_Whirlpool_Formularios_'.now()->format('Ymd_His').'.xlsx');
                }),
        ];
    }
}
