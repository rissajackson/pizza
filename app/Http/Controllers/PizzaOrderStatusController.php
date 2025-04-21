<?php

namespace App\Http\Controllers;

use App\Enums\PizzaOrderStatusEnum;
use App\Events\PizzaOrderStatusUpdatedEvent;
use App\Models\PizzaOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PizzaOrderStatusController extends Controller
{
    public function update(Request $request, PizzaOrder $pizzaOrder): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(PizzaOrderStatusEnum::values())],
        ]);

        if ($pizzaOrder->status->value === $validated['status']) {
            return response()->json([
                'message' => 'The status is already set to the requested value.',
            ], 422);
        }

        $pizzaOrder->update([
            'status' => $validated['status'],
            'status_updated_at' => now(),
        ]);

        PizzaOrderStatusUpdatedEvent::dispatch($pizzaOrder);

        return response()->json([
            'message' => 'Status updated successfully!',
            'pizzaOrder' => $pizzaOrder,
        ]);
    }
}
