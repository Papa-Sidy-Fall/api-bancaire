<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    //
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'succes' => false,
                'message' => 'Identifiants invalides'
            ], 401);
        }

        $user = Auth::user();
        $accesstoken = $user->createToken('API Token')->accessToken;
        $refreshToken = $user->createToken('Refresh Token')->accessToken;
        Cookie::queue('access_token', $accesstoken, 60 * 24 * 7);

        return response()->json([
            'succes' => true,
            'token' => $accesstoken,
            'refresh_token' => $refreshToken
        ]);
    }
}
