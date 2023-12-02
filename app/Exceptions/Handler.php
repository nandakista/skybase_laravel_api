<?php

namespace App\Exceptions;

use Error;
use Throwable;
use BadMethodCallException;
use App\Helpers\ResponseHelper;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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

        $this->renderable(function(AuthenticationException $e){
            if (request()->is('api/*')) {
                return ResponseHelper::unauthorized();
            }
        });

        $this->renderable(function(NotFoundHttpException $e){
            if (request()->is('api/*')) {
                return ResponseHelper::error(
                    code: 404, 
                    message: 'Not found', 
                    error: 'Not found'
                );
            }
        });

        $this->renderable(function(MethodNotAllowedHttpException $e) {
            if (request()->is('api/*')) {
                return ResponseHelper::error(
                    code: 405, 
                    message: 'Method Not Allowed', 
                    error: 'Method Not Allowed'
                );
            }
        });

        $this->renderable(function(BadMethodCallException $e) {
            if (request()->is('api/*')) {
                return ResponseHelper::error(
                    code: 500, 
                    message: 'Internal Server Error', 
                    error: 'Feature not implemented'
                );
            }
        });

        $this->renderable(function(Error $e) {
            if (request()->is('api/*')) {
                return ResponseHelper::error(
                    code: 500, 
                    message: 'Internal Server Error', 
                    error: json_decode($e->getMessage()) ?? $e->getMessage(),
                );
            }
        });
    }
}
