<?php

namespace App\Models;

use App\Enums\PizzaOrderStatusEnum;
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
        'status_updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'status_updated_at' => 'datetime',
        'status' => PizzaOrderStatusEnum::class,
    ];
}
