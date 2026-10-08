<?php

namespace Tests\Feature;

use App\Models\MundialCoupon;
use App\Models\Participant;
use App\Models\Sale;
use App\Repositories\ParticipantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ParticipantServiceTest extends TestCase
{
    use RefreshDatabase;

    private ParticipantService $service;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();
        $this->service = $this->app->make(ParticipantService::class);
    }

    public function test_get_json_participants_returns_correct_structure_and_filters(): void
    {
        // Clear tables inside transaction for pristine test state
        \Schema::disableForeignKeyConstraints();
        \DB::table('mundial_coupons')->delete();
        \DB::table('sales')->delete();
        \DB::table('participants')->delete();
        \Schema::enableForeignKeyConstraints();

        // 1. Valid Active Participant with Active Sale and Coupon
        $participant1 = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'John Doe',
            'cellphone' => '12345678',
            'ci_number' => '87654321',
            'city' => 'Santa Cruz',
            'status' => 'registered',
        ]);

        $sale1 = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant1->id,
            'name' => $participant1->name,
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1500,
        ]);

        $coupon1 = new MundialCoupon([
            'participant_id' => $participant1->id,
            'sale_id' => $sale1->id,
            'code' => 'TICKET100',
            'status' => 'created',
            'active' => 1,
            'category' => 'legacy',
        ]);
        $coupon1->id = (string) \Str::uuid();
        $coupon1->save();

        // 2. Participant with inactive coupon (should be excluded)
        $participant2 = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Jane Inactive Coupon',
            'cellphone' => '87654321',
            'ci_number' => '12345678',
            'city' => 'La Paz',
            'status' => 'registered',
        ]);

        $sale2 = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant2->id,
            'name' => $participant2->name,
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 1000,
        ]);

        $coupon2 = new MundialCoupon([
            'participant_id' => $participant2->id,
            'sale_id' => $sale2->id,
            'code' => 'TICKET200',
            'status' => 'created',
            'active' => 0, // Inactive coupon
            'category' => 'legacy',
        ]);
        $coupon2->id = (string) \Str::uuid();
        $coupon2->save();

        // 3. Participant with inactive sale (should be excluded)
        $participant3 = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Bob Inactive Sale',
            'cellphone' => '11223344',
            'ci_number' => '44332211',
            'city' => 'Cochabamba',
            'status' => 'registered',
        ]);

        $sale3 = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participant3->id,
            'name' => $participant3->name,
            'active' => 0, // Inactive sale
            'sale_type' => 'CONTADO',
            'total_amount' => 800,
        ]);

        $coupon3 = new MundialCoupon([
            'participant_id' => $participant3->id,
            'sale_id' => $sale3->id,
            'code' => 'TICKET300',
            'status' => 'created',
            'active' => 1,
            'category' => 'legacy',
        ]);
        $coupon3->id = (string) \Str::uuid();
        $coupon3->save();

        // 4. Participant with a winner (exchanged) coupon (should be excluded completely)
        $participantWinner = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Alice Winner',
            'cellphone' => '55667788',
            'ci_number' => '88776655',
            'city' => 'Tarija',
            'status' => 'registered',
        ]);

        $saleWinner = Sale::create([
            'id' => (string) \Str::uuid(),
            'participant_id' => $participantWinner->id,
            'name' => $participantWinner->name,
            'active' => 1,
            'sale_type' => 'CONTADO',
            'total_amount' => 2000,
        ]);

        // Winner coupon
        $couponWinner = new MundialCoupon([
            'participant_id' => $participantWinner->id,
            'sale_id' => $saleWinner->id,
            'code' => 'TICKET_WIN',
            'status' => 'exchanged', // Exchanged = Winner coupon
            'active' => 1,
            'category' => 'legacy',
        ]);
        $couponWinner->id = (string) \Str::uuid();
        $couponWinner->save();

        // Alice also has a created coupon, which should still be excluded because Alice has a winner coupon
        $couponWinnerNonExchanged = new MundialCoupon([
            'participant_id' => $participantWinner->id,
            'sale_id' => $saleWinner->id,
            'code' => 'TICKET_CREATED',
            'status' => 'created',
            'active' => 1,
            'category' => 'legacy',
        ]);
        $couponWinnerNonExchanged->id = (string) \Str::uuid();
        $couponWinnerNonExchanged->save();

        // 5. Manual Coupon without sale (should be INCLUDED)
        $participantManual = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Manual User',
            'cellphone' => '99999999',
            'ci_number' => '99999999',
            'city' => 'Montero',
            'status' => 'registered',
        ]);

        $couponManual = new MundialCoupon([
            'participant_id' => $participantManual->id,
            'sale_id' => null,
            'code' => 'MANUAL100',
            'status' => 'created',
            'active' => 1,
            'category' => 'manual',
        ]);
        $couponManual->id = (string) \Str::uuid();
        $couponManual->save();

        // 6. Legacy Coupon without sale (should be EXCLUDED)
        $participantLegacyNoSale = Participant::create([
            'id' => (string) \Str::uuid(),
            'name' => 'Legacy No Sale User',
            'cellphone' => '88888888',
            'ci_number' => '88888888',
            'city' => 'El Alto',
            'status' => 'registered',
        ]);

        $couponLegacyNoSale = new MundialCoupon([
            'participant_id' => $participantLegacyNoSale->id,
            'sale_id' => null,
            'code' => 'LEGACY100',
            'status' => 'created',
            'active' => 1,
            'category' => 'legacy',
        ]);
        $couponLegacyNoSale->id = (string) \Str::uuid();
        $couponLegacyNoSale->save();

        Cache::store('file')->forget('json_participants');
        Cache::forget('json_participants');

        // Call the service method
        $response = $this->service->getJsonParticipants();

        // Assert response structure and content
        $this->assertArrayHasKey('count', $response);
        $this->assertArrayHasKey('result', $response);

        // Expected count is 2 (coupon1 and couponManual)
        $this->assertEquals(2, $response['count']);
        $this->assertCount(2, $response['result']);

        $identifiers = collect($response['result'])->pluck('identifier')->toArray();
        $this->assertContains($coupon1->id, $identifiers);
        $this->assertContains($couponManual->id, $identifiers);
        $this->assertNotContains($couponLegacyNoSale->id, $identifiers);
    }
}
