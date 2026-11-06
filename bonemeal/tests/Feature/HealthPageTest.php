<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthPageTest extends TestCase
{
    public function test_health_page_renders_an_inertia_page(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->component('Health'));
    }
}
