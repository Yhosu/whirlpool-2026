<?php

namespace App\Repositories;

use App\Interfaces\ParticipantServiceInteface;
use App\Models\Branch;
use App\Models\Code;
use App\Models\PrizeBranch;
use App\Models\PrizeStockHistory;
use App\Models\Coupon;
use App\Models\Form;
use App\Models\InvalidParticipant;
use App\Models\MundialCoupon;
use App\Models\Participant;
use App\Models\ParticipantCoupon;
use App\Models\Prize;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParticipantService implements ParticipantServiceInteface
{
    private array $mayorista3000CiAllowlist = [
        '4272837',
        '15450770',
        '11112222',
        // Add the three authorized CI numbers here.
    ];

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

        $saleItemsData = array_map(function ($item) {
            if (! isset($item['category']) && isset($item['code_category'])) {
                $item['category'] = $item['code_category'];
            }

            return $item;
        }, $data['sale_items']);

        $sale->sale_items()->createMany($saleItemsData);
        $sale->load('valid_sale_items');         // WHIRLPOOL

        // VALIDACIONES WHIRLPOOL:
        // Solo code_brand = 2 y monto unitario >= 100 participan.
        // Se excluyen las categorías 135, 129 y 123 (repuestos y componentes).
        $validSaleItems = $sale->valid_sale_items->filter(function ($item) {
            $quantity = max(1, (int) ($item->quantity ?: 1));
            $unitAmount = (float) $item->amount / $quantity;
            $category = $item->category !== null ? (int) $item->category : null;

            return (int) $item->code_brand === 2
                && $unitAmount >= 100
                && ($category === null || ! in_array($category, [123, 129, 135], true));
        });

        $amountSale = $validSaleItems->sum('amount') ?? 0;
        $participationsQuantity = 0;

        foreach ($validSaleItems as $item) {
            $itemQuantity = max(1, (int) ($item->quantity ?: 1));
            $participationsQuantity += $itemQuantity;

            for ($var = 1; $var <= $itemQuantity; $var++) {
                // Generamos su formulario (para dismac 'code' permanece null, se identifica por control_code)
                $form = Form::create([
                    'participant_id' => $participant->id,
                    'name' => $data['name'],
                    'ci_number' => $data['ci_number'],
                    'cellphone' => $data['cellphone'],
                    'code' => null,
                    'invoice_url' => $data['invoice_url'] ?? null,
                    'city' => $data['city'],
                    'status' => 'registered',
                    'control_code' => $data['control_code'],
                    'business_unit' => $businessUnit,
                    'sale_id' => $sale->id,
                    'sale_item_id' => $item->id,
                    'branch_id' => null,
                    'prize_id' => null,
                    'user_id' => null,
                    'type' => 'dismac',
                ]);
                $validCoupons[] = [
                    'participant_id' => $participant->id,
                    'form_id' => $form->id,
                    'sale_id' => $sale->id,
                    'code' => null,
                    'status' => $form->status,
                    'control_code' => $data['control_code'],
                ];
            }
        }

        return $sale;
    }

    /**
     * Obtiene el monto unitario asociado a un formulario a partir de su ítem de venta o de la venta.
     */
    public function getUnitAmountForForm(Form $form): ?float
    {
        $item = $form->relationLoaded('sale_item') ? $form->sale_item : null;

        if (! $item && ! empty($form->sale_item_id)) {
            $item = SaleItem::find($form->sale_item_id);
        }

        if ($item) {
            $qty = max(1, (int) ($item->quantity ?: 1));

            return (float) $item->amount / $qty;
        }

        if (! empty($form->sale_id)) {
            $sale = $form->relationLoaded('sale') ? $form->sale : null;
            if (! $sale) {
                $sale = Sale::with('valid_sale_items')->find($form->sale_id);
            }
            if ($sale && $sale->valid_sale_items && $sale->valid_sale_items->isNotEmpty()) {
                $item = $sale->valid_sale_items->sortByDesc(function ($i) {
                    $qty = max(1, (int) ($i->quantity ?: 1));

                    return (float) $i->amount / $qty;
                })->first();

                if ($item) {
                    $qty = max(1, (int) ($item->quantity ?: 1));

                    return (float) $item->amount / $qty;
                }
            }
        }

        return null;
    }

    /**
     * Determina los valores de premio permitidos para un formulario según su monto unitario.
     * Reglas de la promoción:
     * - Monto unitario mayor a 100 participa.
     * - Monto unitario entre 100 y 2600: solo pueden recibir 100 o 250 Bs como premio.
     * - Monto unitario entre 2601 o más: puede recibir 100, 250, 500, 1000, 2000 o 3000 Bs.
     * - En el mercado informal, 3000 Bs solo se permite a los CI configurados.
     */
    public function getAllowedPrizeValuesForForm(Form $form): array
    {
        $unitAmount = $this->getUnitAmountForForm($form);

        if ($unitAmount !== null) {
            if ($unitAmount <= 2600) {
                $allowed = [100, 250];
            } else {
                $allowed = [100, 250, 500, 1000, 2000, 3000];
            }
        } else {
            // Si no tiene monto unitario definido (ej. mercado informal), todos los premios disponibles
            $allowed = [100, 250, 500, 1000, 2000, 3000];
        }

        if ($form->type === 'informal-market') {
            $formCiDigits = preg_replace('/[^0-9]/', '', (string) $form->ci_number);
            $allowlistDigits = array_map(fn ($ci) => preg_replace('/[^0-9]/', '', (string) $ci), $this->mayorista3000CiAllowlist);

            $isAllowedMayorista3000 = in_array(trim((string) $form->ci_number), $this->mayorista3000CiAllowlist, true)
                || (! empty($formCiDigits) && in_array($formCiDigits, $allowlistDigits, true));

            if ($isAllowedMayorista3000) {
                // EXCLUSIVO: Si está en la lista de mayoristas autorizados, SOLO puede salir el premio de 3000 Bs
                $allowed = [3000];
            } else {
                // Verificar si el carnet prioritario (15450770) ya utilizó su premio.
                // Mientras NO lo haya utilizado, el premio de 3000 Bs queda reservado y se excluye del público.
                // En cuanto el carnet 15450770 funcione y utilice su premio, se habilita 3000 para todo el público general.
                $priorityCarnetUsed = Form::where('ci_number', 'like', '%15450770%')
                    ->where('type', 'informal-market')
                    ->whereIn('status', ['used', 'delivered'])
                    ->exists();

                if (! $priorityCarnetUsed) {
                    // Se reserva 3000 y se excluye del público general mientras 15450770 no haya jugado
                    $allowed = array_values(array_diff($allowed, [3000]));
                }
                // Una vez que 15450770 ya utilizó su premio, 3000 permanece en $allowed disponible para todos
            }
        }

        // Restricción adicional temporal: Hasta el domingo 18 de octubre de 2026 (inclusive)
        // se restringe la salida máxima de premios únicamente a 100, 250 y 500 Bs,
        // respetando las reglas base ya definidas (no afecta a los mayoristas autorizados a 3000).
        if (! ($isAllowedMayorista3000 ?? false) && now()->lte(Carbon::parse('2026-10-18 23:59:59'))) {
            $allowed = array_values(array_intersect($allowed, [100, 250, 500]));
        }

        return $allowed;
    }

    /**
     * Selecciona un premio aleatorio con stock disponible en el canal ECOMMERCE
     * y realiza el descuento automático, respetando los montos de premio permitidos.
     */
    public function assignRandomEcommercePrize(string $formId): array
    {
        $branch = Branch::where('channel', 'ECOMMERCE')->first();
        if (! $branch) {
            return ['branch_id' => null, 'prize_id' => null];
        }

        $form = Form::find($formId);
        $allowedValues = $form ? $this->getAllowedPrizeValuesForForm($form) : [100, 250, 500, 1000, 2000, 3000];

        // Buscamos todos los premios activos que tengan stock > 0 en esta sucursal y monto permitido
        $availablePrizes = Prize::where('active', 1)
            ->whereIn('amount', $allowedValues)
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

    /**
     * Selecciona un premio aleatorio con stock disponible según el tipo de formulario
     * (dismac -> ECOMMERCE, informal-market -> MAYORISTA) y realiza el descuento automático.
     * Aplica la regla de premios permitidos según el monto unitario del producto.
     */
    public function assignRandomPrizeByForm(Form $form): array
    {
        $channel = ($form->type === 'informal-market') ? 'MAYORISTA' : 'ECOMMERCE';
        $branch = Branch::where('channel', $channel)->first();

        if (! $branch) {
            return [
                'success' => false,
                'message' => 'No hay stock disponible.',
                'branch_id' => null,
                'prize_id' => null,
                'code_id' => null,
            ];
        }

        $allowedValues = $this->getAllowedPrizeValuesForForm($form);

        if ($form->type === 'dismac') {
            // Para Dismac, el stock y el premio provienen directamente de los cupones disponibles en la tabla 'codes'
            $availableCode = Code::with('prize')
                ->where('is_used', false)
                ->where('status', 'available')
                ->whereNotNull('prize_id')
                ->whereIn('value', $allowedValues)
                ->inRandomOrder()
                ->lockForUpdate()
                ->first();

            if (! $availableCode) {
                return [
                    'success' => false,
                    'message' => 'No hay stock disponible.',
                    'branch_id' => $branch->id,
                    'prize_id' => null,
                    'code_id' => null,
                ];
            }

            // Blindaje de seguridad: Garantizar que el premio asociado corresponda exactamente al valor monetario del cupón
            $correctPrize = Prize::where('amount', $availableCode->value)->first();
            if ($correctPrize && ($availableCode->prize_id !== $correctPrize->id || ! $availableCode->prize)) {
                $availableCode->prize_id = $correctPrize->id;
                $availableCode->save();
                $availableCode->setRelation('prize', $correctPrize);
            }

            if (! $availableCode->prize) {
                return [
                    'success' => false,
                    'message' => 'No hay stock disponible.',
                    'branch_id' => $branch->id,
                    'prize_id' => null,
                    'code_id' => null,
                ];
            }

            // Marcar el cupón de la tabla codes como utilizado
            $availableCode->update([
                'is_used' => true,
                'status' => 'used',
                'used_at' => now(),
                'form_id' => $form->id,
            ]);

            // Generar movimiento de stock en prize_stock_history para auditoría y sincronización de prize_branch
            PrizeStockHistory::create([
                'prize_id' => $availableCode->prize_id,
                'branch_id' => $branch->id,
                'quantity' => -1,
                'type' => 'subtraction',
                'form_id' => $form->id,
                'description' => "Asignación automática {$branch->channel} al marcar como utilizado (Cupón: {$availableCode->code})",
            ]);

            return [
                'success' => true,
                'branch_id' => $branch->id,
                'prize_id' => $availableCode->prize_id,
                'code_id' => $availableCode->id,
                'prize' => $availableCode->prize,
                'assigned_code' => $availableCode,
            ];
        }

        // Para mercado informal (MAYORISTA), se mantiene la lógica de stock de PrizeBranch
        $availablePrizeBranches = PrizeBranch::with('prize')
            ->where('branch_id', $branch->id)
            ->where('stock', '>', 0)
            ->whereHas('prize', function ($query) use ($allowedValues) {
                $query->where('active', 1)
                    ->whereIn('amount', $allowedValues);
            })
            ->lockForUpdate()
            ->get();

        if ($availablePrizeBranches->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No hay stock disponible.',
                'branch_id' => $branch->id,
                'prize_id' => null,
                'code_id' => null,
            ];
        }

        $randomPrizeBranch = $availablePrizeBranches->shuffle()->first();
        $randomPrize = $randomPrizeBranch?->prize;

        if (! $randomPrize) {
            return [
                'success' => false,
                'message' => 'No hay stock disponible.',
                'branch_id' => $branch->id,
                'prize_id' => null,
                'code_id' => null,
            ];
        }

        // Generamos el movimiento de stock (resta de 1)
        PrizeStockHistory::create([
            'prize_id' => $randomPrize->id,
            'branch_id' => $branch->id,
            'quantity' => -1,
            'type' => 'subtraction',
            'form_id' => $form->id,
            'description' => "Asignación automática {$branch->channel} al marcar como utilizado",
        ]);

        return [
            'success' => true,
            'branch_id' => $branch->id,
            'prize_id' => $randomPrize->id,
            'code_id' => null,
            'prize' => $randomPrize,
            'assigned_code' => null,
        ];
    }

    /**
     * Da formato a un formulario para respuesta API, adjuntando cupón y fecha de vencimiento si corresponde a Dismac.
     */
    public function formatFormData(Form $form): array
    {
        $data = $form->toArray();

        if ($form->type === 'dismac' && $form->assignedCode) {
            $codeModel = $form->assignedCode;
            $formattedExp = $codeModel->expiration_date ? $codeModel->expiration_date->format('Y-m-d H:i:s') : null;

            $data['code'] = $codeModel->code;
            $data['expiration_date'] = $formattedExp;
            $data['assigned_code'] = [
                'id' => $codeModel->id,
                'code' => $codeModel->code,
                'product_code' => $codeModel->product_code,
                'value' => (float) $codeModel->value,
                'expiration_date' => $formattedExp,
                'days_valid' => $codeModel->days_valid,
            ];
        } else {
            $data['assigned_code'] = null;
            $data['expiration_date'] = null;
        }

        return $data;
    }

    /**
     * Valida y consulta los intentos disponibles asociados a un código (code o control_code).
     */
    public function checkCodeAttempts(string $code): array
    {
        $code = trim($code);
        if ($code === '') {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'El código proporcionado no es válido.',
                'errors' => ['Debe enviar un código válido.'],
                'data' => (object) [],
            ];
        }

        // Buscar coincidencias directas por control_code o code
        $matchedForms = Form::where(function ($query) use ($code) {
            $query->where('control_code', $code)
                  ->orWhere('code', $code);
        })->get();

        if ($matchedForms->isEmpty()) {
            return [
                'success' => false,
                'status_code' => 404,
                'message' => 'El código de control de la factura o el código único del mercado informal no es válido para participar. Verifica que incluya un producto Whirlpool y vuelve a intentarlo.',
                'errors' => ['No se encontró ningún formulario asociado al código proporcionado.'],
                'data' => (object) [],
            ];
        }

        // Si alguna coincidencia posee control_code (compra Dismac con múltiples productos),
        // agrupamos por ese control_code para obtener todos los intentos de dicha compra.
        $controlCode = $matchedForms->first(fn ($f) => ! empty($f->control_code))?->control_code;

        if (! empty($controlCode)) {
            $allForms = Form::with(['prize', 'branch', 'sale', 'assignedCode'])
                ->where('control_code', $controlCode)
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $allForms = Form::with(['prize', 'branch', 'sale', 'assignedCode'])
                ->where('code', $code)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        // Filtrar formularios según estado
        $registeredForms = $allForms->where('status', 'registered')->values();
        $usedForms = $allForms->whereIn('status', ['used', 'delivered'])->values();
        $lastForm = $registeredForms->first();

        $formattedLastForm = $lastForm ? $this->formatFormData($lastForm) : null;
        $formattedRegisteredForms = $registeredForms->map(fn ($f) => $this->formatFormData($f))->values();
        $formattedUsedForms = $usedForms->map(function ($f) {
            $formatted = $this->formatFormData($f);
            // Ambos estados (used y delivered) se visualizan en el frontend como utilizados
            $formatted['status'] = 'used';

            return $formatted;
        })->values();

        if (! $lastForm) {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'El código ya no cuenta con intentos disponibles o está en estado de validación.',
                'errors' => ['Todos los intentos han sido utilizados o no se encuentran activos.'],
                'data' => [
                    'last_form' => null,
                    'registered_count' => 0,
                    'registered_forms' => [],
                    'used_count' => $formattedUsedForms->count(),
                    'used_forms' => $formattedUsedForms,
                ],
            ];
        }

        return [
            'success' => true,
            'status_code' => 200,
            'message' => 'El código se validó con éxito.',
            'data' => [
                'last_form' => $formattedLastForm,
                'registered_count' => $formattedRegisteredForms->count(),
                'registered_forms' => $formattedRegisteredForms,
                'used_count' => $formattedUsedForms->count(),
                'used_forms' => $formattedUsedForms,
            ],
        ];
    }

    /**
     * Marca un formulario como utilizado, asignando el premio aleatorio correspondiente y descontando stock.
     */
    public function markFormAsUsed(string $formId, ?string $controlCode = null): array
    {
        $form = Form::where('id', $formId)->first();

        if (! $form) {
            return [
                'success' => false,
                'status_code' => 404,
                'message' => 'Formulario no encontrado.',
                'errors' => ['No se encontró ningún registro con el identificador UUID proporcionado.'],
                'data' => (object) [],
            ];
        }

        if ($form->status === 'used') {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'El formulario ya ha sido utilizado.',
                'errors' => ['Este intento ya fue utilizado previamente.'],
                'data' => [
                    'id' => $form->id,
                    'status' => $form->status,
                ],
            ];
        }

        if ($form->status !== 'registered') {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'Estado de formulario no válido.',
                'errors' => ["No se puede marcar como utilizado un formulario con estado '{$form->status}'."],
                'data' => [
                    'id' => $form->id,
                    'status' => $form->status,
                ],
            ];
        }

        if (! empty($controlCode) && ! empty($form->control_code) && $form->control_code !== $controlCode) {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'El código de control no coincide.',
                'errors' => ['El código de control enviado no coincide con el formulario.'],
                'data' => (object) [],
            ];
        }

        DB::beginTransaction();
        try {
            $prizeAllocation = $this->assignRandomPrizeByForm($form);

            if (! ($prizeAllocation['success'] ?? false)) {
                DB::rollBack();

                return [
                    'success' => false,
                    'status_code' => 400,
                    'message' => $prizeAllocation['message'] ?? 'No hay stock disponible.',
                    'errors' => [$prizeAllocation['message'] ?? 'No hay stock disponible.'],
                    'data' => (object) [],
                ];
            }

            $form->update([
                'branch_id' => $prizeAllocation['branch_id'],
                'prize_id' => $prizeAllocation['prize_id'],
                'code_id' => $prizeAllocation['code_id'] ?? null,
                'status' => 'used',
                'is_scratched' => 1,
            ]);

            DB::commit();

            $freshForm = $form->fresh(['prize', 'branch', 'sale', 'assignedCode', 'sale_item']);

            return [
                'success' => true,
                'status_code' => 200,
                'message' => 'Formulario marcado como utilizado con éxito.',
                'data' => $this->formatFormData($freshForm),
            ];
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            throw $e;
        }
    }

    /**
     * Registra un participante y formulario para el canal de Mercado Informal.
     */
    public function registerInformalMarketForm(array $data): Form
    {
        $name = trim((string) $data['name']);
        $cellphone = trim((string) $data['cellphone']);
        $ciNumber = trim((string) ($data['ci_number'] ?? $data['document_number'] ?? ''));
        $city = trim((string) $data['city']);
        $base64File = (string) $data['file'];

        // Subir archivo optimizado
        $uploadResult = FileUploadService::uploadOptimizedBase64($base64File, 'invoices/informal');

        // Generar código único de 6 caracteres alfanumérico
        $code = \Func::generateCode('App\Models\Form', 'code', 6);

        // Buscar o crear participante
        $participant = Participant::where('ci_number', $ciNumber)->first();
        if (! $participant) {
            $participant = Participant::create([
                'name' => $name,
                'ci_number' => $ciNumber,
                'cellphone' => $cellphone,
                'city' => $city,
                'document_type' => 'CI',
                'status' => 'registered',
                'date' => date('Y-m-d'),
                'time' => date('H:i:s'),
            ]);
        }

        // Crear formulario en estado pending para que el admin pueda validarlo con el botón "Validar"
        return Form::create([
            'name' => $name,
            'ci_number' => $ciNumber,
            'cellphone' => $cellphone,
            'city' => $city,
            'code' => $code,
            'status' => 'pending',
            'type' => 'informal-market',
            'business_unit' => 'MERCADO INFORMAL',
            'invoice_image' => $uploadResult['path'],
            'invoice_url' => $uploadResult['url'],
            'participant_id' => $participant->id,
            'branch_id' => null,
            'prize_id' => null,
            'user_id' => null,
        ]);
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
