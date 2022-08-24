<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function user()
    {
        return Auth::user();
    }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        return \response()->json([
            'message' => 'Registration successful'
        ], 200);
    }

    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Inavalid Credentials'
            ], Response::HTTP_UNAUTHORIZED);
        };
        $user = Auth::user();
        $token = $user->createToken('token')->plainTextToken;
        $cookie = cookie('jwt', $token, 60 * 24 * 24); //1day
        return response([
            'message' => 'Login Successful'
        ])->withCookie($cookie);

    }

    public function logout()
    {
        $cookie = Cookie::forget('jwt');

        return response([
            'message' => 'logged out'
        ])->withCookie($cookie);
    }
}
