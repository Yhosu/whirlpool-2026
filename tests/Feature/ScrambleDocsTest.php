<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScrambleDocsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test that only specific API routes are enabled in Scramble documentation.
     */
    public function test_only_allowed_api_routes_are_in_scramble_docs(): void
    {
        $response = $this->getJson('/docs/api.json');

        $response->assertStatus(200);

        // Get the list of documented paths
        $paths = $response->json('paths');

        $this->assertNotNull($paths, 'OpenAPI paths should not be null');

        // These should be present
        $this->assertArrayHasKey('/register-participant', $paths);
        $this->assertArrayHasKey('/participant/{ciNumber}', $paths);

        // These should NOT be present
        $this->assertArrayNotHasKey('/get-prices', $paths);
        $this->assertArrayNotHasKey('/update-winner', $paths);
        $this->assertArrayNotHasKey('/get-json-participants', $paths);
        $this->assertArrayNotHasKey('/get-winners/{priceId}', $paths);
        $this->assertArrayNotHasKey('/get-winners/{price_id}', $paths);
        $this->assertArrayNotHasKey('/user', $paths);
    }
}
