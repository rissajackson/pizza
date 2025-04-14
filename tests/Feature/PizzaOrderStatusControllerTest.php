<?php

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    // Set a valid API token in the environment for testing
    $this->validToken = 'RZYEArp5IK9uhfCin7wM2vg3EtZBsJDqyF6oanxUl19XKPQ4dLtWHxVQ0YuANM';
    config(['app.api_token' => $this->validToken]);
});

it('uses the testing environment configuration', function () {
    $this->assertEquals('testing', env('APP_ENV'), 'Not using the testing environment');
    $this->assertEquals('RZYEArp5IK9uhfCin7wM2vg3EtZBsJDqyF6oanxUl19XKPQ4dLtWHxVQ0YuANM', env('API_TOKEN'), 'Testing API token mismatch');
    $this->assertEquals('sqlite', env('DB_CONNECTION'), 'Database connection is not set to SQLite for testing');
});

it('updates the status of an order successfully with a valid token', function () {
    // Create a test pizza order
    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken) // Include API token
    ->patchJson(route('pizza-order-status.update', $pizzaOrder->id), [
        'status' => PizzaOrderStatus::IN_OVEN->value,
    ]);

    // Assert the response is successful
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
        $json->where('message', 'Status updated successfully!')
            ->where('pizzaOrder.id', $pizzaOrder->id)
            ->where('pizzaOrder.status', PizzaOrderStatus::IN_OVEN->value)
            ->etc()
        );

    // Ensure the database is updated
    $this->assertDatabaseHas('pizza_orders', [
        'id' => $pizzaOrder->id,
        'status' => PizzaOrderStatus::IN_OVEN->value,
    ]);
});

it('updates the status of an order successfully with a valid token and updates status_updated_at', function () {
    // Create a test pizza order with initial status
    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    // Capture the old `status_updated_at` timestamp
    $oldStatusUpdatedAt = $pizzaOrder->status_updated_at;

    // Send the update request with a valid token
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken)
        ->patchJson(route('pizza-order-status.update', $pizzaOrder->id), [
            'status' => PizzaOrderStatus::IN_OVEN->value,
        ]);

    // Assert the response is successful
    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
        $json->where('message', 'Status updated successfully!')
            ->where('pizzaOrder.id', $pizzaOrder->id)
            ->where('pizzaOrder.status', PizzaOrderStatus::IN_OVEN->value)
            ->etc()
        );

    // Verify the database updates the status and the status_updated_at timestamp
    $this->assertDatabaseHas('pizza_orders', [
        'id' => $pizzaOrder->id,
        'status' => PizzaOrderStatus::IN_OVEN->value,
    ]);

    // Assert `status_updated_at` is updated and greater than the previous timestamp
    $pizzaOrder->refresh();
    $this->assertNotEquals($oldStatusUpdatedAt, $pizzaOrder->status_updated_at);
    $this->assertNotNull($pizzaOrder->status_updated_at);
    $this->assertTrue($pizzaOrder->status_updated_at > $oldStatusUpdatedAt);
});

it('fails with a 401 Unauthorized when the API token is missing', function () {
    // Create a test pizza order
    $pizzaOrder = PizzaOrder::factory()->create();

    $response = $this->patchJson(route('pizza-order-status.update', $pizzaOrder->id), [
        'status' => PizzaOrderStatus::READY->value,
    ]);

    // Assert 401 Unauthorized
    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized: Invalid or missing API token. Please provide a valid API token in the Authorization header.',
        ]);
});

it('fails with a 401 Unauthorized when the API token is invalid', function () {
    // Create a test pizza order
    $pizzaOrder = PizzaOrder::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer InvalidToken') // Invalid token
    ->patchJson(route('pizza-order-status.update', $pizzaOrder->id), [
        'status' => PizzaOrderStatus::READY->value,
    ]);

    // Assert 401 Unauthorized
    $response->assertStatus(401)
        ->assertJson([
            'error' => 'Unauthorized: Invalid or missing API token. Please provide a valid API token in the Authorization header.',
        ]);
});

it('validates that the status must be a valid enum value', function () {
    // Create a test pizza order
    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken) // Include API token
    ->patchJson(route('pizza-order-status.update', $pizzaOrder->id), [
        'status' => 'invalid_status', // Invalid status
    ]);

    // Assert validation error
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);

    // Ensure the database is not updated
    $this->assertDatabaseHas('pizza_orders', [
        'id' => $pizzaOrder->id,
        'status' => PizzaOrderStatus::WORKING->value, // Should remain the same
    ]);
});

it('does not modify status_updated_at if validation fails', function () {
    // Create a test pizza order
    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatus::WORKING->value,
        'status_updated_at' => now(),
    ]);

    // Capture the current `status_updated_at` timestamp
    $statusUpdatedAt = $pizzaOrder->status_updated_at;

    // Send a request with an invalid status
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken)
        ->patchJson(route('pizza-order-status.update', $pizzaOrder->id), [
            'status' => 'INVALID_STATUS',
        ]);

    // Assert validation failure
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);

    // Ensure `status` and `status_updated_at` remain unchanged
    $pizzaOrder->refresh();
    $this->assertEquals(PizzaOrderStatus::WORKING, $pizzaOrder->status);
    $this->assertEquals($statusUpdatedAt, $pizzaOrder->status_updated_at);
});

it('returns a 404 if the pizza order does not exist', function () {
    // Test with a non-existent pizza order ID
    $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken) // Include API token
    ->patchJson(route('pizza-order-status.update', 9999), [
        'status' => PizzaOrderStatus::READY->value,
    ]);

    // Assert 404 Not Found
    $response->assertStatus(404);
});

it('validates the request payload is required', function () {
    // Create a test pizza order
    $pizzaOrder = PizzaOrder::factory()->create();

    $response = $this->withHeader('Authorization', 'Bearer ' . $this->validToken) // Include API token
    ->patchJson(route('pizza-order-status.update', $pizzaOrder->id), []);

    // Assert validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});
