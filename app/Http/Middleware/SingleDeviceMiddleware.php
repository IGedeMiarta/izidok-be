<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class SingleDeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (substr($request->bearerToken(), 10,20) != Auth::user()->last_session) {
            abort(441);
        }
        return $next($request);
    }
}
