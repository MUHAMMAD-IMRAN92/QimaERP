<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class checkAppKey {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $headers = getallheaders();
        if ($headers['app_key'] == 'P3dvmoVlG/7V7soppeHO1a8T0+B/f0PsxTvupV+eems=') {
//            if(isset($headers['session_token'])){
//        $checksession = LoginUsers::where('session_key', $headers['session_token'])->first();
//        if ($checksession) {
//            $user = User::find($checksession->user_id);            
//            Auth::login($user);
//            return $next($request);
//            }}    
            return $next($request);
        } else {
//            return $headers;
            return sendError('You Are Not Autherize For App', 401);
        }
    }

}
