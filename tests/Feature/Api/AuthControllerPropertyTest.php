<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Property-based tests for AuthController.
 *
 * Feature: laravel-app-documentation
 *
 * Simulates property-based testing using PHPUnit loops over randomly generated inputs.
 * Each property runs a minimum of 100 iterations to verify universal behavior.
 */
class AuthControllerPropertyTest extends TestCase
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
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Generate a random valid name (2–40 chars, letters + spaces).
     */
    private function randomName(): string
    {
        $words = [];
        $wordCount = rand(1, 3);
        for ($i = 0; $i < $wordCount; $i++) {
            $len = rand(3, 10);
            $word = '';
            for ($j = 0; $j < $len; $j++) {
                $word .= chr(rand(ord('a'), ord('z')));
            }
            $words[] = ucfirst($word);
        }
        return implode(' ', $words);
    }

    /**
     * Generate a unique valid email address.
     */
    private function uniqueEmail(int $iteration): string
    {
        $local = 'user_' . $iteration . '_' . uniqid();
        $domains = ['example.com', 'test.org', 'mail.net', 'demo.io'];
        $domain = $domains[$iteration % count($domains)];
        return strtolower($local) . '@' . $domain;
    }

    /**
     * Generate a valid password (min 8 chars, satisfies Password::default()).
     */
    private function randomPassword(): string
    {
        // Password::default() in Laravel requires min 8 chars by default.
        // We generate a sufficiently complex password to satisfy any default rules.
        $base = 'Pass' . rand(1000, 9999) . '!aB';
        return $base;
    }

    /**
     * Generate a random email that does NOT exist in the database.
     */
    private function nonExistentEmail(int $iteration): string
    {
        return 'ghost_' . $iteration . '_' . uniqid() . '@nonexistent-domain-xyz.com';
    }

    /**
     * Generate a random password string (not necessarily matching any user).
     */
    private function randomWrongPassword(): string
    {
        return 'wrong_' . rand(100000, 999999) . '_pass';
    }

    // -------------------------------------------------------------------------
    // Property 4: Đăng ký trả về user và token
    // -------------------------------------------------------------------------

    /**
     * Property 4: Đăng ký trả về user và token
     *
     * For any valid registration input (name, email, password), calling
     * POST /api/register SHALL return a response containing `user` object
     * and `token` string.
     *
     * Feature: laravel-app-documentation, Property 4: Đăng ký trả về user và token
     * Validates: Requirements 1.5
     */
    public function test_property_4_register_returns_user_and_token(): void
    {
        $iterations = 100;
        $failures = [];

        for ($i = 0; $i < $iterations; $i++) {
            $name = $this->randomName();
            $email = $this->uniqueEmail($i);
            $password = $this->randomPassword();

            $response = $this->postJson('/api/register', [
                'name'                  => $name,
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $password,
                'terms'                 => true,
            ]);

            $status = $response->getStatusCode();
            $json   = $response->json();

            $hasUser  = isset($json['user']) && is_array($json['user']);
            $hasToken = isset($json['token']) && is_string($json['token']) && strlen($json['token']) > 0;

            if ($status !== 200 || ! $hasUser || ! $hasToken) {
                $failures[] = [
                    'iteration' => $i,
                    'input'     => compact('name', 'email', 'password'),
                    'status'    => $status,
                    'response'  => $json,
                ];
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 4 failed on %d/%d iterations. First failure: %s",
                count($failures),
                $iterations,
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }

    // -------------------------------------------------------------------------
    // Property 5: Đăng nhập với credentials sai trả về 401
    // -------------------------------------------------------------------------

    /**
     * Property 5: Đăng nhập với credentials sai trả về 401
     *
     * For any email/password combination that does not match a user in the
     * system, calling POST /api/login SHALL return HTTP 401.
     *
     * Feature: laravel-app-documentation, Property 5: Đăng nhập với credentials sai trả về 401
     * Validates: Requirements 2.2
     */
    public function test_property_5_login_with_wrong_credentials_returns_401(): void
    {
        $iterations = 100;
        $failures = [];

        // Strategy A (50 iterations): completely non-existent email + random password
        for ($i = 0; $i < 50; $i++) {
            $email    = $this->nonExistentEmail($i);
            $password = $this->randomWrongPassword();

            $response = $this->postJson('/api/login', [
                'email'    => $email,
                'password' => $password,
            ]);

            if ($response->getStatusCode() !== 401) {
                $failures[] = [
                    'strategy'  => 'non_existent_email',
                    'iteration' => $i,
                    'input'     => compact('email', 'password'),
                    'status'    => $response->getStatusCode(),
                    'response'  => $response->json(),
                ];
            }
        }

        // Strategy B (50 iterations): existing user email + wrong password
        // Create a set of real users, then attempt login with wrong passwords.
        $existingUsers = [];
        for ($i = 0; $i < 10; $i++) {
            $existingUsers[] = User::factory()->create([
                'email'    => $this->uniqueEmail(1000 + $i),
                'password' => Hash::make($this->randomPassword()),
            ]);
        }

        for ($i = 0; $i < 50; $i++) {
            $user     = $existingUsers[$i % count($existingUsers)];
            $password = $this->randomWrongPassword(); // always wrong

            $response = $this->postJson('/api/login', [
                'email'    => $user->email,
                'password' => $password,
            ]);

            if ($response->getStatusCode() !== 401) {
                $failures[] = [
                    'strategy'  => 'existing_user_wrong_password',
                    'iteration' => $i,
                    'input'     => ['email' => $user->email, 'password' => $password],
                    'status'    => $response->getStatusCode(),
                    'response'  => $response->json(),
                ];
            }
        }

        $this->assertEmpty(
            $failures,
            sprintf(
                "Property 5 failed on %d/%d iterations. First failure: %s",
                count($failures),
                $iterations,
                json_encode($failures[0] ?? null, JSON_PRETTY_PRINT)
            )
        );
    }
}
