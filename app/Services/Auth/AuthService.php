<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class AuthService
{

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate($request)
    {
        self::ensureIsNotRateLimited($request);

        $user = User::whereEmail($request['email'])->first();

        if (!Hash::check($request['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        Auth::login($user);

        RateLimiter::clear(self::throttleKey($request));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function ensureIsNotRateLimited($request)
    {
        if (!RateLimiter::tooManyAttempts(self::throttleKey($request), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn(self::throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }


    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public static function throttleKey($request)
    {
        return Str::transliterate(Str::lower($request->email) . '|' . request()->ip());
    }

}
