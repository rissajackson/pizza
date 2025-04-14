<?php

namespace App\Http\Controllers;

use App\Models\PizzaOrder;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the dashboard with pizza orders.
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        // Fetch pizza orders and map them to a cleaner structure
        $orders = PizzaOrder::all()->map(function ($order) {
            return [
                'id' => $order->id,
                'status' => $order->status->label() // Assuming the label() gives a user-friendly name
            ];
        });
//        dd($orders);

        // Pass data to the Inertia view
        return Inertia::render('Dashboard', [
            'orders' => $orders ?? [],
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }
}
