<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PizzaOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function it_returns_all_pizza_orders()
    {
        // Arrange: Create multiple pizza order instances
        $pizzaOrders = PizzaOrder::factory()->count(3)->create();

        // Act: Make a GET request to the index route
        $response = $this->getJson(route('pizza-orders.index'));

        // Assert: Check response status and structure
        $response->assertOk();
        $response->assertJsonStructure([
            'pizzaOrders' => [
                '*' => ['id', 'status', 'created_at', 'updated_at'] // Define fields returned in JSON
            ]
        ]);

        // Assert: Check that all pizza orders are returned
        $this->assertCount(3, $response->json('pizzaOrders'));
    }

    public function it_returns_a_single_pizza_order()
    {
        // Arrange: Create a single pizza order
        $pizzaOrder = PizzaOrder::factory()->create();

        // Act: Make a GET request to the show route
        $response = $this->getJson(route('pizza-orders.show', $pizzaOrder->id));

        // Assert: Check response status and structure
        $response->assertOk();
        $response->assertJson([
            'pizzaOrder' => [
                'id' => $pizzaOrder->id,
                'status' => $pizzaOrder->status,
            ],
        ]);
    }

    public function it_returns_a_404_when_pizza_order_does_not_exist()
    {
        // Act: Make a GET request for a non-existent pizza order
        $response = $this->getJson(route('pizza-orders.show', 999));

        // Assert: Check response status
        $response->assertNotFound();
    }
}
