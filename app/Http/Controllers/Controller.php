<?php

namespace App\Http\Controllers;

use App\Traits\Response\CustomResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a json response
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverErrorResponse(string $message = 'Error Occurred, Please try again later', int $code = null)
    {

        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code ?: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
