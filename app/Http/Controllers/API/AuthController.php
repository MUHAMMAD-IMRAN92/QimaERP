<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Region;
use App\Village;
use App\Province;
use App\LoginUser;
use App\Governerate;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
    function login_old(Request $request)
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

            return response()->json(['session no' => $sessionNo]);

            if ($sessionNo) {
                $user->session_no = ($sessionNo->local_session_no + 1);
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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            // 'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // throw ValidationException::withMessages([
            //     'email' => ['The provided credentials are incorrect.'],
            // ]);

            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.INVALID_USER"), 400);
        }

        if ($user->hasRole(['super admin', 'admin'])) {
            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.BLOCKED"), 400);
        }

        $user->load(['roles', 'center_user']);

        $user->center = null;

        if (isset($user->center_user) && isset($user->center_user->center_id)) {
            $user->center_id = $user->center_user->center_id;
            $user->center = $user->center_user->center;
        }

        $user->makeHidden('center_user');

        $user->session_no = 1;

        $latestTransaction = Transaction::where('created_by', $user->id)->orderBy('local_session_no', 'desc')->first();

        if ($latestTransaction) {
            $user->session_no = ($latestTransaction->local_session_no + 1);
        }

        $user->token = $user->createToken($request->email)->plainTextToken;

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.LOGIN"), $user);
    }
}
