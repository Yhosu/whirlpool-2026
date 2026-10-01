<?php

namespace App\Filament\Pages;

use App\Filament\Resources\FormResource;
use App\Models\Participant;
use App\Models\ParticipantCoupon;
use App\Models\Sale;
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
use Rap2hpoutre\FastExcel\SheetCollection;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Hisense/Toshiba';

    protected static ?string $navigationLabel = 'Dashboard Hisense/Toshiba';

    use HasFiltersForm;

    public static function canAccess(): bool
    {
        return true; // Permitimos acceso para evitar el 403
    }

    public function mount(): void
    {
        if (! auth()->user()->isAdmin()) {
            redirect(FormResource::getUrl());
        }
    }

    public function filtersForm(Form $form): Form
    {
        if (! auth()->user()->isAdmin()) {
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

                                $totalParticipants = Participant::whereIn('id', Sale::whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->select('participant_id'))
                                    ->count();

                                $totalCoupons = ParticipantCoupon::whereBetween('created_at', [$start, $end])->count();

                                return new HtmlString("<div style='text-align: center; font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;'>Rango Seleccionado - Participantes con Ventas Válidas: {$totalParticipants} | Cupones: {$totalCoupons}</div>");
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
        if (! auth()->user()->isAdmin()) {
            return [];
        }

        return [
            Action::make('downloadReport')
                ->label('Descargar Reporte')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $startDate = $this->filters['startDate'] ?? now()->startOfDay();
                    $endDate = $this->filters['endDate'] ?? now()->endOfDay();

                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();

                    $participants = Participant::whereIn('id', Sale::whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->select('participant_id'))
                        ->get()->map(function ($p) {

                            return [
                                'ID' => $p->id,
                                'Nombre' => $p->name,
                                'CI' => $p->ci_number,
                                'Celular' => $p->cellphone,
                                'Ciudad' => $p->city,
                                'Estado' => $p->status,
                                'Registrado' => $p->created_at->format('Y-m-d H:i:s'),
                            ];
                        });

                    $coupons = ParticipantCoupon::with(['participant', 'sale'])
                        ->whereBetween('created_at', [$start, $end])->get()->map(function ($c) {
                            return [
                                'Código' => $c->code,
                                'Participante' => $c->participant?->name,
                                'Factura' => $c->sale?->invoice_number,
                                'UDN' => $c->business_unit,
                                'Estado' => $c->status,
                                'Registrado' => $c->created_at->format('Y-m-d H:i:s'),
                            ];
                        });

                    $sales = Sale::with('participant')->whereBetween('created_at', [$start, $end])->whereHas('valid_sale_items')->get()->map(function ($s) {
                        return [
                            'Nombre' => $s->name,
                            'CI' => $s->participant?->ci_number,
                            'Teléfono' => $s->participant?->cellphone,
                            'Tipo Venta' => $s->sale_type,
                            'Total' => $s->total_amount,
                            'Items' => $s->quantity_items,
                            'Factura N°' => $s->invoice_number,
                            'Agencia' => $s->city_agency,
                            'Código Control' => $s->control_code,
                            'URL Factura' => $s->invoice_url,
                            'Registrado' => $s->created_at->format('Y-m-d H:i:s'),
                            'Enlace Directo' => url('/admin/sales?tableAction=edit&tableActionRecord='.$s->id),
                        ];
                    });

                    $sheets = new SheetCollection([
                        'participantes' => $participants,
                        'cupones' => $coupons,
                        'ventas' => $sales,
                    ]);

                    return (new FastExcel($sheets))->download('Reporte_Dismac_'.now()->format('Ymd_His').'.xlsx');
                }),
        ];
    }
}
