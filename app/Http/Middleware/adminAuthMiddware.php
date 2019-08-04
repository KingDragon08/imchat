<?php

namespace App\Http\Middleware;

use Closure;
use Cache;

class adminAuthMiddware
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
            return redirect('admin/login');
        }
        if (Cache::get($id) != $token) {
            return redirect('admin/login');
        }
        return $next($request);
    }
}
