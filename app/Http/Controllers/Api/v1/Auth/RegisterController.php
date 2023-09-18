<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreUserRequest;
use App\Http\Requests\User\Auth\StoreNewUserRequest;
use App\Http\Resources\User\UserResource;
use App\Jobs\ActiveCampaign\SyncUserToActiveCampaign;
use App\Models\Invite;
use App\Models\User;
use App\Services\Auth\TaskService;
use App\Services\User\UserService;
use App\Traits\Response\CustomResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Two\User as ProviderUser;
use Stevebauman\Location\Facades\Location;


class RegisterController extends Controller
{

    public function __construct(public Application $app, protected TaskService $service)
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

            $token = $user->createToken('access_token')->accessToken;

            Auth::login($user);

            event(new Registered($user));

            $response['user'] = new UserResource($user);
            $response['token'] = $token;

            return response()->json([
                "status" => true,
                "message" => "User registration successful",
                "data" => $response
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

            $user->update(["status" => "active"]);

            return response()->json([
                "status" => true,
                "message" => "Email Verified"
            ]);

        }catch(\Throwable $error){
            return response()->json([
                "status" => true,
                "message" => "Unable to verify email"
            ], 500);
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
            return response()->json([
                "status" => false,
                "message" => "Cannot resend verification email now. Try again",
            ], 500);
        }
    }
}
