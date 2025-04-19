<?php

use App\Models\PizzaOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('renders the pizza order dashboard with orders and auth data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $orders = PizzaOrder::factory()->count(3)->create();

    $this->get(route('pizza-orders.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PizzaOrderDashboard')
            ->has('orders', 3)
            ->where('orders.0.id', $orders[0]->id)
            ->where('orders.0.status', $orders[0]->status->label())
            ->where('auth.user.id', $user->id)
            ->has('csrfToken')
        );
});
