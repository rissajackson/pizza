<?php

use App\Models\PizzaOrder;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('pizza-order.{orderId}', function ($user, $orderId) {
    $pizzaOrder = PizzaOrder::find($orderId);

    if (! $pizzaOrder) {
        return false;
    }

    return $pizzaOrder->user_id === $user->id;
});
