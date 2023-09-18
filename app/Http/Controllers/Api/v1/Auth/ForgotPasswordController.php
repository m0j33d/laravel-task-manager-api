<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response | JsonResponse
     */
    public function sendResetPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->safe()->email)->first();

        if ($user) {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if($status === Password::RESET_LINK_SENT)
                return response()->json([
                    "status" => true,
                    "message" => "Password Reset Mail Sent"
                ]);

            throw ValidationException::withMessages([
                'email' => 'No record match this credential'
            ]);
        }

        throw ValidationException::withMessages([
            'email' => 'No record match this credential'
        ]);
    }
}
