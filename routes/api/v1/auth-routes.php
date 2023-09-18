<?php

use App\Http\Controllers\Api\v1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for user authentications alone
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application which involves the authentication
| of the app users.
|
*/

Route::group([],function () {

    Route::post('/login', [LoginController::class, 'login'])->name('login');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::post('/register', [RegisterController::class, 'store'])->name('register');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetPassword'])->name('password.forgot');

    Route::post('/reset-password', [ResetPasswordController::class, 'setNewPassword'])->name('password.reset');



    Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verify'])
        ->middleware(['signed'])->name('verification.verify');


    // Auth checks
    Route::group(['middleware' => ['auth:api']], function(){

        Route::post('/email/verification-notification', [RegisterController::class, 'resendVerification'])
            ->middleware(['throttle:6,1'])->name('verification.send');

        Route::get('/authenticated', [LoginController::class, 'isLoggedIn'])
            ->name('login.confirm');
    });
}
);
