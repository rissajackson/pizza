<?php

use App\Enums\PizzaOrderStatusEnum;
use App\Events\PizzaOrderStatusUpdatedEvent;
use App\Models\PizzaOrder;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;

function createPizzaOrderWithStatus(string $status = PizzaOrderStatusEnum::WORKING->value): PizzaOrder
{
    return PizzaOrder::factory()->create(['status' => $status]);
}

function updatePizzaOrderRoute(PizzaOrder|int $pizzaOrder, array $data = []): TestResponse
{
    return test()->patchJson(route('pizza-order-status.update', $pizzaOrder), $data);
}

it('updates the status of an order successfully', function () {
    Event::fake();

    $pizzaOrder = createPizzaOrderWithStatus();

    $response = updatePizzaOrderRoute($pizzaOrder, [
        'status' => PizzaOrderStatusEnum::IN_OVEN->value,
    ]);

    $response->assertOk()
        ->assertJson([
            'message' => 'Status updated successfully!',
            'pizzaOrder' => [
                'id' => $pizzaOrder->id,
                'status' => PizzaOrderStatusEnum::IN_OVEN->value,
            ],
        ]);

    $pizzaOrder->refresh();

    expect($pizzaOrder)
        ->status->value->toBe(PizzaOrderStatusEnum::IN_OVEN->value)
        ->status_updated_at->not->toBeNull();

    Event::assertDispatched(PizzaOrderStatusUpdatedEvent::class, function ($event) use ($pizzaOrder) {
        return $event->pizzaOrder->id === $pizzaOrder->id &&
            $event->pizzaOrder->status->value === PizzaOrderStatusEnum::IN_OVEN->value;
    });
});

it('returns 200 if the status is already set', function () {
    Event::fake();

    $pizzaOrder = createPizzaOrderWithStatus();

    $response = updatePizzaOrderRoute($pizzaOrder, [
        'status' => PizzaOrderStatusEnum::WORKING->value,
    ]);

    $response->assertStatus(422)
        ->assertJson(['message' => 'The status is already set to the requested value.']);

    $pizzaOrder->refresh();

    expect($pizzaOrder)
        ->status->value->toBe(PizzaOrderStatusEnum::WORKING->value);

    Event::assertNotDispatched(PizzaOrderStatusUpdatedEvent::class);
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
        'status' => PizzaOrderStatusEnum::READY->value,
    ]);

    $response->assertStatus(404);
});
