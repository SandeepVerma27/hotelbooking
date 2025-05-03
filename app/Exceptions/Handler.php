<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
            // Optional: you can log custom errors here
        });
    }

    /**
     * Customize response for unauthenticated requests (e.g., auth:api middleware).
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // JWT token handling
        if ($request->expectsJson()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user access. Please log in.',
            ], 401);
        }

        // return parent::unauthenticated($request, $exception);
        // For non-API requests, fallback to the default authentication handling (if applicable)
        return redirect()->guest(route('login'));
    }

    /**
     * Customize response for JWT-related exceptions (Token expired, Invalid token, etc.).
     */
    public function render($request, Throwable $exception)
    {
        // Handle JWT exceptions
        if ($exception instanceof TokenExpiredException) {
            return response()->json([
                'status' => false,
                'message' => 'Token has expired. Please log in again.',
            ], 401);
        } elseif ($exception instanceof TokenInvalidException) {
            return response()->json([
                'status' => false,
                'message' => 'Token is invalid. Unauthorized access.',
            ], 401);
        } elseif ($exception instanceof JWTException) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized user access. Please log in.',
            ], 401);
        }

        // If it's not a JWT-related error, call the parent render method
        return parent::render($request, $exception);
    }

    /**
     * Customize response for validation errors.
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $exception->errors(),
        ], 422);
    }
}
