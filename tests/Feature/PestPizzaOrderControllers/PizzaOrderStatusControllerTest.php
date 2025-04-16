<?php

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;

function createPizzaOrderWithStatus(string $status = PizzaOrderStatus::WORKING->value): PizzaOrder
{
    return PizzaOrder::factory()->create(['status' => $status]);
}

function updatePizzaOrderRoute(PizzaOrder|int $pizzaOrder, array $data = []): \Illuminate\Testing\TestResponse
{
    return test()->patchJson(route('pizza-order-status.update', $pizzaOrder), $data);
}

it('updates the status of an order successfully', function () {
    $pizzaOrder = createPizzaOrderWithStatus();

    $response = updatePizzaOrderRoute($pizzaOrder, [
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

    expect($pizzaOrder)
        ->status->value->toBe(PizzaOrderStatus::IN_OVEN->value)
        ->status_updated_at->not->toBeNull();
});

it('returns 200 if the status is already set', function () {
    $pizzaOrder = createPizzaOrderWithStatus();

    $response = updatePizzaOrderRoute($pizzaOrder, [
        'status' => PizzaOrderStatus::WORKING->value,
    ]);

    $response->assertOk()
        ->assertJson(['message' => 'The status is already set to the requested value.']);

    $pizzaOrder->refresh();

    expect($pizzaOrder)
        ->status->value->toBe(PizzaOrderStatus::WORKING->value);
});

it('fails validation when status is not a valid enum value', function () {
    $pizzaOrder = createPizzaOrderWithStatus();

    $response = updatePizzaOrderRoute($pizzaOrder, [
        'status' => 'invalid_status',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);

    $pizzaOrder->refresh();

    expect($pizzaOrder)
        ->status->value->not->toBe('invalid_status');
});

it('requires the status field to be provided', function () {
    $pizzaOrder = createPizzaOrderWithStatus();

    $response = updatePizzaOrderRoute($pizzaOrder, []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

it('returns 404 if the pizza order does not exist', function () {
    $response = updatePizzaOrderRoute(9999, [
        'status' => PizzaOrderStatus::READY->value,
    ]);

    $response->assertStatus(404);
});
