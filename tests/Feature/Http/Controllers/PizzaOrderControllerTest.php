<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PizzaOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PizzaOrderController
 */
final class PizzaOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $pizzaOrders = PizzaOrder::factory()->count(3)->create();

        $response = $this->get(route('pizza-orders.index'));
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $pizzaOrder = PizzaOrder::factory()->create();
        $pizzaOrders = PizzaOrder::factory()->count(3)->create();

        $response = $this->get(route('pizza-orders.show', $pizzaOrder));
    }
}
