<?php

namespace App\Http\Middleware;

use Closure;
use Cache;

class AuthMiddware
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
        $token = $request['token'];
        $id = $request['id'];
        if (empty($token) || empty($id)) {
            return response()->json(['status' => 1, 'msg' => '未登录']);
        }
        if (Cache::get($id) != $token) {
            return response()->json(['status' => 1, 'msg' => '未登录']);
        }
        return $next($request);
    }
}
