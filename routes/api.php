<?php

use App\Http\Controllers\ApiTokenController;
use Illuminate\Support\Facades\Route;


Route::get('generate-token', [ApiTokenController::class, 'generateToken']);

