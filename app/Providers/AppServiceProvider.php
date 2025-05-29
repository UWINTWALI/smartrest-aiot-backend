<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set up global error handling and logging
        $this->setupErrorLogging();
    }
    
    /**
     * Set up global error logging for the application.
     */
    private function setupErrorLogging(): void
    {
        try {
            // Log all SQL queries with bindings in local environment
            if (app()->environment('local')) {
                \DB::listen(function ($query) {
                    Log::debug(
                        'SQL Query', 
                        [
                            'sql' => $query->sql,
                            'bindings' => $query->bindings,
                            'time' => $query->time . 'ms'
                        ]
                    );
                });
            }
            
            // Log database query errors (only if database connection is available)
            if ($this->isDatabaseAvailable()) {
                \DB::connection()->getPdo()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }
            
            // Simple error logging implementation
            app('events')->listen('eloquent.query:error', function ($event, $params) {
                Log::channel('api')->error('Database Query Error', [
                    'sql' => $params['query'] ?? null,
                    'bindings' => $params['bindings'] ?? [],
                    'error' => $params['exception']->getMessage() ?? 'Unknown error'
            ]);
        });
        } catch (\Exception $e) {
            // Silently handle database connection errors during CI/CD or when DB is not available
            Log::info('Database connection not available during service provider boot: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if database connection is available.
     */
    private function isDatabaseAvailable(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
