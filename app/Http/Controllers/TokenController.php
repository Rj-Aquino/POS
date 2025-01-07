<?php

namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessTokenResult;

class TokenController extends Controller
{
    public function generateToken(Request $request)
    {
        // Assuming you have a user to issue the token to, for example, an admin user
        $user = User::find(1); // Retrieve user by ID (you can modify this logic)

        // Generate a token for the user
        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}
