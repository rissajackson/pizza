<?php

namespace App\Models;

use App\Enums\PizzaOrderStatus;
use App\Events\PizzaOrderStatusUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PizzaOrder
 *
 * @property int $id
 * @property string $status
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PizzaOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|PizzaOrder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder|PizzaOrder whereId($value)
 * @method static PizzaOrder|null find($id, $columns = ['*'])
 * @mixin \Eloquent
 */
class PizzaOrder extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_name',
        'order_number',
        'pizza_type',
        'status',
        'order_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status_updated_at' => 'datetime',
        'status' => PizzaOrderStatus::class,
    ];

    protected static function booted(): void
    {
        static::updating(function ($pizzaOrder) {
            if ($pizzaOrder->isDirty('status')) {
                PizzaOrderStatusUpdated::dispatch($pizzaOrder);
            }
        });
    }
}
