<?php

use App\Enums\PizzaOrderStatusEnum;
use App\Models\PizzaOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    expect(PizzaOrder::find($pizzaOrder->id))->not->toBeNull();

    $pizzaOrder->delete();

    expect(PizzaOrder::find($pizzaOrder->id))->toBeNull()
        ->and(PizzaOrder::withTrashed()->find($pizzaOrder->id))->not->toBeNull();
});
