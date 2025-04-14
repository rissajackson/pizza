<?php

namespace App\Http\Controllers;

use App\Enums\PizzaOrderStatus;
use App\Events\PizzaOrderStatusUpdated;
use App\Models\PizzaOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $authHeader = $request->header('Authorization');
        $apiToken = str_replace('Bearer ', '', $authHeader);

        if (empty($authHeader) || $apiToken !== env('API_TOKEN')) {
            return response()->json([
                'error' =>
                'Unauthorized: Invalid or missing API token. Please provide a valid API token in the Authorization header.',
            ], status: 401);
        }

        // Validate the incoming data
        $validated = $request->validate([
//            'pizza_id' =>'required|integer', look at something like this later
            'status' => ['required', 'string', function (string $attribute, string $value, callable $fail) {
                if (!in_array($value, PizzaOrderStatus::values(), true)) {
                    $fail("The selected {$attribute} is invalid.");
                }
            }],
        ]);

        // Step 3: Prevent duplicate status updates
        if ($pizzaOrder->status === $validated['status']) {
            return response()->json([
                'message' => 'The status is already set to the requested value.',
            ], 200);
        }

        // Update the status using the enum
        $pizzaOrder->status = PizzaOrderStatus::from($validated['status']); // Enforced type safety
        $pizzaOrder->status_updated_at = now(); // Explicitly update the timestamp
        $pizzaOrder->save();

        event(new PizzaOrderStatusUpdated($pizzaOrder));

        return response()->json([
            'message' => 'Status updated successfully!',
            'pizzaOrder' => $pizzaOrder,
        ]);
    }
}
