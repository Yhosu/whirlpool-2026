<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Code;
use App\Models\Form;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WhirlpoolPrizeRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.dismac.secret_key' => 'test-api-key']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_only_code_brand_2_and_unit_amount_greater_or_equal_to_100_participate(): void
    {
        $payload = [
            'name' => 'Mario Bros',
            'ci_number' => '11223344',
            'document_type' => 'CI',
            'cellphone' => '71234567',
            'city' => 'Santa Cruz',
            'city_agency' => 'Santa Cruz Central',
            'sale_date' => '2026-10-12',
            'control_code' => 'CTRL-RULE-001',
            'business_unit' => 'ECO',
            'sale_type' => 'CONTADO',
            'total_amount' => 5660,
            'sale_items' => [
                // 1. Lavadora Whirlpool: 2 unidades por monto total 2000 (monto unitario 1000) -> 2 intentos
                [
                    'code_brand' => 2,
                    'name' => 'Lavadora Whirlpool 16kg',
                    'quantity' => 2,
                    'amount' => 2000,
                ],
                // 2. Heladera Whirlpool: 1 unidad por monto total 3500 (monto unitario 3500) -> 1 intento
                [
                    'code_brand' => 2,
                    'name' => 'Heladera Whirlpool No Frost',
                    'quantity' => 1,
                    'amount' => 3500,
                ],
                // 3. Accesorio Whirlpool: monto 80, cantidad 1 (monto unitario 80 < 100) -> 0 intentos
                [
                    'code_brand' => 2,
                    'name' => 'Filtro Whirlpool',
                    'quantity' => 1,
                    'amount' => 80,
                ],
                // 4. Otra marca (code_brand != 2): monto 80, cantidad 1 -> 0 intentos
                [
                    'code_brand' => 1,
                    'name' => 'Cafetera Otra Marca',
                    'quantity' => 1,
                    'amount' => 80,
                ],
            ],
        ];

        $response = $this->postJson('/api/register-participant', $payload, [
            'X-API-KEY' => 'test-api-key',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        // Total de intentos: 2 (lavadoras) + 1 (heladera) = 3
        $this->assertEquals(3, $data['total_attempts']);
        $this->assertStringContainsString('3 intento(s)', $data['message_promotion']);

        // Verificar que se crearon 3 formularios en base de datos
        $forms = Form::where('ci_number', '11223344')->get();
        $this->assertCount(3, $forms);

        // Los formularios deben tener asignado su sale_item_id correspondiente
        $lavadoraItem = SaleItem::where('name', 'Lavadora Whirlpool 16kg')->first();
        $heladeraItem = SaleItem::where('name', 'Heladera Whirlpool No Frost')->first();

        $this->assertNotNull($lavadoraItem);
        $this->assertNotNull($heladeraItem);

        $lavadoraForms = $forms->where('sale_item_id', $lavadoraItem->id);
        $heladeraForms = $forms->where('sale_item_id', $heladeraItem->id);

        $this->assertCount(2, $lavadoraForms);
        $this->assertCount(1, $heladeraForms);
    }

    public function test_sale_with_only_sub_100_items_generates_zero_attempts(): void
    {
        $payload = [
            'name' => 'Luigi Bros',
            'ci_number' => '99887766',
            'document_type' => 'CI',
            'cellphone' => '71234568',
            'city' => 'La Paz',
            'city_agency' => 'La Paz Central',
            'sale_date' => '2026-10-12',
            'control_code' => 'CTRL-RULE-002',
            'business_unit' => 'ECO',
            'sale_type' => 'CONTADO',
            'total_amount' => 160,
            'sale_items' => [
                [
                    'code_brand' => 2,
                    'name' => 'Detergente Whirlpool',
                    'quantity' => 2,
                    'amount' => 160, // 160 / 2 = 80 < 100
                ],
            ],
        ];

        $response = $this->postJson('/api/register-participant', $payload, [
            'X-API-KEY' => 'test-api-key',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(0, $data['total_attempts']);
        $this->assertEquals('¡Gracias por su participación!', $data['message_promotion']);

        $forms = Form::where('ci_number', '99887766')->get();
        $this->assertCount(0, $forms);
    }

    public function test_form_with_unit_amount_between_100_and_2600_only_receives_100_or_250_prize(): void
    {
        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        // Crear premios de 100, 250, 500, 1000
        $prizes = [
            100 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 100 Bs', 'amount' => 100, 'active' => true]),
            250 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 250 Bs', 'amount' => 250, 'active' => true]),
            500 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 500 Bs', 'amount' => 500, 'active' => true]),
            1000 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 1000 Bs', 'amount' => 1000, 'active' => true]),
        ];

        foreach ($prizes as $amount => $prize) {
            PrizeBranch::create([
                'id' => (string) Str::uuid(),
                'prize_id' => $prize->id,
                'branch_id' => $branchEco->id,
                'stock' => 10,
            ]);

            Code::create([
                'id' => (string) Str::uuid(),
                'prize_id' => $prize->id,
                'code' => "CUPON-{$amount}-TEST",
                'product_code' => "TC{$amount}S",
                'value' => $amount,
                'status' => 'available',
                'is_used' => false,
            ]);
        }

        $sale = Sale::create([
            'id' => (string) Str::uuid(),
            'name' => 'Ana Gomez',
            'sale_type' => 'CONTADO',
            'total_amount' => 1500,
        ]);

        $item = SaleItem::create([
            'id' => (string) Str::uuid(),
            'sale_id' => $sale->id,
            'name' => 'Lavadora Whirlpool',
            'code_brand' => 2,
            'amount' => 1500, // unit amount = 1500 (between 100 and 2600)
            'quantity' => 1,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Ana Gomez',
            'ci_number' => '55443322',
            'control_code' => 'CTRL-TIER-1',
            'status' => 'registered',
            'type' => 'dismac',
            'sale_id' => $sale->id,
            'sale_item_id' => $item->id,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
            'control_code' => 'CTRL-TIER-1',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $assignedValue = (float) $data['assigned_code']['value'];
        $this->assertContains($assignedValue, [100.0, 250.0], 'Un monto unitario entre 100 y 2600 solo debe recibir 100 o 250 Bs.');
        $this->assertNotEquals(500.0, $assignedValue);
        $this->assertNotEquals(1000.0, $assignedValue);
    }

    public function test_form_with_unit_amount_greater_than_2600_can_receive_higher_prizes(): void
    {
        // Simulamos una fecha posterior a la restricción semanal (después del domingo 18 de octubre de 2026)
        Carbon::setTestNow('2026-10-19 12:00:00');

        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        // Únicamente premio de 1000 disponible para comprobar que puede ganar premios mayores
        $prize1000 = Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 1000 Bs', 'amount' => 1000, 'active' => true]);

        PrizeBranch::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize1000->id,
            'branch_id' => $branchEco->id,
            'stock' => 5,
        ]);

        Code::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize1000->id,
            'code' => 'CUPON-1000-HIGH',
            'product_code' => 'TC1000S',
            'value' => 1000,
            'status' => 'available',
            'is_used' => false,
        ]);

        $sale = Sale::create([
            'id' => (string) Str::uuid(),
            'name' => 'Carlos VIP',
            'sale_type' => 'CONTADO',
            'total_amount' => 4500,
        ]);

        $item = SaleItem::create([
            'id' => (string) Str::uuid(),
            'sale_id' => $sale->id,
            'name' => 'Refrigerador Whirlpool',
            'code_brand' => 2,
            'amount' => 4500, // unit amount = 4500 (> 2600)
            'quantity' => 1,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Carlos VIP',
            'ci_number' => '99001122',
            'control_code' => 'CTRL-TIER-HIGH',
            'status' => 'registered',
            'type' => 'dismac',
            'sale_id' => $sale->id,
            'sale_item_id' => $item->id,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
            'control_code' => 'CTRL-TIER-HIGH',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $assignedValue = (float) $data['assigned_code']['value'];
        $this->assertEquals(1000.0, $assignedValue);
    }

    public function test_form_with_low_unit_amount_fails_gracefully_if_100_and_250_stock_is_empty(): void
    {
        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        // Solo hay premios de 1000 disponibles
        $prize1000 = Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 1000 Bs', 'amount' => 1000, 'active' => true]);

        PrizeBranch::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize1000->id,
            'branch_id' => $branchEco->id,
            'stock' => 5,
        ]);

        Code::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize1000->id,
            'code' => 'CUPON-1000-ONLY',
            'product_code' => 'TC1000S',
            'value' => 1000,
            'status' => 'available',
            'is_used' => false,
        ]);

        $sale = Sale::create([
            'id' => (string) Str::uuid(),
            'name' => 'Low Tier User',
            'sale_type' => 'CONTADO',
            'total_amount' => 1200,
        ]);

        $item = SaleItem::create([
            'id' => (string) Str::uuid(),
            'sale_id' => $sale->id,
            'name' => 'Microondas Whirlpool',
            'code_brand' => 2,
            'amount' => 1200, // unit amount = 1200 (between 100 and 2600)
            'quantity' => 1,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Low Tier User',
            'ci_number' => '44556677',
            'control_code' => 'CTRL-NO-STOCK',
            'status' => 'registered',
            'type' => 'dismac',
            'sale_id' => $sale->id,
            'sale_item_id' => $item->id,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
            'control_code' => 'CTRL-NO-STOCK',
        ]);

        // No debe asignarle el premio de 1000, debe responder con error de stock
        $response->assertStatus(400);
        $response->assertJson([
            'process' => false,
            'message' => 'No hay stock disponible.',
        ]);

        $form->refresh();
        $this->assertEquals('registered', $form->status);
        $this->assertNull($form->prize_id);
    }

    public function test_boundary_conditions_100_and_2600_and_2601(): void
    {
        $service = app(\App\Repositories\ParticipantService::class);

        // Simulamos fecha posterior al 18 de octubre de 2026 para validar las reglas base completas
        Carbon::setTestNow('2026-10-19 12:00:00');

        // Boundary 1: Exactamente 100
        $form100 = new Form();
        $item100 = new SaleItem(['amount' => 100, 'quantity' => 1]);
        $form100->setRelation('sale_item', $item100);
        $this->assertEquals([100, 250], $service->getAllowedPrizeValuesForForm($form100));

        // Boundary 2: Exactamente 2600
        $form2600 = new Form();
        $item2600 = new SaleItem(['amount' => 5200, 'quantity' => 2]); // 5200 / 2 = 2600
        $form2600->setRelation('sale_item', $item2600);
        $this->assertEquals([100, 250], $service->getAllowedPrizeValuesForForm($form2600));

        // Boundary 3: Exactamente 2601
        $form2601 = new Form();
        $item2601 = new SaleItem(['amount' => 2601, 'quantity' => 1]);
        $form2601->setRelation('sale_item', $item2601);
        $this->assertEquals([100, 250, 500, 1000, 2000, 3000], $service->getAllowedPrizeValuesForForm($form2601));

        // Ahora probamos durante la semana de restricción (hasta el domingo 18 inclusive)
        Carbon::setTestNow('2026-10-15 12:00:00');
        // Para <= 2600: sigue siendo [100, 250]
        $this->assertEquals([100, 250], $service->getAllowedPrizeValuesForForm($form2600));
        // Para > 2600: se restringe a [100, 250, 500] (se excluyen 1000, 2000, 3000)
        $this->assertEquals([100, 250, 500], $service->getAllowedPrizeValuesForForm($form2601));
    }

    public function test_admin_validating_pending_informal_market_form_changes_status_to_registered_and_sets_user_id(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Comerciante Minorista',
            'ci_number' => '88776655',
            'document_type' => 'CI',
            'cellphone' => '78901234',
            'city' => 'Santa Cruz',
            'control_code' => 'CTRL-INFORMAL-001',
            'business_unit' => 'informal-market',
            'type' => 'informal-market',
            'status' => 'pending',
            'invoice_url' => 'invoices/receipt-test.jpg',
        ]);

        $this->assertEquals('pending', $form->status);
        $this->assertNull($form->user_id);

        // Simular la acción de validación del panel administrativo
        $form->update([
            'status' => 'registered',
            'user_id' => $admin->id,
        ]);

        $form->refresh();
        $this->assertEquals('registered', $form->status);
        $this->assertEquals($admin->id, $form->user_id);
    }

    public function test_confirming_bank_transfer_optimizes_voucher_and_sets_delivered_and_user_id(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $admin = \App\Models\User::create([
            'name' => 'Admin Finanzas',
            'email' => 'finanzas@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $prize = Prize::create([
            'id' => (string) Str::uuid(),
            'name' => 'Premio 500 Bs',
            'amount' => 500,
            'active' => true,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Ganador Transferencia',
            'ci_number' => '44332211',
            'document_type' => 'CI',
            'cellphone' => '76543210',
            'city' => 'Cochabamba',
            'control_code' => 'CTRL-TRANSF-001',
            'business_unit' => 'informal-market',
            'type' => 'informal-market',
            'status' => 'used',
            'prize_id' => $prize->id,
        ]);

        // Simular subida de comprobante bancario (imagen)
        $fakeVoucher = \Illuminate\Http\UploadedFile::fake()->image('comprobante_banco.jpg', 1200, 800);

        // Optimizar y subir a través de FileUploadService
        $uploadResult = \App\Services\FileUploadService::uploadOptimizedFile($fakeVoucher, 'vouchers');

        $this->assertNotEmpty($uploadResult['path']);
        $this->assertStringStartsWith('vouchers/', $uploadResult['path']);
        $this->assertStringEndsWith('.jpg', $uploadResult['path']);

        // Actualizar el formulario a estado delivered y asignar el voucher y user_id
        $form->update([
            'status' => 'delivered',
            'voucher' => $uploadResult['path'],
            'observations' => 'Transferencia bancaria realizada con éxito',
            'user_id' => $admin->id,
        ]);

        $form->refresh();
        $this->assertEquals('delivered', $form->status);
        $this->assertEquals($uploadResult['path'], $form->voucher);
        $this->assertEquals($admin->id, $form->user_id);
        $this->assertNotNull($form->voucher_view_url);
        $this->assertStringContainsString($uploadResult['path'], $form->voucher_view_url);
    }

    public function test_excluded_categories_135_129_123_do_not_generate_attempts(): void
    {
        $payload = [
            'name' => 'Test Cliente Repuestos',
            'ci_number' => '77889900',
            'document_type' => 'CI',
            'cellphone' => '71122334',
            'city' => 'Cochabamba',
            'city_agency' => 'Cochabamba Central',
            'sale_date' => '2026-10-12',
            'control_code' => 'CTRL-CAT-EXCLUDE-001',
            'business_unit' => 'ECO',
            'sale_type' => 'CONTADO',
            'total_amount' => 6900,
            'sale_items' => [
                // 1. Repuesto Whirlpool (categoría 135) - monto unitario 1500 -> EXCLUIDO (0 intentos)
                [
                    'code_brand' => 2,
                    'category' => 135,
                    'name' => 'Repuesto Motor Lavadora Whirlpool',
                    'quantity' => 1,
                    'amount' => 1500,
                ],
                // 2. Componente Whirlpool (categoría 129) - monto unitario 800 -> EXCLUIDO (0 intentos)
                [
                    'code_brand' => 2,
                    'category' => 129,
                    'name' => 'Componente Válvula Whirlpool',
                    'quantity' => 1,
                    'amount' => 800,
                ],
                // 3. Repuesto Whirlpool (categoría 123) - monto unitario 600 -> EXCLUIDO (0 intentos)
                [
                    'code_brand' => 2,
                    'category' => 123,
                    'name' => 'Repuesto Manguera Whirlpool',
                    'quantity' => 1,
                    'amount' => 600,
                ],
                // 4. Electrodoméstico Whirlpool (categoría 50 o válida) - 1 unidad monto 4000 -> 1 intento
                [
                    'code_brand' => 2,
                    'category' => 50,
                    'name' => 'Refrigerador Whirlpool Inverter',
                    'quantity' => 1,
                    'amount' => 4000,
                ],
            ],
        ];

        $response = $this->postJson('/api/register-participant', $payload, [
            'X-API-KEY' => 'test-api-key',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        // Solo el refrigerador (categoría 50) debe generar intento (1 intento)
        $this->assertEquals(1, $data['total_attempts']);
        $this->assertStringContainsString('1 intento(s)', $data['message_promotion']);

        // Solo 1 formulario en base de datos
        $forms = Form::where('ci_number', '77889900')->get();
        $this->assertCount(1, $forms);

        // El formulario debe estar asociado al ítem de categoría 50
        $refrigeradorItem = SaleItem::where('name', 'Refrigerador Whirlpool Inverter')->first();
        $this->assertNotNull($refrigeradorItem);
        $this->assertEquals($refrigeradorItem->id, $forms->first()->sale_item_id);

        // Los ítems de repuestos deben haberse guardado en sale_items pero no en valid_sale_items
        $sale = Sale::where('control_code', 'CTRL-CAT-EXCLUDE-001')->first();
        $this->assertCount(4, $sale->sale_items);
        $this->assertCount(1, $sale->valid_sale_items);
    }

    public function test_sale_with_only_excluded_categories_generates_zero_attempts(): void
    {
        $payload = [
            'name' => 'Test Solo Repuestos',
            'ci_number' => '11992288',
            'document_type' => 'CI',
            'cellphone' => '79988776',
            'city' => 'Santa Cruz',
            'city_agency' => 'Santa Cruz Norte',
            'sale_date' => '2026-10-12',
            'control_code' => 'CTRL-CAT-EXCLUDE-002',
            'business_unit' => 'ECO',
            'sale_type' => 'CONTADO',
            'total_amount' => 2900,
            'sale_items' => [
                [
                    'code_brand' => 2,
                    'category' => '135', // enviado como string
                    'name' => 'Repuesto Bomba',
                    'quantity' => 1,
                    'amount' => 1500,
                ],
                [
                    'code_brand' => 2,
                    'category' => 129,
                    'name' => 'Componente Tarjeta Electrónica',
                    'quantity' => 1,
                    'amount' => 1400,
                ],
            ],
        ];

        $response = $this->postJson('/api/register-participant', $payload, [
            'X-API-KEY' => 'test-api-key',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(0, $data['total_attempts']);
        $this->assertEquals('¡Gracias por su participación!', $data['message_promotion']);

        $forms = Form::where('ci_number', '11992288')->get();
        $this->assertCount(0, $forms);
    }

    public function test_weekly_restriction_until_sunday_18_limits_prizes_to_100_250_and_500(): void
    {
        // Forzamos fecha durante la semana restringida (ej. jueves 15 de octubre de 2026)
        Carbon::setTestNow('2026-10-15 15:00:00');

        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        // Crear premios de 500 y 1000
        $prize500 = Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 500 Bs', 'amount' => 500, 'active' => true]);
        $prize1000 = Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 1000 Bs', 'amount' => 1000, 'active' => true]);

        foreach ([$prize500, $prize1000] as $prize) {
            PrizeBranch::create([
                'id' => (string) Str::uuid(),
                'prize_id' => $prize->id,
                'branch_id' => $branchEco->id,
                'stock' => 10,
            ]);

            Code::create([
                'id' => (string) Str::uuid(),
                'prize_id' => $prize->id,
                'code' => "CUPON-{$prize->amount}-WEEKLY",
                'product_code' => "TC{$prize->amount}S",
                'value' => $prize->amount,
                'status' => 'available',
                'is_used' => false,
            ]);
        }

        $sale = Sale::create([
            'id' => (string) Str::uuid(),
            'name' => 'Cliente Semana Especial',
            'sale_type' => 'CONTADO',
            'total_amount' => 4000,
        ]);

        $item = SaleItem::create([
            'id' => (string) Str::uuid(),
            'sale_id' => $sale->id,
            'name' => 'Cocina Whirlpool',
            'code_brand' => 2,
            'amount' => 4000, // unit amount = 4000 (> 2600)
            'quantity' => 1,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Cliente Semana Especial',
            'ci_number' => '33445566',
            'control_code' => 'CTRL-WEEKLY-LIMIT',
            'status' => 'registered',
            'type' => 'dismac',
            'sale_id' => $sale->id,
            'sale_item_id' => $item->id,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
            'control_code' => 'CTRL-WEEKLY-LIMIT',
        ]);

        $response->assertStatus(200);
        $data = $response->json('data');

        // Durante esta semana solo debe poder salir 500, NO el de 1000
        $assignedValue = (float) $data['assigned_code']['value'];
        $this->assertEquals(500.0, $assignedValue);
    }
}

