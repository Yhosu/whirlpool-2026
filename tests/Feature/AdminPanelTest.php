<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Form;
use App\Models\Prize;
use App\Models\PrizeBranch;
use App\Models\Sale;
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
        $response->assertSee('Stock Inicial Total');
        $response->assertSee('Stock Disponible Actual');
        $response->assertSee('Premios Entregados / Reducidos');

        // Verify polling is disabled on panel database notifications
        $panel = \Filament\Facades\Filament::getPanel('admin');
        $this->assertNull($panel->getDatabaseNotificationsPollingInterval());
    }

    public function test_authenticated_admin_can_access_prize_stock_history_resource(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/prize-stock-histories');
        $response->assertSuccessful();
    }

    public function test_admin_panel_is_restricted_to_dark_mode_always(): void
    {
        $panel = \Filament\Facades\Filament::getPanel('admin');
        $this->assertTrue($panel->hasDarkMode());
        $this->assertTrue($panel->hasDarkModeForced());

        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertSuccessful();
        $response->assertSee("localStorage.setItem('theme', 'dark')", false);
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

    public function test_anular_dismac_form_liberates_assigned_code_and_restores_stock(): void
    {
        $branch = Branch::create([
            'name' => 'Central Dismac Nacional',
            'city' => 'Santa Cruz',
            'channel' => 'ECOMMERCE',
        ]);

        $prize = Prize::create([
            'name' => 'Premio 250 Bs',
            'amount' => 250,
            'active' => 1,
        ]);

        $prizeBranch = PrizeBranch::create([
            'branch_id' => $branch->id,
            'prize_id' => $prize->id,
            'stock' => 10,
        ]);

        $code = \App\Models\Code::create([
            'code' => 'COUPON-ANULAR-TEST',
            'value' => 250,
            'prize_id' => $prize->id,
            'status' => 'used',
            'is_used' => true,
            'used_at' => now(),
        ]);

        $dismacForm = \App\Models\Form::create([
            'name' => 'Cliente Dismac Test',
            'ci_number' => '11223344',
            'cellphone' => '71122334',
            'city' => 'Santa Cruz',
            'type' => 'dismac',
            'status' => 'used',
            'branch_id' => $branch->id,
            'prize_id' => $prize->id,
            'code_id' => $code->id,
            'business_unit' => 'RET',
        ]);

        $code->update(['form_id' => $dismacForm->id]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->callTableAction('anular', $dismacForm, data: [
                'invalidation_reason' => 'Anulación Dismac con liberación de cupón',
            ]);

        $this->assertEquals('invalidated', $dismacForm->fresh()->status);
        $this->assertEquals(11, $prizeBranch->fresh()->stock);

        $freshCode = $code->fresh();
        $this->assertFalse($freshCode->is_used);
        $this->assertEquals('available', $freshCode->status);
        $this->assertNull($freshCode->used_at);
        $this->assertNull($freshCode->form_id);

        $this->assertStringContainsString('COUPON-ANULAR-TEST', $dismacForm->fresh()->observations);
    }

    public function test_marcar_entregado_action_updates_dismac_used_form_to_delivered(): void
    {
        $code = \App\Models\Code::create([
            'code' => 'COUPON-DELIVERY-TEST',
            'value' => 500,
            'status' => 'used',
            'is_used' => true,
        ]);

        $dismacUsedForm = \App\Models\Form::create([
            'name' => 'Cliente Dismac Entregado',
            'ci_number' => '44332211',
            'cellphone' => '72233445',
            'city' => 'Santa Cruz',
            'type' => 'dismac',
            'status' => 'used',
            'code_id' => $code->id,
            'business_unit' => 'RET',
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->assertTableActionVisible('marcar_entregado', $dismacUsedForm)
            ->callTableAction('marcar_entregado', $dismacUsedForm, data: [
                'observations' => 'Cupón utilizado en compras Dismac Central',
            ]);

        $freshForm = $dismacUsedForm->fresh();
        $this->assertEquals('delivered', $freshForm->status);
        $this->assertEquals($this->admin->id, $freshForm->user_id);
        $this->assertStringContainsString('Cupón utilizado en compras Dismac Central', $freshForm->observations);

        // Test with empty/optional observations
        $dismacUsedForm2 = \App\Models\Form::create([
            'name' => 'Cliente Dismac Entregado 2',
            'ci_number' => '99881122',
            'cellphone' => '73344556',
            'city' => 'Cochabamba',
            'type' => 'dismac',
            'status' => 'used',
            'business_unit' => 'ECO',
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->callTableAction('marcar_entregado', $dismacUsedForm2);

        $this->assertEquals('delivered', $dismacUsedForm2->fresh()->status);
    }

    public function test_marcar_entregado_action_visibility_rules(): void
    {
        $dismacRegistered = \App\Models\Form::create([
            'name' => 'Dismac Registrado',
            'ci_number' => '555111',
            'cellphone' => '70011122',
            'city' => 'La Paz',
            'type' => 'dismac',
            'status' => 'registered',
        ]);

        $informalUsed = \App\Models\Form::create([
            'name' => 'Informal Usado',
            'ci_number' => '555222',
            'cellphone' => '70022233',
            'city' => 'Cochabamba',
            'type' => 'informal-market',
            'status' => 'used',
        ]);

        $dismacDelivered = \App\Models\Form::create([
            'name' => 'Dismac Ya Entregado',
            'ci_number' => '555333',
            'cellphone' => '70033344',
            'city' => 'Santa Cruz',
            'type' => 'dismac',
            'status' => 'delivered',
        ]);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->assertTableActionHidden('marcar_entregado', $dismacRegistered)
            ->assertTableActionHidden('marcar_entregado', $informalUsed)
            ->assertTableActionHidden('marcar_entregado', $dismacDelivered);
    }

    public function test_admin_can_edit_forms_and_sales(): void
    {
        $form = Form::create([
            'name' => 'Cliente Form Edit',
            'ci_number' => '12341234',
            'cellphone' => '70012345',
            'city' => 'Santa Cruz',
            'type' => 'informal-market',
            'status' => 'pending',
            'code' => 'FORM-EDIT-01',
        ]);

        $sale = Sale::create([
            'name' => 'Cliente Sale Edit',
            'control_code' => 'SALE-CTRL-01',
            'invoice_number' => 'FAC-01',
            'city_agency' => 'Santa Cruz',
            'business_unit' => 'RET',
            'sale_type' => 'CONTADO',
            'sale_date' => now()->toDateString(),
            'quantity_items' => 1,
            'total_amount' => 1500,
            'active' => 1,
        ]);

        $this->actingAs($this->admin);
        $this->assertTrue(\App\Filament\Resources\FormResource::canEdit($form));
        $this->assertTrue(\App\Filament\Resources\SaleResource::canEdit($sale));

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->assertTableActionVisible('edit', $form);

        \Livewire\Livewire::actingAs($this->admin)
            ->test(\App\Filament\Resources\SaleResource\Pages\ListSales::class)
            ->assertTableActionVisible('edit', $sale);
    }

    public function test_subadmin_has_access_to_everything_except_editing_in_forms_and_sales(): void
    {
        $subadmin = User::factory()->create([
            'role' => 'subadmin',
            'email' => 'subadmin@dismac.com.bo',
        ]);

        // Subadmin can access dashboard and other panel sections
        $this->actingAs($subadmin)->get('/admin')->assertSuccessful();
        $this->actingAs($subadmin)->get('/admin/forms')->assertSuccessful();
        $this->actingAs($subadmin)->get('/admin/sales')->assertSuccessful();
        $this->actingAs($subadmin)->get('/admin/stock-matrix')->assertSuccessful();
        $this->actingAs($subadmin)->get('/admin/prize-stock-histories')->assertSuccessful();

        $form = Form::create([
            'name' => 'Cliente Subadmin Form',
            'ci_number' => '9876543',
            'cellphone' => '70098765',
            'city' => 'Cochabamba',
            'type' => 'informal-market',
            'status' => 'pending',
            'code' => 'SUBADMIN-FORM-01',
        ]);

        $sale = Sale::create([
            'name' => 'Cliente Subadmin Sale',
            'control_code' => 'SUBADMIN-CTRL-01',
            'invoice_number' => 'FAC-SUBADMIN-01',
            'city_agency' => 'Cochabamba',
            'business_unit' => 'ECO',
            'sale_type' => 'CONTADO',
            'sale_date' => now()->toDateString(),
            'quantity_items' => 1,
            'total_amount' => 2000,
            'active' => 1,
        ]);

        // canEdit returns false for subadmin
        $this->actingAs($subadmin);
        $this->assertFalse(\App\Filament\Resources\FormResource::canEdit($form));
        $this->assertFalse(\App\Filament\Resources\SaleResource::canEdit($sale));

        // In tables: edit is hidden, view is visible
        \Livewire\Livewire::actingAs($subadmin)
            ->test(\App\Filament\Resources\FormResource\Pages\ManageForms::class)
            ->assertTableActionHidden('edit', $form)
            ->assertTableActionVisible('view', $form);

        \Livewire\Livewire::actingAs($subadmin)
            ->test(\App\Filament\Resources\SaleResource\Pages\ListSales::class)
            ->assertTableActionHidden('edit', $sale)
            ->assertTableActionVisible('view', $sale);
    }
}

