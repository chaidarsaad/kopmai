<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsStoreClosed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $store = Store::first();
        if ($store && $store->is_open === false) {
            return response('<script>alert("Maaf Koperasi Sedang Tutup"); window.location.href = "' . route('home') . '";</script>');
        }
        return $next($request);
    }
}
