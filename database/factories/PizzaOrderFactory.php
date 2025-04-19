<?php

namespace Database\Factories;

use App\Enums\PizzaOrderStatusEnum;
use App\Models\PizzaOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PizzaOrderFactory extends Factory
{
    protected $model = PizzaOrder::class;

    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name(),
            'order_number' => $this->faker->unique()->uuid(),
            'pizza_type' => $this->faker->randomElement([
                'Margherita', 'Pepperoni', 'Veggie Lovers', 'Meat Lovers', 'Hawaiian', 'Supreme',
            ]),
            'status' => $this->faker->randomElement(PizzaOrderStatusEnum::cases())->value,
            'order_type' => $this->faker->randomElement(['pickup', 'delivery']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
