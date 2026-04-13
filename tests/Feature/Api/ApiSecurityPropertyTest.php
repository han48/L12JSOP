<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-based tests for API Security.
 *
 * Feature: laravel-app-documentation
 *
 * Simulates property-based testing using PHPUnit loops over protected endpoints.
 * Each property runs a minimum of 100 iterations to verify universal behavior.
 */
class ApiSecurityPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! config('fortify.user.enable', false)) {
            $this->markTestSkipped('Fortify user authentication is disabled');
        }
    }

    // -------------------------------------------------------------------------
    // Property 6: Protected endpoints yêu cầu token hợp lệ
    // -------------------------------------------------------------------------

    /**
     * Property 6: Protected endpoints yêu cầu token hợp lệ
     *
     * For any protected endpoint (/api/posts, /api/products, /api/transactions,
     * /api/notifications), when called without a Bearer token, the response
     * SHALL have HTTP status 401.
     *
     * Feature: laravel-app-documentation, Property 6: Protected endpoints yêu cầu token hợp lệ
     * Validates: Requirements 13.3
     */
    public function test_property_6_protected_endpoints_require_valid_token(): void
    {
        $protectedEndpoints = [
            ['method' => 'GET', 'uri' => '/api/posts'],
            ['method' => 'GET', 'uri' => '/api/posts/1'],
            ['method' => 'GET', 'uri' => '/api/products'],
            ['method' => 'GET', 'uri' => '/api/products/1'],
            ['method' => 'GET', 'uri' => '/api/transactions'],
            ['method' => 'GET', 'uri' => '/api/transactions/1'],
            ['method' => 'GET', 'uri' => '/api/notifications'],
            ['method' => 'GET', 'uri' => '/api/notifications/unread'],
        ];

        $endpointCount = count($protectedEndpoints);
        $iterations = 100;
        $failures = [];

        for ($i = 0; $i < $iterations; $i++) {
            $endpoint = $protectedEndpoints[$i % $endpointCount];

            $response = $this->json(
                $endpoint['method'],
                $endpoint['uri'],
                [],
                ['Accept' => 'application/json']
                // No Authorization header — intentionally unauthenticated
            );

            $status = $response->getStatusCode();

            if ($status !== 401) {
                $failures[] = [
                    'iteration' => $i,
                    'endpoint'  => $endpoint,
                    'status'    => $status,
                    'response'  => $response->json(),
                ];
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 6 failed on %d/%d iterations. First failure: %s",
                count($failures),
                $iterations,
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }
}
