<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-based tests for BaseController (API layer).
 *
 * Feature: laravel-app-documentation
 *
 * Simulates property-based testing using PHPUnit loops over randomly generated inputs.
 * Each property runs a minimum of 100 iterations to verify universal behavior.
 *
 * Note: Tests use SQLite in-memory database. Property 3 uses Product (no HasFullTextSearch)
 * to avoid MATCH...AGAINST syntax which is unsupported in SQLite.
 */
class BaseControllerPropertyTest extends TestCase
{
    use RefreshDatabase;

    /** @var User */
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        if (! config('fortify.user.enable', false)) {
            $this->markTestSkipped('Fortify user authentication is disabled');
        }

        // Create an authenticated user for all API calls (auth:sanctum required)
        $this->user = User::factory()->create();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Make an authenticated JSON GET request.
     */
    private function apiGet(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user)->getJson($uri);
    }

    /**
     * Make an authenticated JSON POST request.
     */
    private function apiPost(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user)->postJson($uri, $data);
    }

    /**
     * Make an authenticated JSON PUT request.
     */
    private function apiPut(string $uri, array $data = []): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user)->putJson($uri, $data);
    }

    /**
     * Make an authenticated JSON DELETE request.
     */
    private function apiDelete(string $uri): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($this->user)->deleteJson($uri);
    }

    /**
     * Create a Post with the given status.
     * Posts require author_id, title, and html fields.
     */
    private function createPost(int $status, array $overrides = []): Post
    {
        return Post::create(array_merge([
            'author_id'   => $this->user->id,
            'title'       => 'Test Post ' . uniqid(),
            'html'        => '<p>Content</p>',
            'description' => 'Description',
            'categories'  => ['tech', 'news'],
            'tags'        => ['php', 'laravel'],
            'status'      => $status,
        ], $overrides));
    }

    /**
     * Create a Product with the given status.
     */
    private function createProduct(int $status, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name'        => 'Product ' . uniqid(),
            'description' => 'Description',
            'categories'  => ['electronics'],
            'tags'        => ['sale'],
            'price'       => 10.00,
            'status'      => $status,
        ], $overrides));
    }

    /**
     * Create a Transaction with the given status.
     */
    private function createTransaction(int $status, array $overrides = []): Transaction
    {
        return Transaction::create(array_merge([
            'user_id'  => $this->user->id,
            'code'     => 'TXN-' . uniqid(),
            'data'     => json_encode(['note' => 'test']),
            'amount'   => 100.00,
            'tax'      => 5.00,
            'currency' => 'USD',
            'status'   => $status,
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Property 1: API chỉ trả về records có status = 1
    // -------------------------------------------------------------------------

    /**
     * Property 1: API chỉ trả về records có status = 1
     *
     * For any collection of records (Post, Product, Transaction) with mixed
     * status values, when calling the index endpoint, all records in the
     * response SHALL have status = 1.
     *
     * Feature: laravel-app-documentation, Property 1: API chỉ trả về records có status = 1
     * Validates: Requirements 7.5, 9.5, 10.6
     */
    public function test_property_1_index_only_returns_records_with_status_1(): void
    {
        $iterations = 100;
        $failures   = [];

        // Status values to mix: 0 (private), 1 (public), 2 (internal)
        $allStatuses = [0, 1, 2];

        // Resources to test: [endpoint, createFn]
        $resources = [
            'posts'        => fn(int $s) => $this->createPost($s),
            'products'     => fn(int $s) => $this->createProduct($s),
            'transactions' => fn(int $s) => $this->createTransaction($s),
        ];

        foreach ($resources as $endpoint => $createFn) {
            for ($i = 0; $i < $iterations; $i++) {
                // Each iteration: create a fresh set of records with mixed statuses
                // Pick a random mix: some status=0, some status=1, some status=2
                $countPerStatus = rand(1, 3);
                foreach ($allStatuses as $status) {
                    for ($k = 0; $k < $countPerStatus; $k++) {
                        $createFn($status);
                    }
                }

                $response = $this->apiGet("/api/{$endpoint}");
                $json     = $response->json();

                // The paginated response has a 'data' key
                $items = $json['data'] ?? [];

                foreach ($items as $item) {
                    if (($item['status'] ?? null) !== 1) {
                        $failures[] = [
                            'property'  => 'Property 1',
                            'endpoint'  => $endpoint,
                            'iteration' => $i,
                            'item_id'   => $item['id'] ?? null,
                            'status'    => $item['status'] ?? null,
                        ];
                        break; // one failure per iteration is enough
                    }
                }
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 1 failed on %d iteration(s). First failure: %s",
                count($failures),
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }

    // -------------------------------------------------------------------------
    // Property 2: API show trả về đúng record theo id
    // -------------------------------------------------------------------------

    /**
     * Property 2: API show trả về đúng record theo id
     *
     * For any record with status = 1, when calling show($id), the response
     * SHALL contain the record with the exact same id.
     *
     * Feature: laravel-app-documentation, Property 2: API show trả về đúng record theo id
     * Validates: Requirements 7.6, 9.6, 10.7
     */
    public function test_property_2_show_returns_correct_record_by_id(): void
    {
        $iterations = 100;
        $failures   = [];

        $resources = [
            'posts'        => fn() => $this->createPost(1),
            'products'     => fn() => $this->createProduct(1),
            'transactions' => fn() => $this->createTransaction(1),
        ];

        foreach ($resources as $endpoint => $createFn) {
            for ($i = 0; $i < $iterations; $i++) {
                $record   = $createFn();
                $response = $this->apiGet("/api/{$endpoint}/{$record->id}");

                $status = $response->getStatusCode();
                $json   = $response->json();

                $returnedId = $json['id'] ?? null;

                if ($status !== 200 || (int) $returnedId !== (int) $record->id) {
                    $failures[] = [
                        'property'     => 'Property 2',
                        'endpoint'     => $endpoint,
                        'iteration'    => $i,
                        'expected_id'  => $record->id,
                        'returned_id'  => $returnedId,
                        'http_status'  => $status,
                    ];
                }
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 2 failed on %d iteration(s). First failure: %s",
                count($failures),
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }

    // -------------------------------------------------------------------------
    // Property 3: Recommendations không chứa post gốc và có tối đa 3 kết quả
    // -------------------------------------------------------------------------

    /**
     * Property 3: Recommendations không chứa post gốc và có tối đa 3 kết quả
     *
     * For any Product with status = 1, when calling show($id)?recommendations=1,
     * the response SHALL contain at most 3 items and SHALL NOT contain the
     * item with id = $id.
     *
     * Note: Uses Product (not Post) because Post uses HasFullTextSearch which
     * relies on MySQL FULLTEXT MATCH...AGAINST syntax unsupported in SQLite.
     * Product's recommendations use the base query without full-text search.
     *
     * Feature: laravel-app-documentation, Property 3: Recommendations không chứa post gốc và có tối đa 3 kết quả
     * Validates: Requirements 7.8, 9.8
     */
    public function test_property_3_recommendations_exclude_original_and_max_3(): void
    {
        $iterations = 100;
        $failures   = [];

        for ($i = 0; $i < $iterations; $i++) {
            // Create a varying number of additional products (0–5) with status=1
            $extraCount = rand(0, 5);
            $extras     = [];
            for ($k = 0; $k < $extraCount; $k++) {
                $extras[] = $this->createProduct(1, [
                    'categories' => ['electronics', 'gadgets'],
                    'tags'       => ['sale', 'new'],
                ]);
            }

            // The original product
            $original = $this->createProduct(1, [
                'categories' => ['electronics'],
                'tags'       => ['sale'],
            ]);

            $response = $this->apiGet("/api/products/{$original->id}?recommendations=1");
            $status   = $response->getStatusCode();
            $items    = $response->json();

            if ($status !== 200) {
                $failures[] = [
                    'property'    => 'Property 3',
                    'iteration'   => $i,
                    'http_status' => $status,
                    'response'    => $items,
                ];
                continue;
            }

            // Must be an array (not paginated)
            if (! is_array($items)) {
                $failures[] = [
                    'property'  => 'Property 3',
                    'iteration' => $i,
                    'reason'    => 'Response is not an array',
                    'response'  => $items,
                ];
                continue;
            }

            $count = count($items);

            // Assert count <= 3
            if ($count > 3) {
                $failures[] = [
                    'property'    => 'Property 3',
                    'iteration'   => $i,
                    'reason'      => "count > 3 (got {$count})",
                    'original_id' => $original->id,
                ];
                continue;
            }

            // Assert original id is not in results
            $returnedIds = array_column($items, 'id');
            if (in_array($original->id, $returnedIds)) {
                $failures[] = [
                    'property'     => 'Property 3',
                    'iteration'    => $i,
                    'reason'       => 'Original id found in recommendations',
                    'original_id'  => $original->id,
                    'returned_ids' => $returnedIds,
                ];
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 3 failed on %d iteration(s). First failure: %s",
                count($failures),
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }

    // -------------------------------------------------------------------------
    // Property 7: store/update/destroy luôn trả về 403
    // -------------------------------------------------------------------------

    /**
     * Property 7: store/update/destroy luôn trả về 403
     *
     * For any resource (Post, Product, Transaction), when calling store,
     * update, or destroy actions, the response SHALL have HTTP status 403.
     *
     * Feature: laravel-app-documentation, Property 7: store/update/destroy luôn trả về 403
     * Validates: Requirements 13.4
     */
    public function test_property_7_store_update_destroy_always_return_403(): void
    {
        $iterations = 100;
        $failures   = [];

        $endpoints = ['posts', 'products', 'transactions'];
        $actions   = ['store', 'update', 'destroy'];

        for ($i = 0; $i < $iterations; $i++) {
            $endpoint = $endpoints[$i % count($endpoints)];
            $action   = $actions[$i % count($actions)];

            // Use a random id for update/destroy (doesn't matter — should 403 regardless)
            $fakeId = rand(1, 9999);

            switch ($action) {
                case 'store':
                    $response = $this->apiPost("/api/{$endpoint}", ['name' => 'test']);
                    break;
                case 'update':
                    $response = $this->apiPut("/api/{$endpoint}/{$fakeId}", ['name' => 'test']);
                    break;
                case 'destroy':
                    $response = $this->apiDelete("/api/{$endpoint}/{$fakeId}");
                    break;
            }

            $status = $response->getStatusCode();

            if ($status !== 403) {
                $failures[] = [
                    'property'    => 'Property 7',
                    'iteration'   => $i,
                    'endpoint'    => $endpoint,
                    'action'      => $action,
                    'http_status' => $status,
                    'response'    => $response->json(),
                ];
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 7 failed on %d iteration(s). First failure: %s",
                count($failures),
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }
}
