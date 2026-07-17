<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/')->assertOk()->assertSee('TOGA');
        $this->get('/drivers')->assertNotFound();
        $this->get('/gallery')->assertOk();
        $this->get('/news')->assertOk()->assertSee('Sim Racing on a Budget');
    }
}
