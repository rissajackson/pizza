<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\PizzaOrder;

Broadcast::channel('pizza-order.{orderId}', function ($user, $orderId) {
    // Check if the order exists
    $pizzaOrder = PizzaOrder::find($orderId);

    if (!$pizzaOrder) {
        return false; // Or, you could throw an exception:  throw new \Illuminate\Broadcasting\BroadcastException('Order not found.');
    }

    // Check if the order belongs to the authenticated user
    return $pizzaOrder->user_id === $user->id;
});
