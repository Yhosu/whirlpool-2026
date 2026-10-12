<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Code;
use App\Models\Form;
use App\Models\Participant;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('s3');
    }

    public function test_check_code_returns_last_form_and_counts_when_registered(): void
    {
        $participant = Participant::create([
            'id' => (string) Str::uuid(),
            'name' => 'Carlos Mendoza',
            'ci_number' => '1234567',
            'cellphone' => '70012345',
            'city' => 'Santa Cruz',
            'status' => 'registered',
        ]);

        $controlCode = 'CTRL-ABC-999';

        // Form 1: registered
        $form1 = Form::create([
            'id' => (string) Str::uuid(),
            'participant_id' => $participant->id,
            'name' => $participant->name,
            'ci_number' => $participant->ci_number,
            'cellphone' => $participant->cellphone,
            'city' => $participant->city,
            'code' => 'CODE01',
            'control_code' => $controlCode,
            'status' => 'registered',
            'business_unit' => 'MERCADO INFORMAL',
        ]);

        // Form 2: used
        $form2 = Form::create([
            'id' => (string) Str::uuid(),
            'participant_id' => $participant->id,
            'name' => $participant->name,
            'ci_number' => $participant->ci_number,
            'cellphone' => $participant->cellphone,
            'city' => $participant->city,
            'code' => 'CODE02',
            'control_code' => $controlCode,
            'status' => 'used',
            'business_unit' => 'MERCADO INFORMAL',
        ]);

        // Consulta usando control_code
        $response = $this->getJson("/api/check-code/{$controlCode}");

        $response->assertStatus(200);
        $response->assertJson([
            'process' => true,
            'message' => 'El código se validó con éxito.',
        ]);

        $data = $response->json('data');
        $this->assertNotNull($data['last_form']);
        $this->assertEquals($form1->id, $data['last_form']['id']);
        $this->assertEquals('registered', $data['last_form']['status']);
        $this->assertEquals(1, $data['registered_count']);
        $this->assertEquals(1, $data['used_count']);
        $this->assertCount(1, $data['used_forms']);
        $this->assertEquals($form2->id, $data['used_forms'][0]['id']);

        // Consulta usando code directamente
        $responseByCode = $this->getJson("/api/check-code/CODE01");
        $responseByCode->assertStatus(200);
        $this->assertTrue($responseByCode->json('process'));
    }

    public function test_check_code_includes_delivered_forms_in_used_forms_and_presents_them_as_used(): void
    {
        $participant = Participant::create([
            'id' => (string) Str::uuid(),
            'name' => 'Carla Mendoza',
            'ci_number' => '44556677',
            'cellphone' => '71122334',
            'city' => 'Santa Cruz',
            'status' => 'registered',
        ]);

        $controlCode = 'CTRL-DELIVERED-TEST';

        // Form 1: registrado (intento pendiente de girar)
        $formRegistered = Form::create([
            'id' => (string) Str::uuid(),
            'participant_id' => $participant->id,
            'name' => $participant->name,
            'ci_number' => $participant->ci_number,
            'cellphone' => $participant->cellphone,
            'city' => $participant->city,
            'code' => 'COD-REG',
            'control_code' => $controlCode,
            'status' => 'registered',
            'business_unit' => 'informal-market',
        ]);

        // Form 2: used (ya giró)
        $formUsed = Form::create([
            'id' => (string) Str::uuid(),
            'participant_id' => $participant->id,
            'name' => $participant->name,
            'ci_number' => $participant->ci_number,
            'cellphone' => $participant->cellphone,
            'city' => $participant->city,
            'code' => 'COD-USED',
            'control_code' => $controlCode,
            'status' => 'used',
            'business_unit' => 'informal-market',
        ]);

        // Form 3: delivered (ya giró y se confirmó la transferencia/entrega)
        $formDelivered = Form::create([
            'id' => (string) Str::uuid(),
            'participant_id' => $participant->id,
            'name' => $participant->name,
            'ci_number' => $participant->ci_number,
            'cellphone' => $participant->cellphone,
            'city' => $participant->city,
            'code' => 'COD-DELIV',
            'control_code' => $controlCode,
            'status' => 'delivered',
            'business_unit' => 'informal-market',
        ]);

        $response = $this->getJson("/api/check-code/{$controlCode}");

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(1, $data['registered_count']);
        // used_count debe ser 2 (1 used + 1 delivered)
        $this->assertEquals(2, $data['used_count']);
        $this->assertCount(2, $data['used_forms']);

        $usedIds = collect($data['used_forms'])->pluck('id')->all();
        $this->assertContains($formUsed->id, $usedIds);
        $this->assertContains($formDelivered->id, $usedIds);

        // Ambos formularios deben presentarse en el frontend con status 'used'
        foreach ($data['used_forms'] as $usedFormItem) {
            $this->assertEquals('used', $usedFormItem['status']);
        }
    }

    public function test_check_code_returns_404_when_code_does_not_exist(): void
    {
        $response = $this->getJson('/api/check-code/NONEXISTENT999');

        $response->assertStatus(404);
        $response->assertJson([
            'process' => false,
        ]);
    }

    public function test_check_code_returns_400_when_no_forms_are_in_registered_status(): void
    {
        $participant = Participant::create([
            'id' => (string) Str::uuid(),
            'name' => 'Maria Lopez',
            'ci_number' => '7654321',
            'cellphone' => '70098765',
            'city' => 'La Paz',
            'status' => 'registered',
        ]);

        $code = 'USED01';
        Form::create([
            'id' => (string) Str::uuid(),
            'participant_id' => $participant->id,
            'name' => $participant->name,
            'ci_number' => $participant->ci_number,
            'cellphone' => $participant->cellphone,
            'city' => $participant->city,
            'code' => $code,
            'status' => 'used',
        ]);

        $response = $this->getJson("/api/check-code/{$code}");

        $response->assertStatus(400);
        $response->assertJson([
            'process' => false,
        ]);
        $this->assertEquals(0, $response->json('data.registered_count'));
        $this->assertEquals(1, $response->json('data.used_count'));
    }

    public function test_mark_as_used_assigns_ecommerce_prize_and_discounts_stock_for_dismac(): void
    {
        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        $prize = Prize::create([
            'id' => (string) Str::uuid(),
            'name' => 'Premio 500 Bs',
            'amount' => 500,
            'active' => true,
        ]);

        $prizeBranch = PrizeBranch::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize->id,
            'branch_id' => $branchEco->id,
            'stock' => 5,
        ]);

        $code = Code::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize->id,
            'code' => 'COUPON-DISMAC-999',
            'product_code' => 'TC500S',
            'value' => 500,
            'expiration_date' => now()->addDays(90),
            'days_valid' => 90,
            'status' => 'available',
            'is_used' => false,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Pedro Sanchez',
            'ci_number' => '4567890',
            'cellphone' => '71122334',
            'city' => 'Cochabamba',
            'code' => null,
            'control_code' => 'CTRL-555',
            'status' => 'registered',
            'type' => 'dismac',
            'is_scratched' => 0,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
            'control_code' => 'CTRL-555',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'process' => true,
            'message' => 'Formulario marcado como utilizado con éxito.',
            'data' => [
                'code' => 'COUPON-DISMAC-999',
                'assigned_code' => [
                    'code' => 'COUPON-DISMAC-999',
                    'product_code' => 'TC500S',
                    'value' => 500,
                ],
            ],
        ]);

        $form->refresh();
        $this->assertEquals('used', $form->status);
        $this->assertEquals(1, $form->is_scratched);
        $this->assertEquals($branchEco->id, $form->branch_id);
        $this->assertEquals($prize->id, $form->prize_id);
        $this->assertEquals($code->id, $form->code_id);

        $code->refresh();
        $this->assertTrue($code->is_used);
        $this->assertEquals('used', $code->status);
        $this->assertEquals($form->id, $code->form_id);

        $prizeBranch->refresh();
        $this->assertEquals(4, $prizeBranch->stock);

        $this->assertDatabaseHas('prize_stock_history', [
            'form_id' => $form->id,
            'prize_id' => $prize->id,
            'branch_id' => $branchEco->id,
            'quantity' => -1,
            'type' => 'subtraction',
        ]);
    }

    public function test_mark_as_used_assigns_mayorista_prize_and_discounts_stock_for_informal_market(): void
    {
        $branchMay = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'CashBack Mayoristas',
            'channel' => 'MAYORISTA',
            'city' => 'Santa Cruz',
        ]);

        $prize = Prize::create([
            'id' => (string) Str::uuid(),
            'name' => 'Premio 250 Bs',
            'amount' => 250,
            'active' => true,
        ]);

        $prizeBranch = PrizeBranch::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize->id,
            'branch_id' => $branchMay->id,
            'stock' => 3,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Maria Fernandez',
            'ci_number' => '9876543',
            'cellphone' => '72233445',
            'city' => 'Santa Cruz',
            'code' => 'COUPON88',
            'status' => 'registered',
            'type' => 'informal-market',
            'is_scratched' => 0,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'process' => true,
            'message' => 'Formulario marcado como utilizado con éxito.',
        ]);

        $form->refresh();
        $this->assertEquals('used', $form->status);
        $this->assertEquals(1, $form->is_scratched);
        $this->assertEquals($branchMay->id, $form->branch_id);
        $this->assertEquals($prize->id, $form->prize_id);

        $prizeBranch->refresh();
        $this->assertEquals(2, $prizeBranch->stock);

        $this->assertDatabaseHas('prize_stock_history', [
            'form_id' => $form->id,
            'prize_id' => $prize->id,
            'branch_id' => $branchMay->id,
            'quantity' => -1,
            'type' => 'subtraction',
        ]);
    }

    public function test_mark_as_used_fails_when_no_stock_available(): void
    {
        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        $prize = Prize::create([
            'id' => (string) Str::uuid(),
            'name' => 'Premio 100 Bs',
            'amount' => 100,
            'active' => true,
        ]);

        // Stock en 0
        PrizeBranch::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize->id,
            'branch_id' => $branchEco->id,
            'stock' => 0,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Juan Sin Stock',
            'ci_number' => '1122334',
            'cellphone' => '73344556',
            'city' => 'La Paz',
            'code' => 'NOSTOCK1',
            'status' => 'registered',
            'type' => 'dismac',
            'is_scratched' => 0,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'process' => false,
            'message' => 'No hay stock disponible.',
        ]);

        $form->refresh();
        $this->assertEquals('registered', $form->status);
        $this->assertNull($form->prize_id);
        $this->assertNull($form->branch_id);
    }

    public function test_mark_as_used_fails_if_already_used_or_not_found(): void
    {
        // 404 si el UUID no existe
        $response404 = $this->postJson('/api/mark-as-used', [
            'id' => (string) Str::uuid(),
        ]);
        $response404->assertStatus(404);
        $response404->assertJson(['process' => false]);

        // 400 si ya fue utilizado
        $formUsed = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Pedro Sanchez',
            'ci_number' => '4567890',
            'cellphone' => '71122334',
            'city' => 'Cochabamba',
            'code' => 'USED99',
            'status' => 'used',
        ]);

        $response400 = $this->postJson('/api/mark-as-used', [
            'id' => $formUsed->id,
        ]);
        $response400->assertStatus(400);
        $response400->assertJson(['process' => false]);
    }

    public function test_informal_market_form_creates_form_in_pending_status_without_prize(): void
    {
        // 1x1 transparent PNG base64
        $base64Png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $payload = [
            'name' => 'Ana Gutierrez',
            'cellphone' => '78912345',
            'file' => $base64Png,
            'document_number' => '9988776',
            'city' => 'Santa Cruz',
        ];

        $response = $this->postJson('/api/informal-market-form', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'process' => true,
            'message' => 'Su formulario se ha llenado con éxito.',
        ]);

        $createdForm = Form::where('ci_number', '9988776')->first();
        $this->assertNotNull($createdForm);
        $this->assertEquals('pending', $createdForm->status);
        $this->assertEquals('informal-market', $createdForm->type);
        $this->assertEquals('MERCADO INFORMAL', $createdForm->business_unit);
        $this->assertNull($createdForm->prize_id, 'No debe tener premio asignado en este paso');
        $this->assertNotNull($createdForm->code);
        $this->assertEquals(6, strlen($createdForm->code));

        // Verificar que se creó y asoció el participante
        $participant = \App\Models\Participant::where('ci_number', '9988776')->first();
        $this->assertNotNull($participant);
        $this->assertEquals($participant->id, $createdForm->participant_id);
    }

    public function test_informal_market_form_reuses_existing_participant_by_ci_number(): void
    {
        $base64Png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        // Crear participante previo
        $existingParticipant = \App\Models\Participant::create([
            'name' => 'Carlos Perez Existente',
            'ci_number' => '5544332',
            'cellphone' => '71122334',
            'city' => 'Cochabamba',
            'status' => 'registered',
            'date' => date('Y-m-d'),
            'time' => date('H:i:s'),
        ]);

        $payload = [
            'name' => 'Carlos Perez',
            'cellphone' => '71122334',
            'file' => $base64Png,
            'ci_number' => '5544332',
            'city' => 'Cochabamba',
        ];

        $response = $this->postJson('/api/informal-market-form', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'process' => true,
            'data' => [
                'type' => 'informal-market',
                'participant_id' => $existingParticipant->id,
            ],
        ]);

        // Verificar que no se duplicó el participante
        $this->assertEquals(1, \App\Models\Participant::where('ci_number', '5544332')->count());

        $form = Form::where('ci_number', '5544332')->first();
        $this->assertEquals($existingParticipant->id, $form->participant_id);
        $this->assertEquals('informal-market', $form->type);
    }

    public function test_informal_market_form_validates_required_fields(): void
    {
        $response = $this->postJson('/api/informal-market-form', []);

        $response->assertStatus(400);
        $response->assertJson([
            'process' => false,
            'message' => 'Error de validación en los parámetros enviados.',
        ]);
        $this->assertNotEmpty($response->json('errors'));
    }

    public function test_new_endpoints_are_excluded_from_scramble_docs(): void
    {
        $response = $this->getJson('/docs/api.json');
        $response->assertStatus(200);

        $paths = $response->json('paths');
        $this->assertArrayNotHasKey('/check-code/{code}', $paths);
        $this->assertArrayNotHasKey('/mark-as-used', $paths);
        $this->assertArrayNotHasKey('/informal-market-form', $paths);
    }

    public function test_register_participant_creates_forms_without_assigning_prize(): void
    {
        config(['services.dismac.secret_key' => 'secret-test-key']);

        $payload = [
            'ci_number' => '8765432',
            'document_type' => 'CI',
            'name' => 'Roberto Carlos',
            'email' => 'roberto@example.com',
            'cellphone' => '71234567',
            'city' => 'Santa Cruz',
            'city_agency' => 'Santa Cruz Central',
            'sale_date' => '2026-10-08',
            'control_code' => 'CTRL-SALE-123',
            'business_unit' => 'ECO',
            'sale_type' => 'CONTADO',
            'total_amount' => 5000,
            'sale_items' => [
                [
                    'code_brand' => 2,
                    'sku' => 'WHIRL-001',
                    'name' => 'Lavadora Whirlpool 16kg',
                    'quantity' => 1,
                    'amount' => 5000,
                ],
            ],
        ];

        $response = $this->postJson('/api/register-participant', $payload, [
            'X-API-KEY' => 'secret-test-key',
        ]);

        $response->assertStatus(200);

        $form = Form::where('ci_number', '8765432')->first();
        $this->assertNotNull($form);
        $this->assertEquals('registered', $form->status);
        $this->assertEquals('dismac', $form->type);
        $this->assertNull($form->code, 'El código del formulario dismac debe ser null');
        $this->assertNull($form->prize_id, 'El formulario no debe tener premio asignado al crearse la venta');
        $this->assertNull($form->branch_id, 'El formulario no debe tener sucursal asignada al crearse la venta');
        $this->assertDatabaseMissing('prize_stock_history', [
            'form_id' => $form->id,
        ]);
    }
}

