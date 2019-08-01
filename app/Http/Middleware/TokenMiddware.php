<?php

namespace App\Http\Middleware;

use Closure;
use Cache;

class TokenMiddware
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
        $token = $request->header('Authentication-Info');
        if (empty($token) || $token != env('THIRD_PART_TOKEN')) {
            return response()->json(['status' => 1, 'msg' => '403']);
        }
        return $next($request);
    }
}
