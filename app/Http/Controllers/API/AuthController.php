<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Governerate;
use App\Region;
use App\LoginUser;
use App\Province;
use App\Village;
use App\User;
use App\Transaction;

class AuthController extends Controller
{

    private $app_lang;

    public function __construct()
    {
        set_time_limit(0);
        $headers = getallheaders();
        if (isset($headers['app_lang'])) {
            $this->app_lang = $headers['app_lang'];
        } else {
            $this->app_lang = 'en';
        }
    }

    /**
     * Login user.
     *
     * @return \Illuminate\Http\Response
     */
    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        $auth = auth()->guard('web');
        $center = '';
        if ($auth->attempt(['password' => $request->password, 'email' => $request->email])) {
            $user = Auth::user();
            if ($user->hasRole(['super admin', 'admin'])) {
                Auth::logout();
                return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.BLOCKED"), 400);
            }
            $user = User::where('user_id', Auth::user()->user_id)->with('roles', 'center_user')->first();
            $user->center = null;
            if (isset($user->center_user) && isset($user->center_user->center_id)) {
                $user->center_id = $user->center_user->center_id;
                $user->center = $user->center_user->center;
            }
            $user->makeHidden('center_user');
            $user->session_no = 1;
            $sessionNo = Transaction::where('created_by', Auth::user()->user_id)->orderBy('local_session_no', 'desc')->first();
            if ($sessionNo) {
                $user->session_no = ($sessionNo->local_session_no + 1);;
            }
            // return sendSuccess('Logged In', $user);
            $session = $this->saveLoginUserDetail($user->user_id);
            $user->session_key = $session->session_key;
            return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.LOGIN"), $user);
        } else {
            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.INVALID_USER"), 400);
        }
    }

    /**
     * save user session key here .
     * $user_id loginuser id
     * @return \Illuminate\Http\Response
     */
    private function saveLoginUserDetail($user_id)
    {
        $newLoginUser = new LoginUser();
        $newLoginUser->session_key = bcrypt($user_id);
        $newLoginUser->user_id = $user_id;

        $newLoginUser->save();
        return $newLoginUser;
    }
}
