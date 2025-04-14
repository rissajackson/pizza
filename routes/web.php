<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PizzaOrderController;
use App\Http\Controllers\PizzaOrderStatusController;
use App\Http\Controllers\ProfileController;
use App\Models\PizzaOrder;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::resource('pizza-orders', PizzaOrderController::class)->only(['index', 'show']);

Route::patch('pizza-orders/{pizzaOrder}/status', [PizzaOrderStatusController::class, 'update'])->name('pizza-order-status.update');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
