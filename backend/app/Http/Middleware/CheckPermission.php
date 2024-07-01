<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user() && auth()->user()->permisiune_id == 1) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized', 'user_level' => auth()->user()], 401);
    }
}
