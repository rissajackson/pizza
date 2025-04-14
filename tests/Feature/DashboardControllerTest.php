<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the dashboard route returns the correct data.
     *
     * @return void
     */
    public function test_dashboard_returns_orders()
    {
        // Create a user
        $user = User::factory()->create();

        // Create some test PizzaOrder data
        PizzaOrder::factory()->count(3)->create();

        // Act as the user
        $this->actingAs($user);

        // Send a GET request to the /dashboard route
        $response = $this->get('/dashboard');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the 'orders' data is in the response
        $response->assertInertia(fn ($page) =>
        $page
            ->component('Dashboard') // Check that the correct component is rendered
            ->has('orders', 3) // Ensure there are 3 orders
            ->has('auth.user') // Check that the authenticated user is passed
        );
    }
}
