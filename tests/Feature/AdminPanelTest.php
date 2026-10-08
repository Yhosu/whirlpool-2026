<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@dismac.com.bo',
        ]);
    }

    public function test_root_route_redirects_to_admin(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/admin');
    }

    public function test_guest_accessing_admin_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_admin_can_access_forms_resource(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/forms');
        $response->assertSuccessful();
    }

    public function test_authenticated_admin_can_access_sales_resource(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/sales');
        $response->assertSuccessful();
    }

    public function test_authenticated_admin_can_access_stock_matrix_page(): void
    {
        $branch = Branch::create([
            'name' => 'Central Dismac',
            'city' => 'Santa Cruz',
            'channel' => 'ECOMMERCE',
        ]);
        $prize = Prize::create([
            'name' => 'Premio 500 Bs',
            'amount' => 500,
            'active' => 1,
        ]);
        PrizeBranch::create([
            'branch_id' => $branch->id,
            'prize_id' => $prize->id,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/stock-matrix');
        $response->assertSuccessful();
        $response->assertSee('Matriz de Inventario Real-Time');
    }

    public function test_authenticated_admin_can_access_prize_stock_history_resource(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/prize-stock-histories');
        $response->assertSuccessful();
    }

    public function test_validar_action_changes_status_from_pending_to_registered(): void
    {
        $form = \App\Models\Form::create([
            'name' => 'Juan Perez',
            'ci_number' => '1234567',
            'cellphone' => '70012345',
            'city' => 'Santa Cruz',
            'code' => 'TEST-VALIDATE-01',
            'type' => 'informal-market',
            'status' => 'pending',
            'business_unit' => 'RET',
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->assertTableActionVisible('validar', $form)
            ->callTableAction('validar', $form);

        $this->assertEquals('registered', $form->fresh()->status);
        $this->assertEquals($this->admin->id, $form->fresh()->user_id);
    }

    public function test_validar_action_is_hidden_for_non_pending_forms(): void
    {
        $registeredForm = \App\Models\Form::create([
            'name' => 'Maria Lopez',
            'ci_number' => '7654321',
            'cellphone' => '70054321',
            'city' => 'Cochabamba',
            'code' => 'TEST-REGISTERED-01',
            'type' => 'dismac',
            'status' => 'registered',
            'business_unit' => 'ECO',
        ]);

        $usedForm = \App\Models\Form::create([
            'name' => 'Carlos Gomez',
            'ci_number' => '8888888',
            'cellphone' => '70088888',
            'city' => 'La Paz',
            'code' => 'TEST-USED-01',
            'type' => 'dismac',
            'status' => 'used',
            'business_unit' => 'WEB',
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->assertTableActionHidden('validar', $registeredForm)
            ->assertTableActionHidden('validar', $usedForm);
    }

    public function test_anular_used_form_restores_prize_stock(): void
    {
        $branch = Branch::create([
            'name' => 'Central Dismac',
            'city' => 'Santa Cruz',
            'channel' => 'ECOMMERCE',
        ]);

        $prize = Prize::create([
            'name' => 'Premio 500 Bs',
            'amount' => 500,
            'active' => 1,
        ]);

        $prizeBranch = PrizeBranch::create([
            'branch_id' => $branch->id,
            'prize_id' => $prize->id,
            'stock' => 5,
        ]);

        $usedForm = \App\Models\Form::create([
            'name' => 'Pedro Anulacion',
            'ci_number' => '9988776',
            'cellphone' => '70099887',
            'city' => 'Santa Cruz',
            'code' => 'TEST-ANULAR-01',
            'type' => 'dismac',
            'status' => 'used',
            'branch_id' => $branch->id,
            'prize_id' => $prize->id,
            'business_unit' => 'ECO',
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->callTableAction('anular', $usedForm, data: [
                'invalidation_reason' => 'Prueba de anulación y reposición de stock',
            ]);

        $this->assertEquals('invalidated', $usedForm->fresh()->status);
        $this->assertEquals(6, $prizeBranch->fresh()->stock);

        $this->assertDatabaseHas('prize_stock_history', [
            'form_id' => $usedForm->id,
            'prize_id' => $prize->id,
            'branch_id' => $branch->id,
            'type' => 'addition',
            'quantity' => 1,
        ]);
    }
}

