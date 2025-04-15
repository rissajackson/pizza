<?php

namespace App\Http\Controllers;

use App\Enums\PizzaOrderStatus;
use App\Models\PizzaOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PizzaOrderStatusController extends Controller
{
    /**
     * Update the status of a pizza order.
     *
     * @param Request $request
     * @param PizzaOrder $pizzaOrder
     * @return JsonResponse
     */
    public function update(Request $request, PizzaOrder $pizzaOrder): JsonResponse
    {
        // Validate the incoming data
        $validated = $request->validate([
            'status' => ['required', 'string', function (string $attribute, string $value, callable $fail) {
                if (!in_array($value, PizzaOrderStatus::values(), true)) {
                    $fail("The selected {$attribute} is invalid.");
                }
            }],
        ]);

        // Step 3: Prevent duplicate status updates
        if ($pizzaOrder->status->value === $validated['status']) { // Compare `value` of enum
            return response()->json([
                'message' => 'The status is already set to the requested value.',
            ], 200);
        }

        // Update the status using the validated string value
        $pizzaOrder->status = $validated['status']; // Ensure raw string is passed for DB save
        $pizzaOrder->status_updated_at = now(); // Explicitly update the timestamp
        $pizzaOrder->save();

        return response()->json([
            'message' => 'Status updated successfully!',
            'pizzaOrder' => $pizzaOrder,
        ]);
    }
}
