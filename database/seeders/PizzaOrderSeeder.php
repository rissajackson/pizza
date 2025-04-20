<?php

namespace Database\Seeders;

use App\Models\PizzaOrder;
use Illuminate\Database\Seeder;

class PizzaOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PizzaOrder::factory()->count(10)->create();
        PizzaOrder::factory()->received()->count(4)->create();
        PizzaOrder::factory()->working()->count(3)->create();
        PizzaOrder::factory()->inOven()->count(2)->create();
        PizzaOrder::factory()->ready()->count(1)->create();


    }
}
