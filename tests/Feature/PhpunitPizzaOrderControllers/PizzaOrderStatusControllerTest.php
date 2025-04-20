<?php

namespace Tests\Feature\PhpunitPizzaOrderControllers;

use App\Enums\PizzaOrderStatusEnum;
use App\Events\PizzaOrderStatusUpdatedEvent;
use App\Models\PizzaOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PizzaOrderStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    protected PizzaOrder $pizzaOrder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pizzaOrder = PizzaOrder::factory()->working()->create([
            'created_at' => now()->subMinutes(10),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_updates_status_successfully(): void
    {
        Event::fake();

        $newStatus = PizzaOrderStatusEnum::IN_OVEN->value;

        $response = $this->patchJson(
            route('pizza-orders.status.update', $this->pizzaOrder->id),
            ['status' => $newStatus]
        );

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('message', 'Status updated successfully!')
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

        Event::assertDispatched(PizzaOrderStatusUpdatedEvent::class, function ($event) use ($newStatus) {
            return $event->pizzaOrder->id === $this->pizzaOrder->id &&
                $event->pizzaOrder->status->value === $newStatus;
        });
    }

    public function test_returns_200_when_status_is_the_same(): void
    {
        Event::fake();

        $response = $this->patchJson(
            route('pizza-orders.status.update', $this->pizzaOrder->id),
            ['status' => PizzaOrderStatusEnum::WORKING->value]
        );

        $response
            ->assertStatus(422)
            ->assertJson(['message' => 'The status is already set to the requested value.']);

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => PizzaOrderStatusEnum::WORKING->value,
        ]);

        Event::assertNotDispatched(PizzaOrderStatusUpdatedEvent::class);
    }

    public function test_validates_invalid_status_value(): void
    {
        $response = $this->patchJson(
            route('pizza-orders.status.update', $this->pizzaOrder->id),
            ['status' => 'invalid-status']
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => PizzaOrderStatusEnum::WORKING->value,
        ]);
    }

    public function test_returns_404_for_non_existent_order(): void
    {
        $response = $this->patchJson(
            route('pizza-orders.status.update', 99999),
            ['status' => PizzaOrderStatusEnum::READY->value]
        );

        $response->assertNotFound();
    }

    public function test_validates_missing_status_parameter(): void
    {
        $response = $this->patchJson(
            route('pizza-orders.status.update', $this->pizzaOrder->id),
            []
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('pizza_orders', [
            'id' => $this->pizzaOrder->id,
            'status' => PizzaOrderStatusEnum::WORKING->value,
        ]);
    }
}
