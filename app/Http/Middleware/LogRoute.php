<?php

namespace App\Http\Middleware;

use Closure;
use App\Logs;
use Illuminate\Support\Str;

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
        if ($request->header('Build-Number')) {
            // dd($request->header('Build-Number'));
            $log->build_no = $request->header('Build-Number');
        }
        $log->save();

        return $response;
    }
}
