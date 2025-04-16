<?php

use App\Enums\PizzaOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pizza_orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 50);
            $table->string('order_number', 50)->unique();
            $table->string('pizza_type', 50);
            $table->enum('order_type', ['pickup', 'delivery']);
            $table->string('status')->default(PizzaOrderStatus::RECEIVED->value);
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pizza_orders');
    }
};
