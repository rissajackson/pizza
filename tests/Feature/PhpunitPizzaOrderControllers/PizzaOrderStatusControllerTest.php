<?php

namespace Tests\Feature\PhpunitPizzaOrderControllers;

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PizzaOrderStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    protected PizzaOrder $pizzaOrder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pizzaOrder = PizzaOrder::factory()->create([
            'status' => PizzaOrderStatus::WORKING,
            'created_at' => now()->subMinutes(10),
        ]);
    }

    public function test_updates_status_successfully(): void
    {
        $newStatus = PizzaOrderStatus::IN_OVEN->value;

        $response = $this->patchJson(
            route('pizza-order-status.update', $this->pizzaOrder->id),
            ['status' => $newStatus]
        );

        $response
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->where('message', 'Status updated successfully!')
                ->where('pizzaOrder.id', $this->pizzaOrder->id)
                ->where('pizzaOrder.status', $newStatus)
            );

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => $newStatus,
        ]);

        $this->pizzaOrder->refresh();
        $this->assertNotNull($this->pizzaOrder->status_updated_at);
        $this->assertNotEquals($this->pizzaOrder->created_at, $this->pizzaOrder->status_updated_at);
    }

    public function test_returns_200_when_status_is_the_same(): void
    {
        $response = $this->patchJson(
            route('pizza-order-status.update', $this->pizzaOrder->id),
            ['status' => PizzaOrderStatus::WORKING->value]
        );

        $response
            ->assertOk()
            ->assertJson(['message' => 'The status is already set to the requested value.']);

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => PizzaOrderStatus::WORKING->value,
        ]);
    }

    public function test_validates_invalid_status_value(): void
    {
        $response = $this->patchJson(
            route('pizza-order-status.update', $this->pizzaOrder->id),
            ['status' => 'invalid-status']
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => PizzaOrderStatus::WORKING->value,
        ]);
    }

    public function test_returns_404_for_non_existent_order(): void
    {
        $response = $this->patchJson(
            route('pizza-order-status.update', 99999),
            ['status' => PizzaOrderStatus::READY->value]
        );

        $response->assertNotFound();
    }

    public function test_validates_missing_status_parameter(): void
    {
        $response = $this->patchJson(
            route('pizza-order-status.update', $this->pizzaOrder->id),
            []
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => PizzaOrderStatus::WORKING->value,
        ]);
    }
}
