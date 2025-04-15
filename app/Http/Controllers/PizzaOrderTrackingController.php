<?php

namespace App\Http\Controllers;

use App\Models\PizzaOrder;
use Inertia\Inertia;
use Inertia\Response;

class PizzaOrderTrackingController extends Controller
{
    /**
     * Display the dashboard with pizza orders.
     *
     * @return Response
     */
    public function index(): Response
    {
        $orders = PizzaOrder::all()->map(function ($order) {
            return [
                'id' => $order->id,
                'status' => $order->status->label() // Assuming status->label formats user-friendly labels
            ];
        });

        return Inertia::render('PizzaOrderDashboard', [
            'orders' => $orders,
            'auth' => [
                'user' => auth()->user(),
            ],
            'csrfToken' => csrf_token(),
        ]);
    }
}
