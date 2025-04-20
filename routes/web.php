<?php

use App\Http\Controllers\PizzaOrderStatusController;
use App\Http\Controllers\PizzaOrderTrackingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/auth.php';

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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('pizza-orders')->name('pizza-orders.')->group(function () {
        Route::get('/', [PizzaOrderTrackingController::class, 'index'])->name('index');

        Route::middleware('throttle:update-status')->group(function () {
            Route::patch('{pizzaOrder}/status', [PizzaOrderStatusController::class, 'update'])
                ->name('status.update');
        });
    });
});
