<?php

namespace Tests\Unit\Models;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for Base model helpers.
 *
 * Tests Base::displayStatus() HTML output for each status value.
 *
 * Note: SendNotification::GetColorFromString() does not exist in the codebase,
 * so those tests are omitted.
 *
 * Validates: Requirements 7.1, 9.1
 */
class BaseModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * status 0 → HTML with 'btn-danger' class and 'private' text
     */
    public function test_display_status_0_returns_private_badge(): void
    {
        $post = new Post(['status' => 0]);

        $html = $post->display_status;

        $this->assertStringContainsString('btn-danger', $html);
        $this->assertStringContainsString('private', $html);
    }

    /**
     * status 1 → HTML with 'btn-success' class and 'public' text
     */
    public function test_display_status_1_returns_public_badge(): void
    {
        $post = new Post(['status' => 1]);

        $html = $post->display_status;

        $this->assertStringContainsString('btn-success', $html);
        $this->assertStringContainsString('public', $html);
    }

    /**
     * status 2 → HTML with 'btn-warning' class and 'internal' text
     */
    public function test_display_status_2_returns_internal_badge(): void
    {
        $post = new Post(['status' => 2]);

        $html = $post->display_status;

        $this->assertStringContainsString('btn-warning', $html);
        $this->assertStringContainsString('internal', $html);
    }

    /**
     * unknown status (e.g. 99) → HTML with 'btn-dark' class and 'unknow' text
     */
    public function test_display_status_unknown_returns_dark_badge(): void
    {
        $post = new Post(['status' => 99]);

        $html = $post->display_status;

        $this->assertStringContainsString('btn-dark', $html);
        $this->assertStringContainsString('unknow', $html);
    }

    /**
     * displayStatus() always returns a <label> HTML element
     */
    public function test_display_status_returns_label_element(): void
    {
        foreach ([0, 1, 2, 99] as $status) {
            $post = new Post(['status' => $status]);
            $html = $post->display_status;

            $this->assertStringContainsString('<label', $html);
            $this->assertStringContainsString('</label>', $html);
            $this->assertStringContainsString('btn-tag', $html);
        }
    }
}
