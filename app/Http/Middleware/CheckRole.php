<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\User;

class CheckRole
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
        $user = User::where('user_id', Auth::user()->user_id)->with('roles')->first(); {
            if ($user->roles[0]->name == 'Coffee Buying Manager' || $user->roles[0]->name == 'Super Admin') {
                return $next($request);
            }
            Auth::logout();
            return redirect('admin/login')->with('logincheck', 'You Are Not Authorized');
        }
    }
}
