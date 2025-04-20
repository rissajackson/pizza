<?php

use App\Enums\PizzaOrderStatusEnum;
use App\Events\PizzaOrderStatusUpdatedEvent;
use App\Models\PizzaOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

// Test namespace is not needed for Pest

uses(TestCase::class, RefreshDatabase::class);

it('can create a PizzaOrder model instance', function () {
    $pizzaOrder = PizzaOrder::create([
        'customer_name' => 'John Doe',
        'order_number' => 'ORD123',
        'pizza_type' => 'Pepperoni',
        'status' => PizzaOrderStatusEnum::RECEIVED,
        'order_type' => 'Delivery',
    ]);

    expect($pizzaOrder)
        ->toBeInstanceOf(PizzaOrder::class)
        ->and($pizzaOrder->customer_name)->toBe('John Doe')
        ->and($pizzaOrder->order_number)->toBe('ORD123')
        ->and($pizzaOrder->pizza_type)->toBe('Pepperoni')
        ->and($pizzaOrder->status)->toBe(PizzaOrderStatusEnum::RECEIVED)
        ->and($pizzaOrder->order_type)->toBe('Delivery');
});

it('casts its attributes correctly', function () {
    $pizzaOrder = PizzaOrder::factory()->make([
        'status_updated_at' => '2023-10-22 10:00:00',
        'status' => 'received',
    ]);

    expect($pizzaOrder->status_updated_at)
        ->toBeInstanceOf(Carbon::class)
        ->and($pizzaOrder->status)
        ->toBeInstanceOf(PizzaOrderStatusEnum::class)
        ->and($pizzaOrder->status->value)
        ->toBe('received');
});

it('supports soft deletes', function () {
    $pizzaOrder = PizzaOrder::factory()->create();

    // Ensure the model exists
    expect(PizzaOrder::find($pizzaOrder->id))->not->toBeNull();

    // Soft delete the model
    $pizzaOrder->delete();

    // Assert the model is no longer findable, but still exists in the database
    expect(PizzaOrder::find($pizzaOrder->id))->toBeNull()
        ->and(PizzaOrder::withTrashed()->find($pizzaOrder->id))->not->toBeNull();
});

it('dispatches PizzaOrderStatusUpdatedEvent', function () {
    Event::fake();

    $pizzaOrder = PizzaOrder::factory()->create();

    PizzaOrderStatusUpdatedEvent::dispatch($pizzaOrder);

    Event::assertDispatched(PizzaOrderStatusUpdatedEvent::class, function ($event) use ($pizzaOrder) {
        return $event->pizzaOrder->is($pizzaOrder);
    });
});

it('does not dispatch PizzaOrderStatusUpdatedEvent if status is unchanged', function () {
    // Fake the Event facade to intercept events
    Event::fake();

    $pizzaOrder = PizzaOrder::factory()->create([
        'status' => PizzaOrderStatusEnum::RECEIVED,
    ]);

    // Perform an update, but do not change the 'status' property
    $pizzaOrder->update([
        'customer_name' => 'Jane Doe',
    ]);

    // Assert the PizzaOrderStatusUpdatedEvent was not dispatched
    Event::assertNotDispatched(PizzaOrderStatusUpdatedEvent::class);
});
