<?php

namespace Tests\Feature\PhpunitPizzaOrderControllers;

use App\Models\PizzaOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PizzaOrderTrackingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_route_returns_pizza_orders(): void
    {
        $user = User::factory()->create();
        PizzaOrder::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/pizza-orders');

        $response->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('PizzaOrderDashboard')
                ->has('orders', 3)
                ->where('auth.user.id', $user->id)
            );
    }
}
