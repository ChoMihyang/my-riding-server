<?php

namespace App\Http\Middleware;

use Closure;

class ForceJsonResponse
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
        // ForceJsonResponse 모든 응답을 JSON으로 자동 변환하는 미들웨어
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
