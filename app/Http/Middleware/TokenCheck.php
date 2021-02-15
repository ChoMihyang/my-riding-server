<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TokenCheck
{
    /**
     * Handle an incoming request.
     * @param  Request $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 유저 토큰 체크
        $user = Auth::guard('api')->user();

        if(!$user) {
            return response()->json([
                "인증오류",
            ],404);
        }
//        dd($user);

        return $next($request);
    }
}
