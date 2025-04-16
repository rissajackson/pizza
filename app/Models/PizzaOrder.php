<?php

namespace App\Models;

use App\Enums\PizzaOrderStatus;
use App\Events\PizzaOrderStatusUpdated;
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
        'status' => PizzaOrderStatus::class,
    ];

    protected static function booted(): void
    {
        static::updating(function (self $pizzaOrder) {
            if ($pizzaOrder->isDirty('status')) {
                PizzaOrderStatusUpdated::dispatch($pizzaOrder);
            }
        });
    }
}
