<?php

namespace App\Filament\Resources\MundialSaleResource\Pages;

use App\Filament\Resources\MundialSaleResource;
use App\Models\MundialCoupon;
use App\Models\Participant;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Rap2hpoutre\FastExcel\FastExcel;

class ListMundialSales extends ListRecords
{
    protected static string $resource = MundialSaleResource::class;

    // Kept for backward compatibility with active client sessions to prevent Livewire exceptions during deployment
    public $shouldEditOnLoad = null;

    public $editRecordId = null;

    public $hasAttemptedAutoOpen = null;

    public function mount(): void
    {
        parent::mount();

        $editRecordId = request()->query('edit_record');
        if ($editRecordId) {
            redirect(route('filament.admin.resources.mundial-sales.index', [
                'tableAction' => 'edit',
                'tableActionRecord' => $editRecordId,
            ]));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Descargar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $query = $this->getFilteredSortedTableQuery();

                    $sales = $query->with(['participant'])->get();

                    $data = $sales->map(function ($sale) {
                        return [
                            'Participante' => $sale->name ?? ($sale->participant?->name ?? 'N/A'),
                            'Items' => $sale->quantity_items,
                            'Tipo Venta' => $sale->sale_type ?? 'N/A',
                            'Total' => $sale->total_amount,
                            'Cupones' => $sale->mundial_coupons_count ?? 0,
                            'Factura' => $sale->invoice_number ?? 'N/A',
                            'Factura Link' => $sale->invoice_url ?? 'N/A',
                            'CI' => $sale->participant?->ci_number ?? 'N/A',
                            'Celular' => $sale->participant?->cellphone ?? 'N/A',
                            'Ciudad/Agencia' => $sale->city_agency ?? 'N/A',
                            'Cód. Control' => $sale->control_code ?? 'N/A',
                            'UDN' => $sale->business_unit ?? 'N/A',
                            'Estado' => $sale->active ? 'Válido' : 'No Válido',
                            'Registrado' => $sale->created_at?->format('d/m/Y H:i:s') ?? 'N/A',
                            'Link Editar' => route('filament.admin.resources.mundial-sales.index', [
                                'tableAction' => 'edit',
                                'tableActionRecord' => $sale->id,
                            ]),
                        ];
                    });

                    return response()->streamDownload(function () use ($data) {
                        (new FastExcel($data))->export('php://output');
                    }, 'ventas_mundial_'.now()->format('Ymd_His').'.xlsx', [
                        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ]);
                }),
        ];
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $search = $this->getTableSearch();

        if (empty($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search) {
            if (preg_match('/[0-9]/', $search)) {
                if (is_numeric($search)) {
                    // 1. Purely numeric (CI, Cellphone, Invoice Number)
                    $participantIds = Participant::where('ci_number', 'like', "{$search}%")
                        ->orWhere('cellphone', 'like', "{$search}%")
                        ->limit(200)
                        ->pluck('id')
                        ->toArray();

                    $q->where('sales.invoice_number', $search);
                    if (! empty($participantIds)) {
                        $q->orWhereIn('sales.participant_id', $participantIds);
                    }
                } else {
                    // 2. Alphanumeric with digits (Coupon code candidate)
                    $saleIds = MundialCoupon::where('code', 'like', "{$search}%")
                        ->limit(200)
                        ->pluck('sale_id')
                        ->filter()
                        ->unique()
                        ->toArray();

                    if (! empty($saleIds)) {
                        $q->whereIn('sales.id', $saleIds);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                }
            } else {
                // 3. Purely alphabetic/spaces (Name, City, Control Code, or Purely Alphabetic Coupon)
                $saleIds = [];
                if (strlen($search) >= 3) {
                    $saleIds = MundialCoupon::where('code', 'like', "{$search}%")
                        ->limit(200)
                        ->pluck('sale_id')
                        ->filter()
                        ->unique()
                        ->toArray();
                }

                $words = array_filter(explode(' ', $search));

                $q->where(function (Builder $subQ) use ($words, $search) {
                    if (! empty($words)) {
                        $subQ->where(function (Builder $subSubQ) use ($words) {
                            foreach ($words as $word) {
                                $subSubQ->where('sales.name', 'like', "%{$word}%");
                            }
                        });
                    }
                    $subQ->orWhere('sales.city_agency', 'like', "%{$search}%")
                        ->orWhere('sales.control_code', 'like', "%{$search}%");
                });

                if (! empty($saleIds)) {
                    $q->orWhereIn('sales.id', $saleIds);
                }
            }
        });
    }
}
