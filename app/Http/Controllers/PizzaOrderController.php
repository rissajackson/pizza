<?php

namespace App\Http\Controllers;

use App\Models\PizzaOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PizzaOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pizzaOrders = PizzaOrder::all();

        return response()->json([
            'pizzaOrders' => $pizzaOrders,
        ]);
    }

    public function show(Request $request, PizzaOrder $pizzaOrder): JsonResponse
    {
        return response()->json([
            'pizzaOrder' => $pizzaOrder,
        ]);
    }
}
