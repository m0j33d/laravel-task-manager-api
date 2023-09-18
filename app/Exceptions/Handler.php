<?php

namespace App\Exceptions;

use HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Response if the request wants json for NotFound and ModelNotFound Exception
        if (
            ($exception instanceof NotFoundHttpException)
            &&
            ($request->is('api/*') ||
                $request->wantsJson()
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage() == '' ? 'Page Requested for Not Found' : $exception->getMessage()
            ], 404);
        }
        // Response if the request wants json for NotFound and ModelNotFound Exception
        if (
            ($exception instanceof AuthorizationException)
            &&
            ($request->is('api/*') ||
                $request->wantsJson()
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to perform this action'
            ], 403);
        }

        if (
            ($exception instanceof HttpException && $exception->getStatusCode() == Response::HTTP_FORBIDDEN)
            &&
            ($request->is('api/*') ||
                $request->wantsJson()
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage()
            ], 403);
        }

        // Response if the request wants json for ModelNotFound Exception
        if (
            ($exception instanceof ModelNotFoundException)
            &&
            ($request->is('api/*') ||
                $request->wantsJson()
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Entry for ' . str_replace("App\Models\\", '', $exception->getModel()) . ' resource not found'
            ], 404);
        }
        // Response if the request wants json for ModelNotFound Exception
        if (
            ($exception instanceof AuthenticationException)
            &&
            ($request->is('api/*') ||
                $request->wantsJson()
            )
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Requesting User is not authenticated'
            ], 401);
        }


        return parent::render($request, $exception);
    }
}
