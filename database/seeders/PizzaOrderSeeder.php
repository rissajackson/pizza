<?php

namespace Database\Seeders;

use App\Models\PizzaOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PizzaOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PizzaOrder::factory()->count(20)->create();
    }
}
