<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $email = array('customercare@medlinx.co.id','izidokid@gmail.com','product.izidok@gmail.com');
        if (in_array(Auth::user()->email, $email)) {
            if (substr($request->bearerToken(), 10,20) != Auth::user()->last_session) {
                abort(469);
            }
        }
        return $next($request);
    }
}
