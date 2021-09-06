<?php

namespace App\Http\Middleware;

use Closure;
use App\logs;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LogRoute
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
        // dd($request->all());
        $response = $next($request);

        // $log = [
        //     'URI' => $request->getUri(),
        //     'METHOD' => $request->getMethod(),
        //     'REQUEST_BODY' => $request->all(),
        //     'RESPONSE' => $response->getContent(),
        //     'seprator' => 'done Here'
        // ];
        // \Log::info($log);
        $log = new Logs();
        $log->method =  $request->getMethod();
        $log->url = Str::afterLast($request->getUri(), '/');
        $log->request = json_encode($request->all());
        $log->response =  $response->getContent();
        $log->save();

        return $response;
    }
}
