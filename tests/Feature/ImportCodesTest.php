<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Code;
use App\Models\Form;
use App\Models\Prize;
use App\Models\PrizeBranch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImportCodesTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_codes_command_populates_codes_table_from_excel_files(): void
    {
        // Seed prizes
        $prizes = [
            100 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 100 Bs', 'amount' => 100, 'active' => true]),
            250 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 250 Bs', 'amount' => 250, 'active' => true]),
            500 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 500 Bs', 'amount' => 500, 'active' => true]),
            1000 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 1000 Bs', 'amount' => 1000, 'active' => true]),
            2000 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 2000 Bs', 'amount' => 2000, 'active' => true]),
            3000 => Prize::create(['id' => (string) Str::uuid(), 'name' => 'Premio 3000 Bs', 'amount' => 3000, 'active' => true]),
        ];

        $this->artisan('app:import-codes')
            ->assertSuccessful();

        $this->assertEquals(288, Code::count());
        $this->assertEquals(288, Code::where('is_used', false)->where('status', 'available')->count());

        // Verify each prize has exact imported codes matching excels
        $this->assertEquals(170, Code::where('prize_id', $prizes[100]->id)->count());
        $this->assertEquals(75, Code::where('prize_id', $prizes[250]->id)->count());
        $this->assertEquals(14, Code::where('prize_id', $prizes[500]->id)->count());
        $this->assertEquals(15, Code::where('prize_id', $prizes[1000]->id)->count());
        $this->assertEquals(7, Code::where('prize_id', $prizes[2000]->id)->count());
        $this->assertEquals(7, Code::where('prize_id', $prizes[3000]->id)->count());

        // Verify expiration date is parsed and not null
        $sample = Code::first();
        $this->assertNotNull($sample->expiration_date);
        $this->assertNotNull($sample->product_code);
        $this->assertNotNull($sample->code);
    }

    public function test_mark_as_used_assigns_coupon_and_marks_it_used_in_codes_table(): void
    {
        $branchEco = Branch::create([
            'id' => (string) Str::uuid(),
            'name' => 'Central Dismac',
            'channel' => 'ECOMMERCE',
            'city' => 'Santa Cruz',
        ]);

        $prize = Prize::create([
            'id' => (string) Str::uuid(),
            'name' => 'Premio 1000 Bs',
            'amount' => 1000,
            'active' => true,
        ]);

        PrizeBranch::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize->id,
            'branch_id' => $branchEco->id,
            'stock' => 10,
        ]);

        $code = Code::create([
            'id' => (string) Str::uuid(),
            'prize_id' => $prize->id,
            'code' => 'UNIQUE-VOUCHER-XYZ',
            'product_code' => 'TC1000S',
            'value' => 1000,
            'expiration_date' => now()->addDays(90),
            'days_valid' => 90,
            'status' => 'available',
            'is_used' => false,
        ]);

        $form = Form::create([
            'id' => (string) Str::uuid(),
            'name' => 'Juan Perez',
            'ci_number' => '1234567',
            'cellphone' => '70012345',
            'city' => 'Santa Cruz',
            'code' => null,
            'control_code' => 'CTRL-DISMAC-1',
            'status' => 'registered',
            'type' => 'dismac',
            'is_scratched' => 0,
        ]);

        $response = $this->postJson('/api/mark-as-used', [
            'id' => $form->id,
            'control_code' => 'CTRL-DISMAC-1',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'process' => true,
            'data' => [
                'code' => 'UNIQUE-VOUCHER-XYZ',
                'assigned_code' => [
                    'code' => 'UNIQUE-VOUCHER-XYZ',
                    'product_code' => 'TC1000S',
                    'value' => 1000,
                ],
            ],
        ]);

        $code->refresh();
        $this->assertTrue($code->is_used);
        $this->assertEquals('used', $code->status);
        $this->assertEquals($form->id, $code->form_id);
        $this->assertNotNull($code->used_at);

        $form->refresh();
        $this->assertEquals($code->id, $form->code_id);
        $this->assertNull($form->code, 'El campo code en tabla forms permanece null para dismac');

        // Verificar que /api/check-code devuelve el formulario usado con su cupón asignado
        $checkResponse = $this->getJson('/api/check-code/CTRL-DISMAC-1');
        $checkResponse->assertStatus(400); // Todos los intentos usados
        $checkResponse->assertJson([
            'process' => false,
            'data' => [
                'used_count' => 1,
                'used_forms' => [
                    [
                        'id' => $form->id,
                        'code' => 'UNIQUE-VOUCHER-XYZ',
                        'code_id' => $code->id,
                        'status' => 'used',
                        'assigned_code' => [
                            'code' => 'UNIQUE-VOUCHER-XYZ',
                            'product_code' => 'TC1000S',
                            'value' => 1000,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
