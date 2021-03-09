<?php

namespace App\Http\Middleware;

use Closure;

class checkAppKey
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
        $appKey = 'P3dvmoVlG/7V7soppeHO1a8T0+B/f0PsxTvupV+eems=';
        $appKeyHeader = $request->header('x-app-key');

        if ($appKeyHeader == $appKey) {
            return $next($request);
        } else {
            return sendError('You Are Not Autherize For App', 401);
        }
    }
}
