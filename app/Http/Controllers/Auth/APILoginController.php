<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class APILoginController extends Controller
{
    public function login()
    {
        $credentials = request(['email', 'password']);

        // try to auth and get the token using api authentication
        if (!$token = auth('api')->attempt($credentials)) {

            return response()->json(['email' => 'Wrong email or password'], 401);
        }

        return response()->json([
            'token' => $token,
            'type' => 'bearer',
            'expires' => auth('api')->factory()->getTTL() * 60,

        ]);
    }
}
