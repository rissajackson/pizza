<?php

namespace Database\Factories;

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class PizzaOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PizzaOrder::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name(),
            'order_number' => $this->faker->unique()->uuid(),
            'pizza_type' => $this->faker->randomElement([
                'Margherita','Pepperoni','Veggie Lovers','Meat Lovers','Hawaiian','Supreme'
            ]),
            'status' => $this->faker->randomElement(PizzaOrderStatus::cases())->value,
            'order_type' => fake()->randomElement(['pickup','delivery']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
