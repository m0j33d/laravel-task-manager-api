<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Enums\User\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{

    public function __construct(public Application $app, protected AuthService $service)
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response | JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        try{
            $user = User::create($request->validated());

            event(new Registered($user));

            return response()->json([
                "status" => true,
                "message" => "User registration successful",
            ]);
        }catch(\Throwable $error){
            return $this->serverErrorResponse('User Registration Failed, Please try again later');
        }
    }

    public function verify(Request $request)
    {
        try {
            $user = User::find($request->route('id'));

            if (!hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
                return response()->json([
                    "status" => false,
                    "message" => "Unable to verify email"
                ], 422);
            }

            if ($user->markEmailAsVerified())
                event(new Verified($user));

            $user->update(["status" => UserStatus::ACTIVE()]);

            return response()->json([
                "status" => true,
                "message" => "Email Verified"
            ]);

        }catch(\Throwable $error){
            logger($error);
            return $this->serverErrorResponse('Unable to verify email');
        }
    }

    public function resendVerification(Request $request)
    {
        try{
            $request->user()->sendEmailVerificationNotification();

            return response()->json([
                "status" => true,
                "message" => "New verification email sent. Check your mail",
            ]);

        }catch (\Throwable $error){
            return $this->serverErrorResponse('Cannot resend verification email now. Try again');
        }
    }
}
