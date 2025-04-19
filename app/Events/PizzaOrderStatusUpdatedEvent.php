<?php

namespace App\Events;

use App\Models\PizzaOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PizzaOrderStatusUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public PizzaOrder $pizzaOrder)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('pizza-order.' . $this->pizzaOrder->id);
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->pizzaOrder->id,
            'status' => $this->pizzaOrder->status->label(),
            'updated_at' => $this->pizzaOrder->updated_at,
        ];
    }
}
