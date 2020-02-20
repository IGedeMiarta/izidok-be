<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return response()->json([
                'status' => false,
                'message' => 'You do not have the required authorization...'
            ], 403);
        }

        if($exception instanceof HttpException){
            if($exception->getStatusCode() === 403){
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have the required authorization...'
                ], 403);
            }
        }

        if($exception instanceof HttpException){
            if($exception->getStatusCode() === 440){
                return response()->json([
                    'status' => false,
                    'message' => 'api key has been expired, please login again...'
                ], 440);
            }
        }

        if($exception instanceof HttpException){
            if($exception->getStatusCode() === 69){
                return response()->json([
                    'status' => false,
                    'message' => 'Your account has been logged in another device!'
                ], 469);
            }
        }
        
        return parent::render($request, $exception);
    }
}
