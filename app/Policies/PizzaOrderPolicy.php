<?php

namespace App\Policies;

use App\Models\PizzaOrder;
use App\Models\User;

class PizzaOrderPolicy
{
    public function view(User $user, PizzaOrder $pizzaOrder): bool
    {
        return $pizzaOrder->user_id === $user->id;
    }
}
