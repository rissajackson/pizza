<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class ApiTokenController extends Controller
{
    public function generateToken()
    {
        // Generate a random token
        $token = Str::random(60);

        // Save this token securely (e.g., in your .env or a database)
        // For simplicity, we will return it for now
        return response()->json(['token' => $token]);
    }
}
