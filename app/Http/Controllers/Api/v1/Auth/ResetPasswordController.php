<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\StoreNewPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResetPasswordController extends Controller
{
    public function setNewPassword(StoreNewPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),

            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if($status === Password::PASSWORD_RESET)
            return response()->json([
                "status" => true,
                "message" => "Password Reset Successfully"
            ]);

        throw ValidationException::withMessages([
            'Password Reset Failed. Try again.'
        ]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        if (Hash::check($data['current_password'], $user->password)) {

            if (strcmp($data['current_password'], $data['new_password']) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'New password cannot be the same as current password'
                ],401);
            }

            $user->update([
                "password" => bcrypt($data['new_password'])
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password changed successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect current password entered'
            ], 400);
        }

    }
}
