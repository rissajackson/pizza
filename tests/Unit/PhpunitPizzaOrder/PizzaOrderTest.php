<?php


namespace Tests\Unit\PhpunitPizzaOrder;

use App\Enums\PizzaOrderStatusEnum;
use App\Events\PizzaOrderStatusUpdatedEvent;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PizzaOrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_can_create_a_pizza_order()
    {
        $pizzaOrder = PizzaOrder::create([
            'customer_name' => 'John Doe',
            'order_number' => 'ORD123',
            'pizza_type' => 'Pepperoni',
            'status' => PizzaOrderStatusEnum::RECEIVED,
            'order_type' => 'Delivery',
        ]);

        $this->assertInstanceOf(PizzaOrder::class, $pizzaOrder);
        $this->assertEquals('John Doe', $pizzaOrder->customer_name);
        $this->assertEquals('ORD123', $pizzaOrder->order_number);
        $this->assertEquals('Pepperoni', $pizzaOrder->pizza_type);
        $this->assertEquals(PizzaOrderStatusEnum::RECEIVED, $pizzaOrder->status);
        $this->assertEquals('Delivery', $pizzaOrder->order_type);
    }

    #[Test]
    public function it_casts_attributes_correctly()
    {
        $pizzaOrder = PizzaOrder::factory()->make([
            'status_updated_at' => '2023-10-22 10:00:00',
            'status' => 'received',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $pizzaOrder->status_updated_at);
        $this->assertInstanceOf(PizzaOrderStatusEnum::class, $pizzaOrder->status);
        $this->assertEquals('received', $pizzaOrder->status->value);
    }

    #[Test]
    public function it_supports_soft_deletes()
    {
        $pizzaOrder = PizzaOrder::factory()->create();

        // Ensure the model exists
        $this->assertNotNull(PizzaOrder::find($pizzaOrder->id));

        // Soft delete the model
        $pizzaOrder->delete();

        // Assert the model is no longer findable, but still exists in the database
        $this->assertNull(PizzaOrder::find($pizzaOrder->id));
        $this->assertNotNull(PizzaOrder::withTrashed()->find($pizzaOrder->id));
    }

    #[Test]
    public function it_dispatches_pizza_order_status_updated_event()
    {
        Event::fake();

        $pizzaOrder = PizzaOrder::factory()->create();

        PizzaOrderStatusUpdatedEvent::dispatch($pizzaOrder);

        Event::assertDispatched(PizzaOrderStatusUpdatedEvent::class, function ($event) use ($pizzaOrder) {
            return $event->pizzaOrder->is($pizzaOrder);
        });
    }

    #[Test]
    public function it_does_not_dispatch_pizza_order_status_updated_event_if_status_is_unchanged()
    {
        Event::fake();

        $pizzaOrder = PizzaOrder::factory()->create([
            'status' => PizzaOrderStatusEnum::RECEIVED,
        ]);

        $pizzaOrder->customer_name = 'Jane Doe';
        $pizzaOrder->save();

        Event::assertNotDispatched(PizzaOrderStatusUpdatedEvent::class);
    }
}
