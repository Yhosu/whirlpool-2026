<?php

namespace Tests\Feature;

use App\Filament\Resources\MinicuotasAlDiaResource;
use App\Models\MundialCoupon;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MinicuotasAlDiaResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_minicuotas_al_dia_resource_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(MinicuotasAlDiaResource::getUrl('index'));

        $response->assertSuccessful();
    }

    public function test_subadmin_can_access_minicuotas_al_dia_resource_page(): void
    {
        $subadmin = User::factory()->create(['role' => 'subadmin']);

        $response = $this->actingAs($subadmin)->get(MinicuotasAlDiaResource::getUrl('index'));

        $response->assertSuccessful();
    }

    public function test_regular_user_cannot_access_minicuotas_al_dia_resource_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(MinicuotasAlDiaResource::getUrl('index'));

        $response->assertForbidden();
    }

    public function test_resource_only_lists_manual_coupons(): void
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

        // Create manual coupon
        $manualCoupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'code' => 'MANUAL123',
            'status' => 'created',
            'active' => 1,
            'category' => 'manual',
        ]);
        $manualCoupon->id = (string) \Str::uuid();
        $manualCoupon->save();

        // Create legacy coupon
        $legacyCoupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'code' => 'LEGACY456',
            'status' => 'created',
            'active' => 1,
            'category' => 'legacy',
        ]);
        $legacyCoupon->id = (string) \Str::uuid();
        $legacyCoupon->save();

        // Test Livewire table displays only manual coupon
        Livewire::actingAs($admin)
            ->test(MinicuotasAlDiaResource\Pages\ListMinicuotasAlDias::class)
            ->assertCanSeeTableRecords([$manualCoupon])
            ->assertCanNotSeeTableRecords([$legacyCoupon]);
    }

    public function test_can_search_by_code_name_ci_and_phone(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $participant1 = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John Doe Searchable',
            'cellphone' => '78945612',
            'ci_number' => '12345678',
            'status' => 'registered',
        ]);

        $coupon1 = new MundialCoupon([
            'participant_id' => $participant1->id,
            'code' => 'COUPONXYZ',
            'status' => 'created',
            'active' => 1,
            'category' => 'manual',
        ]);
        $coupon1->id = (string) \Str::uuid();
        $coupon1->save();

        $participant2 = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Jane Smith Different',
            'cellphone' => '11111111',
            'ci_number' => '99999999',
            'status' => 'registered',
        ]);

        $coupon2 = new MundialCoupon([
            'participant_id' => $participant2->id,
            'code' => 'COUPONABC',
            'status' => 'created',
            'active' => 1,
            'category' => 'manual',
        ]);
        $coupon2->id = (string) \Str::uuid();
        $coupon2->save();

        // Search by code
        Livewire::actingAs($admin)
            ->test(MinicuotasAlDiaResource\Pages\ListMinicuotasAlDias::class)
            ->searchTable('COUPONXYZ')
            ->assertCanSeeTableRecords([$coupon1])
            ->assertCanNotSeeTableRecords([$coupon2]);

        // Search by name
        Livewire::actingAs($admin)
            ->test(MinicuotasAlDiaResource\Pages\ListMinicuotasAlDias::class)
            ->searchTable('John Doe Searchable')
            ->assertCanSeeTableRecords([$coupon1])
            ->assertCanNotSeeTableRecords([$coupon2]);

        // Search by CI
        Livewire::actingAs($admin)
            ->test(MinicuotasAlDiaResource\Pages\ListMinicuotasAlDias::class)
            ->searchTable('12345678')
            ->assertCanSeeTableRecords([$coupon1])
            ->assertCanNotSeeTableRecords([$coupon2]);

        // Search by cellphone
        Livewire::actingAs($admin)
            ->test(MinicuotasAlDiaResource\Pages\ListMinicuotasAlDias::class)
            ->searchTable('78945612')
            ->assertCanSeeTableRecords([$coupon1])
            ->assertCanNotSeeTableRecords([$coupon2]);
    }

    public function test_can_search_by_name_with_multiple_spaces(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $participant = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'FREDDY  CABRERA CEREZO',
            'cellphone' => '78945612',
            'ci_number' => '12345678',
            'status' => 'registered',
        ]);

        $coupon = new MundialCoupon([
            'participant_id' => $participant->id,
            'code' => 'COUPONXYZ',
            'status' => 'created',
            'active' => 1,
            'category' => 'manual',
        ]);
        $coupon->id = (string) \Str::uuid();
        $coupon->save();

        // Search with single space should match double space
        Livewire::actingAs($admin)
            ->test(MinicuotasAlDiaResource\Pages\ListMinicuotasAlDias::class)
            ->searchTable('FREDDY CABRERA CEREZO')
            ->assertCanSeeTableRecords([$coupon]);
    }
}
