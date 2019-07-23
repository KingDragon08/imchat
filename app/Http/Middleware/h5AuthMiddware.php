<?php

namespace App\Http\Middleware;

use Closure;
use Cache;

class h5AuthMiddware
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
        $id = session('id', null);
        $token = session('token', null);
        if (empty($token) || empty($id)) {
            return redirect('h5/login');
        }
        if (Cache::get($id) != $token) {
            return redirect('h5/login');
        }
        return $next($request);
    }
}
