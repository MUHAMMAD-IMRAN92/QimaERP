<?php

namespace App\Http\Controllers\API;

use App\CoffeeSession;
use App\User;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    private $app_lang;

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';
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

        // $user->session_no = 1;

        // $latestTransaction = Transaction::where('created_by', $user->id)->orderBy('local_session_no', 'desc')->first();

        // if ($latestTransaction) {
        //     $user->session_no = ($latestTransaction->local_session_no + 1);
        // }

        $session_no = CoffeeSession::max('server_session_id') ?? 0;

        $user->session_no = $session_no + 1;

        $user->token = $user->createToken($request->email)->plainTextToken;

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.LOGIN"), $user);
    }
}
