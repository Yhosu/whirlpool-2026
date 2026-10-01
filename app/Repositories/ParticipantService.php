<?php

namespace App\Repositories;

use App\Interfaces\ParticipantServiceInteface;
use App\Models\Branch;
use App\Models\PrizeStockHistory;
use App\Models\Coupon;
use App\Models\Form;
use App\Models\InvalidParticipant;
use App\Models\MundialCoupon;
use App\Models\Participant;
use App\Models\ParticipantCoupon;
use App\Models\Prize;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParticipantService implements ParticipantServiceInteface
{
    protected $defaultPriceEcoId;

    protected $defaultPriceRetId;

    public function __construct(
        private readonly ParticipantRepository $participantRepository
    ) {
        $this->defaultPriceEcoId = 'b0066606-a212-4614-a730-a77ea98af544'; // UUID para valor 0
        $this->defaultPriceRetId = '14212584-5ba8-4b1f-9e81-4338100fd10a'; // UUID para valor 0
    }

    private function getCacheStore(): ?string
    {
        return app()->environment('testing') ? null : 'file';
    }

    public function registerParticipant(array $data): array
    {
        \Log::info('data', ['data' => $data]);
        try {
            $ciNumber = $data['ci_number'];
            $checkInvalidParticipant = InvalidParticipant::where('ci_number', trim($ciNumber))->first();
            abort_if(
                $checkInvalidParticipant,
                400,
                'Este participante no puede participar de esta promoción.'
            );
            $participant = $this->participantRepository->findByCiNumber($ciNumber);
            DB::beginTransaction();
            if (! $participant) {
                $data['date'] = date('Y-m-d');
                $data['time'] = date('H:i:s');
                $participant = Participant::create($data);
            }
            $result = $this->generateCoupons(
                $participant,
                $ciNumber,
                $data['sale_type'],
                $data
            );
            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating participant', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getAllQuantityPrices($type)
    {
        $pricesArrayFinal = [];
        $prices = Coupon::where('type', $type)->where('actual_quantity', '>', 0)->get();
        abort_if(count($prices) == 0, 400, 'Los códigos de cupones se agotaron y ya no se pueden generar más, verifique con administración.');
        foreach ($prices as $price) {
            for ($i = 1; $i <= $price->actual_quantity; $i++) {
                $pricesArrayFinal[] = $price->id;
            }
        }

        return $pricesArrayFinal;
    }

    public function getPriceCoupon(&$getAllQuantityPrices, $businessUnit)
    {
        if (empty($getAllQuantityPrices)) {
            return $businessUnit == 'ECO' ? $this->defaultPriceEcoId : $this->defaultPriceRetId;
        }
        shuffle($getAllQuantityPrices);
        $k = array_rand($getAllQuantityPrices);
        $randomArray = $getAllQuantityPrices[$k];
        unset($getAllQuantityPrices[$k]);

        return $randomArray;
    }

    public function calculatePriceCoupon()
    {
        $percentage = \Func::randomProbability(config('services.dismac.percentage_win'));
        if ($percentage) {
            return Coupon::where('status', 'unused')->inRandomOrder()->first() ?? null;
        }

        return null;
    }

    public function generateCoupons(
        $participant,
        $ciNumber,
        $saleType,
        $data
    ) {
        [$actualCoupons, $amountSale, $totalAmount] = [0, 0, 0];
        $validCoupons = [];
        $participationsQuantity = 0;
        $sale = $this->createSale($participant, $saleType, $data, $amountSale, $actualCoupons, $totalAmount, $validCoupons, $participationsQuantity);
        $coupons = [];
        return [
            'generate_participant_code' => $participant->id,
            'participant_link' => sprintf('%s/participate/%s', config('app.url'), $ciNumber),
            'participant_name' => $participant->name,
            'participant_email' => $participant->email,
            'participant_ci' => $participant->ci_number,
            // 'generated_coupons' => $validCoupons,
            'total_attempts' => $participationsQuantity, // Número de GIROS en la lavadora
            'message_promotion' => $participationsQuantity > 0
                ? sprintf("¡Felicidades! Usted generó %s intento(s) en la promoción WHIRLPOOL SPECIAL DAYS por la compra de su(s) producto(s) de la marca WHIRLPOOL.", $participationsQuantity)
                : '¡Gracias por su participación!'
        ];
    }

    public function createSale($participant, $saleType, $data, &$amountSale, &$actualCoupons, &$totalAmount, &$validCoupons, &$participationsQuantity)
    {
        $saleData = [
            'participant_id' => $participant->id,
            'name' => $data['name'],
            'city_agency' => $data['city_agency'],
            'sale_date' => $data['sale_date'],
            'control_code' => $data['control_code'],
            'business_unit' => $data['business_unit'],
            'quantity_items' => count($data['sale_items']),
            'total_amount' => $data['total_amount'],
            'invoice_url' => $data['invoice_url'] ?? null,
            'invoice_number' => $data['invoice_number'] ?? null,
            'sale_type' => $saleType,
        ];
        $businessUnit = $data['business_unit'];
        $sale = Sale::create($saleData);
        $sale->sale_items()->createMany($data['sale_items']);
        $sale->load('valid_sale_items');         // WHIRLPOOL

        // VALIDACIONES WHIRLPOOL
        $amountSale = $sale->valid_sale_items->sum('amount') ?? 0;
        $participationsQuantity = $sale->valid_sale_items->sum(fn ($item) => $item->quantity ?: 1);
        if ($participationsQuantity > 0) {
            for ($var = 1; $var <= $participationsQuantity; $var++) {
                // Generamos su formulario
                $code = \Func::generateCode('App\Models\Form', 'code', 6, 'alphanumeric');
                $form = Form::create([
                    'participant_id' => $participant->id,
                    'name' => $data['name'],
                    'ci_number' => $data['ci_number'],
                    'cellphone' => $data['cellphone'],
                    'code' => $code,
                    'invoice_url' => $data['invoice_url'] ?? null,
                    'city' => $data['city'],
                    'status' => 'registered',
                    'control_code' => $data['control_code'],
                    'business_unit' => $businessUnit,
                    'sale_id' => $sale->id,
                    'branch_id' => null,
                    'prize_id' => null,
                    'user_id' => null,
                    'type' => 'dismac'
                ]);
                $prizeAllocation = $this->assignRandomEcommercePrize($form->id);
                $prizeId = $prizeAllocation['prize_id'] ?? null;
                if (! $prizeId) {
                    $form->delete();
                    continue;
                }
                $form->update([
                    'branch_id' => $prizeAllocation['branch_id'],
                    'prize_id' => $prizeId,
                ]);
                $validCoupons[] = [
                    'participant_id' => $participant->id,
                    'form_id' => $form->id,
                    'sale_id' => $sale->id,
                    'code' => $code,
                    'status' => $form->status,
                    'control_code' => $data['control_code'],
                ];
            }
        }
        return $sale;
    }

    /**
     * Selecciona un premio aleatorio con stock disponible en el canal ECOMMERCE
     * y realiza el descuento automático.
     */
    public function assignRandomEcommercePrize(string $formId): array
    {
        $branch = Branch::where('channel', 'ECOMMERCE')->first();
        if (! $branch) {
            return ['branch_id' => null, 'prize_id' => null];
        }

        // Buscamos todos los premios activos que tengan stock > 0 en esta sucursal
        $availablePrizes = Prize::where('active', 1)
            ->whereHas('branches', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id)
                    ->where('stock', '>', 0);
            })->get();

        if ($availablePrizes->isEmpty()) {
            return ['branch_id' => $branch->id, 'prize_id' => null];
        }

        // Hacemos el shuffle en PHP para mayor seguridad de aleatoriedad
        $randomPrize = $availablePrizes->shuffle()->first();

        if (! $randomPrize) {
            return ['branch_id' => $branch->id, 'prize_id' => null];
        }

        // Generamos el movimiento de stock (resta de 1)
        PrizeStockHistory::create([
            'prize_id' => $randomPrize->id,
            'branch_id' => $branch->id,
            'quantity' => -1,
            'type' => 'subtraction',
            'form_id' => $formId,
            'description' => 'Asignación automática ECOMMERCE por registro de participación',
        ]);

        return [
            'branch_id' => $branch->id,
            'prize_id' => $randomPrize->id,
        ];
    }

    public function findByCiNumber($ciNumber)
    {
        $ciNumber = trim($ciNumber);

        $participant = Participant::with([
            'forms.sale',
        ])->where('ci_number', $ciNumber)->first();

        if (! $participant) {
            $formItem = Form::where('ci_number', $ciNumber)->first();
            if ($formItem) {
                $participant = Participant::create([
                    'name' => $formItem->name,
                    'ci_number' => $formItem->ci_number,
                    'cellphone' => $formItem->cellphone,
                    'city' => $formItem->city,
                    'status' => 'registered',
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                ]);
                Form::where('ci_number', $ciNumber)
                    ->whereNull('participant_id')
                    ->update(['participant_id' => $participant->id]);

                $participant->load('forms.sale');
            }
        }

        abort_if(! $participant, 400, 'El participante no se encuentra registrado.');

        Form::where('ci_number', $ciNumber)
            ->whereNull('participant_id')
            ->update(['participant_id' => $participant->id]);

        $totalAttempts = Form::where(function ($query) use ($participant, $ciNumber) {
            $query->where('ci_number', $ciNumber);
            if ($participant) {
                $query->orWhere('participant_id', $participant->id);
            }
        })
        ->where('status', 'registered')
        ->count();

        $formCoupons = $participant->forms->map(fn ($formItem) => [
            'participant_id' => $participant->id,
            'form_id' => $formItem->id,
            'sale_id' => $formItem->sale_id,
            'code' => $formItem->code,
            'status' => $formItem->status,
            'control_code' => $formItem->sale->control_code ?? null,
        ])->toArray();

        return [
            'generate_participant_code' => $participant->id,
            'participant_link' => sprintf('%s/participate/%s', config('app.url'), $ciNumber),
            'participant_name' => $participant->name,
            'participant_email' => $participant->email,
            'participant_ci' => $participant->ci_number,
            'total_attempts' => $totalAttempts,
            'message_promotion' => $totalAttempts > 0
                ? sprintf("¡Felicidades! Usted cuenta con %s intento(s) disponible(s) en la promoción WHIRLPOOL SPECIAL DAYS.", $totalAttempts)
                : 'No cuenta con intentos disponibles en este momento.'
        ];
    }

    public function getPrices()
    {
        return Prize::where('active', 1)->get();
    }

    public function getJsonParticipants()
    {
        \Log::info('getJsonParticipants');

        return Cache::store($this->getCacheStore())->remember('json_participants', 3600, function () {
            $results = DB::table('mundial_coupons')
                ->join('participants', 'mundial_coupons.participant_id', '=', 'participants.id')
                ->leftJoin('sales', 'mundial_coupons.sale_id', '=', 'sales.id')
                ->select([
                    'mundial_coupons.id as identifier',
                    'participants.name as name',
                    'mundial_coupons.code as ticket',
                    'participants.ci_number as ci_number',
                    'participants.cellphone as cellphone',
                    'mundial_coupons.status as status',
                    'participants.city as city',
                ])
                ->where('mundial_coupons.active', 1)
                ->where(function ($query) {
                    $query->where('sales.active', 1)
                        ->orWhere(function ($q) {
                            $q->whereNull('mundial_coupons.sale_id')
                                ->where('mundial_coupons.category', 'manual');
                        });
                })
                ->whereNotNull('participants.ci_number')
                ->where('participants.ci_number', '!=', '')
                ->where('participants.ci_number', '!=', '0')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('mundial_coupons as mc_sub')
                        ->whereColumn('mc_sub.participant_id', 'mundial_coupons.participant_id')
                        ->where('mc_sub.status', 'exchanged');
                })
                ->get();

            $data = [];
            foreach ($results as $key => $row) {
                $data[] = [
                    'identifier' => $row->identifier,
                    'count' => $key + 1,
                    'name' => $row->name ?? '',
                    'ticket' => $row->ticket,
                    'ci_number' => $row->ci_number,
                    'cellphone' => $row->cellphone,
                    'status' => $row->status,
                    'city' => $row->city ?? 'CLIENTE MINICUOTAS AL DÍA',
                    'ci_expedition' => '-',
                ];
            }

            return [
                'count' => count($data),
                'result' => $data,
            ];
        });
    }

    public function updateWinner($data)
    {
        $identifier = $data['identifier'];
        $code = $data['code'];
        $titlePrice = $data['titlePrice'];
        $prize = Prize::where('active', 1)->where('name', $titlePrice)->firstOrFail();
        $mundialCoupon = MundialCoupon::where('code', $code)
            ->where('id', $identifier)
            ->first();
        abort_if(! $mundialCoupon, 400, 'No existe el código de cupón enviado.');
        abort_if($mundialCoupon->status == 'exchanged', 400, 'El cupón ya tiene un premio vigente, el cupón no puede participar');
        $mundialCoupon->status = 'exchanged';
        $mundialCoupon->prize_id = $prize->id;
        $mundialCoupon->save();
        $prize->actual_quantity = $prize->actual_quantity - 1;
        $prize->save();

        Cache::store($this->getCacheStore())->forget('json_participants');

        return $mundialCoupon;
    }

    public function getWinnersByPrice($priceId)
    {
        $data = [];
        $mundialCoupons = MundialCoupon::with(['participant', 'prize'])
            ->has('participant')
            ->has('prize')
            ->where('prize_id', $priceId)
            ->where('status', 'exchanged')
            ->get();
        foreach ($mundialCoupons as $participantCoupon) {
            $data[] = [
                'name' => $participantCoupon->participant->name ?? '',
                'ci_number' => $participantCoupon->participant->ci_number ?? '',
                'cellphone' => $participantCoupon->participant->cellphone ?? '',
                'code' => $participantCoupon->code,
                'city' => $participantCoupon->category == 'legacy' ? $participantCoupon->participant->city ?? '' : 'CLIENTE MINICUOTAS AL DÍA',
                'price_name' => $participantCoupon->prize->name ?? '',
                'price_id' => $participantCoupon->prize->id ?? '',
                'price_order' => $participantCoupon->prize->order ?? '',
                'status' => $participantCoupon->status ?? '',
            ];
        }

        return $data;
    }
}
