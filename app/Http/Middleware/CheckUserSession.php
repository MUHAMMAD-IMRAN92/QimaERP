<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\LoginUser;
use App\User;
use Closure;

class CheckUserSession {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $headers = getallheaders();
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();
        if ($checksession) {
            $user = User::find($checksession->user_id);
            if ($user) {
                Auth::login($user);
                return $next($request);
            } else {
                return sendError('Session Expired', 404);
            }
        } else {
            return sendError('Session Expired', 404);
            //return Response::json(array('status' => 'error', 'errorMessage' => 'Session Expired', 'errorCode' => 400), 400);
        }
    }

}
