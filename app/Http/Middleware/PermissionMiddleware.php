<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthorized: Please login first',
                'error' => 'unauthenticated'
            ], 401);
        }

        if (!$request->user()->hasPermission($permission)) {
            return response()->json([
                'message' => 'Access Denied: You do not have permission to perform this action',
                'error' => 'insufficient_permissions'
            ], 403);
        }

        return $next($request);
    }
}