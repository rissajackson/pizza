<?php

use App\Models\PizzaOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

// Test for the index method of the PizzaOrderTrackingController
it('renders the pizza order dashboard with orders and auth data', function () {
    // Arrange: Create a test user and log them in
    $user = User::factory()->create();
    $this->actingAs($user);

    // Arrange: Create some test pizza orders
    $orders = PizzaOrder::factory()->count(3)->create();

    // Act: Hit the index route of the controller
    $response = $this->get(route('pizza-orders.index')); // Assuming the route is named `pizza-orders.index`

    // Assert: Check that the response status is OK
    $response->assertOk();

    // Assert: Check the structure of the Inertia response and content
    $response->assertInertia(fn (Assert $page) =>
    $page
        ->component('PizzaOrderDashboard') // Ensure it uses the correct Inertia component
        ->has('orders', 3) // Ensure the orders array contains 3 items
        ->where('orders.0.id', $orders[0]->id) // Check specific order data
        ->where('orders.0.status', $orders[0]->status->label()) // Check formatted status label
        ->where('auth.user.id', $user->id) // Ensure user data matches the authenticated user
        ->has('csrfToken') // Ensure the CSRF token exists
    );
});
