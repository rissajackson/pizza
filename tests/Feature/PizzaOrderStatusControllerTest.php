<?php

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;

it('updates the status of an order successfully', function () {
    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    $response = $this->patchJson(route('pizza-order-status.update', $pizzaOrder), [
        'status' => PizzaOrderStatus::IN_OVEN->value,
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Status updated successfully!',
            'pizzaOrder' => [
                'id' => $pizzaOrder->id,
                'status' => PizzaOrderStatus::IN_OVEN->value,
            ],
        ]);

    $pizzaOrder->refresh();

    expect($pizzaOrder->status->value)->toBe(PizzaOrderStatus::IN_OVEN->value);
    expect($pizzaOrder->status_updated_at)->not->toBeNull();
});

it('returns 200 if the status is already set', function () {
    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    $response = $this->patchJson(route('pizza-order-status.update', $pizzaOrder), [
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'The status is already set to the requested value.',
        ]);

    $pizzaOrder->refresh();

    expect($pizzaOrder->status->value)->toBe(PizzaOrderStatus::WORKING->value);
});

it('validates that the status must be a valid enum value', function () {
    $pizzaOrder = PizzaOrder::factory()->create();

    $response = $this->patchJson(route('pizza-order-status.update', $pizzaOrder), [
        'status' => 'invalid_status',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);

    $pizzaOrder->refresh();

    expect($pizzaOrder->status->value)->not->toBe('invalid_status');
});

it('requires the status field to be provided', function () {
    $pizzaOrder = PizzaOrder::factory()->create();

    $response = $this->patchJson(route('pizza-order-status.update', $pizzaOrder), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

it('returns a 404 if the pizza order does not exist', function () {
    $response = $this->patchJson(route('pizza-order-status.update', 9999), [
        'status' => PizzaOrderStatus::READY->value,
    ]);

    $response->assertStatus(404);
});
