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
            'order_number' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'pizza_type' => $this->faker->randomElement([
                'Margherita', 'Pepperoni', 'Veggie Lovers', 'Meat Lovers', 'Hawaiian', 'Supreme',
            ]),
            'status' => $this->faker->randomElement(PizzaOrderStatusEnum::cases())->value,
            'order_type' => $this->faker->randomElement(['pickup', 'delivery']),
            'status_updated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    public function status(string $status): self
    {
        return $this->state(fn () => ['status' => $status, 'status_updated_at' => now()]);
    }

    public function received(): self
    {
        return $this->status(PizzaOrderStatusEnum::RECEIVED->value);
    }

    public function working(): self
    {
        return $this->status(PizzaOrderStatusEnum::WORKING->value);
    }

    public function inOven(): self
    {
        return $this->status(PizzaOrderStatusEnum::IN_OVEN->value);
    }

    public function ready(): self
    {
        return $this->status(PizzaOrderStatusEnum::READY->value);
    }
}
