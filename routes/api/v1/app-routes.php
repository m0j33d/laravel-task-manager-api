<?php


use App\Http\Controllers\Api\v1\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([], function () {


    /*
    |--------------------------------------------------------------------------
    | Tasks Routes
    |--------------------------------------------------------------------------
    |
    | Here is all the routes for Tasks management
    |
    */

    Route::group(['middleware' => 'verified'], function () {

        Route::apiResource('/tasks', TaskController::class);

    });

});

