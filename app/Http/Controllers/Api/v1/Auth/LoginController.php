<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function __construct(protected AuthService $service)
    {
        //
    }
    public function login(LoginRequest $request)
    {
        $this->service->authenticate($request);

        $user = Auth::user();

        $token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "Login Successful",
            "token" => $token
        ]);
    }

    public function isLoggedIn()
    {
        return response()->json([
            "status" => true
        ]);
    }

    public function logout()
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json([
            "status" => true,
            "message" => "Logout successful"
        ]);

    }
}
