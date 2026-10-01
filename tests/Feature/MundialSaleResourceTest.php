<?php

namespace Tests\Feature;

use App\Filament\Resources\MundialSaleResource;
use App\Models\MundialCoupon;
use App\Models\Participant;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class MundialSaleResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_mundial_sale_resource_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(MundialSaleResource::getUrl('index'));

        $response->assertSuccessful();
    }

    public function test_subadmin_can_access_mundial_sale_resource_page(): void
    {
        $subadmin = User::factory()->create(['role' => 'subadmin']);

        $response = $this->actingAs($subadmin)->get(MundialSaleResource::getUrl('index'));

        $response->assertSuccessful();
    }

    public function test_regular_user_cannot_access_mundial_sale_resource_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(MundialSaleResource::getUrl('index'));

        $response->assertForbidden();
    }

    public function test_resource_only_lists_sales_with_mundial_coupons(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a participant
        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Test Participant',
            'cellphone' => '123456',
            'ci_number' => '654321',
            'status' => 'registered',
        ]);

        // Create a sale with mundial coupon
        $saleWithCoupon = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale With Coupon',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
        ]);

        $coupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $saleWithCoupon->id,
            'code' => 'MUNDIAL1',
            'status' => 'created',
            'active' => 1,
        ]);
        $coupon->id = (string) \Str::uuid();
        $coupon->save();

        // Create a sale without mundial coupon
        $saleWithoutCoupon = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale Without Coupon',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 500,
        ]);

        // Test Livewire table displays only the one with coupon
        Livewire::actingAs($admin)
            ->test(MundialSaleResource\Pages\ListMundialSales::class)
            ->assertCanSeeTableRecords([$saleWithCoupon])
            ->assertCanNotSeeTableRecords([$saleWithoutCoupon]);
    }

    public function test_admin_can_download_excel_via_page_action(): void
    {
        Carbon::setTestNow('2026-06-13 12:00:00');
        $admin = User::factory()->create(['role' => 'admin']);

        // Create participant
        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John Exportable',
            'cellphone' => '1234567',
            'ci_number' => '7654321',
            'status' => 'registered',
        ]);

        // Create a sale with mundial coupon
        $sale = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale For Export',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1234,
            'business_unit' => 'ECO',
        ]);

        $coupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $sale->id,
            'code' => 'EXP-COUPON',
            'status' => 'created',
            'active' => 1,
        ]);
        $coupon->id = (string) \Str::uuid();
        $coupon->save();

        Livewire::actingAs($admin)
            ->test(MundialSaleResource\Pages\ListMundialSales::class)
            ->callAction('export')
            ->assertFileDownloaded('ventas_mundial_20260613_120000.xlsx');

        Carbon::setTestNow();
    }

    public function test_excel_download_applies_filters(): void
    {
        Carbon::setTestNow('2026-06-13 12:00:00');
        $admin = User::factory()->create(['role' => 'admin']);

        // Create participant
        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John Filtered Export',
            'cellphone' => '1234567',
            'ci_number' => '7654321',
            'status' => 'registered',
        ]);

        // Create sale ECO
        $saleEco = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale ECO',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
            'business_unit' => 'ECO',
        ]);

        $coupon1 = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $saleEco->id,
            'code' => 'COUPON-ECO',
            'status' => 'created',
            'active' => 1,
        ]);
        $coupon1->id = (string) \Str::uuid();
        $coupon1->save();

        // Create sale RET
        $saleRet = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale RET',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 2000,
            'business_unit' => 'RET',
        ]);

        $coupon2 = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $saleRet->id,
            'code' => 'COUPON-RET',
            'status' => 'created',
            'active' => 1,
        ]);
        $coupon2->id = (string) \Str::uuid();
        $coupon2->save();

        // Download excel with filter 'business_unit' = 'ECO'
        Livewire::actingAs($admin)
            ->test(MundialSaleResource\Pages\ListMundialSales::class)
            ->filterTable('business_unit', 'ECO')
            ->callAction('export')
            ->assertFileDownloaded('ventas_mundial_20260613_120000.xlsx');

        Carbon::setTestNow();
    }

    public function test_opening_page_with_legacy_edit_query_parameter_redirects(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John Test',
            'cellphone' => '123456',
            'ci_number' => '123456',
            'status' => 'registered',
        ]);
        $sale = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale Test',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
        ]);
        $coupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $sale->id,
            'code' => 'COUPON1',
            'status' => 'created',
            'active' => 1,
        ]);
        $coupon->id = (string) \Str::uuid();
        $coupon->save();

        $response = $this->actingAs($admin)
            ->get(MundialSaleResource::getUrl('index', [
                'edit_record' => $sale->id,
            ]));

        $response->assertRedirect(MundialSaleResource::getUrl('index', [
            'tableAction' => 'edit',
            'tableActionRecord' => $sale->id,
        ]));
    }

    public function test_opening_page_with_native_table_action_parameters_mounts_edit_action(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John Test',
            'cellphone' => '123456',
            'ci_number' => '123456',
            'status' => 'registered',
        ]);
        $sale = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale Test',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
        ]);
        $coupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $sale->id,
            'code' => 'COUPON1',
            'status' => 'created',
            'active' => 1,
        ]);
        $coupon->id = (string) \Str::uuid();
        $coupon->save();

        $response = $this->actingAs($admin)
            ->get(MundialSaleResource::getUrl('index', [
                'tableAction' => 'edit',
                'tableActionRecord' => $sale->id,
            ]));

        $response->assertSuccessful();

        Livewire::actingAs($admin)
            ->withQueryParams([
                'tableAction' => 'edit',
                'tableActionRecord' => $sale->id,
            ])
            ->test(MundialSaleResource\Pages\ListMundialSales::class)
            ->assertSet('defaultTableAction', 'edit')
            ->assertSet('defaultTableActionRecord', $sale->id);
    }

    public function test_date_range_filter_applies_correctly_on_created_at(): void
    {
        Carbon::setTestNow('2026-06-13 12:00:00');
        $admin = User::factory()->create(['role' => 'admin']);
        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John DateTest',
            'cellphone' => '123456',
            'ci_number' => '123456',
            'status' => 'registered',
        ]);

        $saleToday = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale Today',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
        ]);
        $couponToday = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $saleToday->id,
            'code' => 'COUPON_TODAY',
            'status' => 'created',
            'active' => 1,
        ]);
        $couponToday->id = (string) \Str::uuid();
        $couponToday->save();

        Carbon::setTestNow('2026-06-12 12:00:00');
        $saleYesterday = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant->id,
            'name' => 'Sale Yesterday',
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
        ]);
        $couponYesterday = new MundialCoupon([
            'participant_id' => $participant->id,
            'sale_id' => $saleYesterday->id,
            'code' => 'COUPON_YESTERDAY',
            'status' => 'created',
            'active' => 1,
        ]);
        $couponYesterday->id = (string) \Str::uuid();
        $couponYesterday->save();

        Carbon::setTestNow('2026-06-13 12:00:00');

        Livewire::actingAs($admin)
            ->test(MundialSaleResource\Pages\ListMundialSales::class)
            ->filterTable('created_at', [
                'created_from' => '2026-06-13',
                'created_until' => '2026-06-13',
            ])
            ->assertCanSeeTableRecords([$saleToday])
            ->assertCanNotSeeTableRecords([$saleYesterday]);

        Carbon::setTestNow();
    }
}
