<?php

namespace App\Models;

use App\Enums\PizzaOrderStatusEnum;
use App\Events\PizzaOrderStatusUpdatedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PizzaOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_name',
        'order_number',
        'pizza_type',
        'status',
        'order_type',
    ];

    protected $casts = [
        'id' => 'integer',
        'status_updated_at' => 'datetime',
        'status' => PizzaOrderStatusEnum::class,
    ];

    protected static function booted(): void
    {
        static::updated(function (self $pizzaOrder) {
            if ($pizzaOrder->wasChanged('status')) {
                PizzaOrderStatusUpdatedEvent::dispatch($pizzaOrder);
            }
        });
    }
}
