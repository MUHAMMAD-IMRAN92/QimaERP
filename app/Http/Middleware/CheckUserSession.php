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
        if (isset($headers['app_lang'])) {
            $app_lang = $headers['app_lang'];
        } else {
            $app_lang = 'en';
        }
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();
        if ($checksession) {
            $user = User::find($checksession->user_id);
            if ($user) {
                Auth::login($user);
                return $next($request);
            } else {
                return sendError(Config("statuscodes." . $app_lang . ".error_messages.SESSION_EXPIRED"), 404);
            }
        } else {
            return sendError(Config("statuscodes." . $app_lang . ".error_messages.SESSION_EXPIRED"), 404);
            //return Response::json(array('status' => 'error', 'errorMessage' => 'Session Expired', 'errorCode' => 400), 400);
        }
    }

}
