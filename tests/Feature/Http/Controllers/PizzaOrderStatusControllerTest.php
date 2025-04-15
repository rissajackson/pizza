<?php

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use Carbon\Carbon;

class PizzaOrderStatusControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Set up any common test preparations.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // You might want to create a user here if your controller uses authentication
        // $this->user = User::factory()->create();
    }

    /**
     * Test successful status update.
     */
    public function test_updates_status_successfully(): void
    {
        // Arrange: Create a pizza order with an initial status.
        $initialStatus = PizzaOrderStatus::WORKING;
        $newStatus = PizzaOrderStatus::IN_OVEN;
        $createdAt = now()->subMinutes(10); // Set created_at to 10 minutes ago
        $pizzaOrder = PizzaOrder::factory()->create([
            'status' => $initialStatus,
            'created_at' => $createdAt, // Set a specific created_at
        ]);

        // Act: Send a PATCH request to update the status.
        $response = $this->patchJson(
            route('pizza-order-status.update', $pizzaOrder->id),
            ['status' => $newStatus->value]
        );

        // Assert:
        $response->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
            $json->where('message', 'Status updated successfully!')
                ->where('pizzaOrder.id', $pizzaOrder->id)
                ->where('pizzaOrder.status', $newStatus->value)
            );

        // Assert that the database was updated.
        $this->assertDatabaseHas('pizza_orders', [
            'id' => $pizzaOrder->id,
            'status' => $newStatus->value,
        ]);

        // Assert that the status_updated_at was changed.
        $pizzaOrder->refresh(); // Refresh the model from the database.
        $this->assertNotNull($pizzaOrder->status_updated_at);
        $this->assertNotEquals($createdAt, $pizzaOrder->status_updated_at); // Compare with the stored created_at
    }

    /**
     * Test that a 200 is returned when the status is the same
     */
    public function test_returns_200_when_status_is_the_same(): void
    {
        // Arrange: Create a pizza order.
        $initialStatus = PizzaOrderStatus::WORKING;
        $pizzaOrder = PizzaOrder::factory()->create(['status' => $initialStatus]);

        // Act: Send a PATCH request with the same status.
        $response = $this->patchJson(
            route('pizza-order-status.update', $pizzaOrder->id),
            ['status' => $initialStatus->value]
        );

        // Assert:
        $response->assertOk()
            ->assertJson(['message' => 'The status is already set to the requested value.']);

        // Assert: the database was not updated
        $this->assertDatabaseHas('pizza_orders', [
            'id' => $pizzaOrder->id,
            'status' => $initialStatus->value,
        ]);
    }

    /**
     * Test validation for invalid status values.
     */
    public function test_validates_invalid_status_value(): void
    {
        // Arrange: Create a pizza order.
        $pizzaOrder = PizzaOrder::factory()->create(['status' => PizzaOrderStatus::WORKING]);
        $invalidStatus = 'invalid-status';

        // Act: Send a PATCH request with an invalid status.
        $response = $this->patchJson(
            route('pizza-order-status.update', $pizzaOrder->id),
            ['status' => $invalidStatus]
        );

        // Assert:
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        // Assert: the database was not updated
        $this->assertDatabaseHas('pizza_orders', [
            'id' => $pizzaOrder->id,
            'status' => PizzaOrderStatus::WORKING->value,
        ]);
    }

    /**
     * Test 404 response for non-existent order.
     */
    public function test_returns_404_for_non_existent_order(): void
    {
        // Act: Send a PATCH request for a non-existent order ID.
        $response = $this->patchJson(
            route('pizza-order-status.update', 99999), // Use a non-existent ID.
            ['status' => PizzaOrderStatus::READY->value]
        );

        // Assert:
        $response->assertNotFound();
    }

    /**
     * Test validation for missing status parameter.
     */
    public function test_validates_missing_status_parameter(): void
    {
        // Arrange: Create a pizza order.
        $pizzaOrder = PizzaOrder::factory()->create(['status' => PizzaOrderStatus::WORKING]);

        // Act: Send a PATCH request without the status parameter.
        $response = $this->patchJson(
            route('pizza-order-status.update', $pizzaOrder->id),
            [] // Empty data.
        );

        // Assert:
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        // Assert: Database not updated
        $this->assertDatabaseHas('pizza_orders', [
            'id' => $pizzaOrder->id,
            'status' => PizzaOrderStatus::WORKING->value,
        ]);
    }
}
