<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PizzaOrderStatusController
 */
final class PizzaOrderStatusControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function update_behaves_as_expected(): void
    {
//        $pizzaOrderStatus = PizzaOrderStatus::factory()->create();
//        $pizzaOrderStatuses = PizzaOrderStatus::factory()->count(3)->create();
//
//        $response = $this->put(route('pizza-order-statuses.update', $pizzaOrderStatus));
    }
}
