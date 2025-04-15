<?php

namespace App\Events;

use App\Models\PizzaOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PizzaOrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PizzaOrder $pizzaOrder;

    /**
     * Create a new event instance.
     */
    public function __construct(PizzaOrder $pizzaOrder)
    {
        $this->pizzaOrder = $pizzaOrder;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('pizza-order.' . $this->pizzaOrder->id);
    }

    /**
     * Define the data that should be sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->pizzaOrder->id,
            'status' => $this->pizzaOrder->status->label(), // Assume 'status' is a field in the PizzaOrder model
            'updated_at' => $this->pizzaOrder->updated_at, // Optional for UI
        ];
    }
}
