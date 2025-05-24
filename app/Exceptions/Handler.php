<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
            // Custom reporting logic can be implemented here
        });
        
        // Convert exceptions to API responses for API requests
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($request, $e);
            }
            
            return null;
        });
    }
    
    /**
     * Handle API exceptions and return proper JSON responses
     */
    private function handleApiException(Request $request, Throwable $exception)
    {
        // Log the exception with request details for better debugging
        Log::channel('api')->error('API Exception', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_id' => $request->user()->user_id ?? 'unauthenticated',
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
        
        // Handle different types of exceptions with appropriate responses
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        if ($exception instanceof AuthorizationException) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }
        
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Resource not found',
            ], 404);
        }
        
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'The given data was invalid',
                'errors' => $exception->errors(),
            ], 422);
        }
        
        if ($exception instanceof QueryException) {
            // Don't expose SQL errors to client
            return response()->json([
                'message' => 'Database error',
            ], 500);
        }
        
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'API endpoint not found',
            ], 404);
        }
        
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'message' => 'Method not allowed',
            ], 405);
        }
        
        if ($exception instanceof HttpException) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'HTTP error',
            ], $exception->getStatusCode());
        }
        
        // Generic error response for all other exceptions
        if (config('app.debug')) {
            // In debug mode, include detailed error info
            return response()->json([
                'message' => 'Server error',
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ], 500);
        }
        
        // Production error response without sensitive details
        return response()->json([
            'message' => 'Server error',
        ], 500);
    }
}
