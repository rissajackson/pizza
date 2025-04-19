<?php

namespace App\Http\Controllers;

use App\Enums\PizzaOrderStatusEnum;
use App\Models\PizzaOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PizzaOrderStatusController extends Controller
{
    /**
     * Update the status of a pizza order.
     */
    public function update(Request $request, PizzaOrder $pizzaOrder): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', function (string $attribute, string $value, callable $fail) {
                if (! in_array($value, PizzaOrderStatusEnum::values(), true)) {
                    $fail("The selected {$attribute} is invalid.");
                }
            }],
        ]);

        if ($pizzaOrder->status->value === $validated['status']) { // Compare `value` of enum
            return response()->json([
                'message' => 'The status is already set to the requested value.',
            ], 200);
        }

        $pizzaOrder->status = $validated['status'];
        $pizzaOrder->status_updated_at = now();
        $pizzaOrder->save();

        return response()->json([
            'message' => 'Status updated successfully!',
            'pizzaOrder' => $pizzaOrder,
        ]);
    }
}
