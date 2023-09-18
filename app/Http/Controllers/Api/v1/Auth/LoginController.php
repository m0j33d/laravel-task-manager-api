<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Services\Auth\TaskService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function __construct(protected TaskService $service)
    {
        //
    }
    public function login(LoginRequest $request)
    {
        $this->service->authenticate($request);

        $user = Auth::user();

        $token = $user->createToken('access_token')->accessToken;

        return response()->json([
            "status" => true,
            "message" => "Login Successful",
            "token" => $token
        ]);
    }

    public function isLoggedIn()
    {
        if (Auth::guard('api')->check()) {
            return response()->json([
                "status" => true
            ]);
        }

        throw new AuthenticationException('User Not Authenticated');
    }

    public function logout()
    {
        if (Auth::guard('api')->check()) {
            Auth::user()->AauthAcessToken()->delete();
            return response()->json([
                "status" => true,
                "message" => "Logout successful"
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "User was not logged in"
        ],403);
    }
}
