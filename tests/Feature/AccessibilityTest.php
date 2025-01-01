<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibilityTest extends TestCase
{
    /**
     * A basic test accessibility.
     *
     * @return void
     */
    public function test_accessibility()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
