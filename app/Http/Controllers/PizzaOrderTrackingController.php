<?php

namespace App\Http\Controllers;

use App\Models\PizzaOrder;
use Inertia\Inertia;
use Inertia\Response;

class PizzaOrderTrackingController extends Controller
{
    public function index(): Response
    {
        $orders = PizzaOrder::all()->map(fn ($order) => [
            'id' => $order->id,
            'status' => $order->status->label(),
        ]);

        return Inertia::render('PizzaOrderDashboard', [
            'orders' => $orders,
            'auth' => [
                'user' => auth()->user(),
            ],
            'csrfToken' => csrf_token(),
        ]);
    }
}
