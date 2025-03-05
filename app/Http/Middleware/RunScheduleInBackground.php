<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class RunScheduleInBackground
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Cache::has('last_schedule_run')) {
            exec('php ' . base_path('artisan') . ' schedule:run > /dev/null 2>/dev/null &');

            Cache::put('last_schedule_run', now(), 60);
        }

        return $next($request);
    }
}
