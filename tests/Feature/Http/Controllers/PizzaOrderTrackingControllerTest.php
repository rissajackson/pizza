<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PizzaOrderTrackingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the pizza orders index route returns the correct data.
     *
     * @return void
     */
    public function test_it_returns_pizza_orders_on_index_route()
    {
        // Create a user
        $user = User::factory()->create();

        // Create some test PizzaOrder data
        PizzaOrder::factory()->count(3)->create();

        // Act as the user
        $this->actingAs($user);

        // Send a GET request to the /pizza-orders route
        $response = $this->get('/pizza-orders');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the correct Inertia data is returned
        $response->assertInertia(fn ($page) =>
        $page
            ->component('PizzaOrderDashboard') // Ensure the correct Inertia component is rendered
            ->has('orders', 3) // Verify there are exactly 3 orders
            ->where('auth.user.id', $user->id) // Ensure the authenticated user is passed correctly
        );
    }
}

