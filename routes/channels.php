<?php

use App\Models\PizzaOrder;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('pizza-order.{orderId}', function ($user, $orderId) {
    if (! ctype_digit((string) $orderId)) {
        Log::warning("Invalid Order ID value provided: {$orderId}");

        return false;
    }

    $authorized = PizzaOrder::where('id', $orderId)
        ->where('user_id', $user->id ?? null)
        ->exists();

    if (! $authorized) {
        Log::warning("User {$user->id} tried to access unauthorized Order ID {$orderId}.");
    }

    return $authorized;
});
