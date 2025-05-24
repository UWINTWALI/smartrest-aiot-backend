<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip CSRF verification for API routes
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return $next($request);
            }
        }
        
        // For other routes, verify CSRF token
        if ($request->isMethod('GET') || 
            $request->hasHeader('X-CSRF-TOKEN') ||
            $request->has('_token')) {
            return $next($request);
        }
        
        abort(419, 'CSRF token mismatch.');
    }
}
