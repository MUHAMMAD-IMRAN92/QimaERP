<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Governerate;
use App\LoginUser;
use App\Province;
use App\Village;
use App\User;

class AuthController extends Controller {

    /**
     * Login user.
     *
     * @return \Illuminate\Http\Response
     */
    function login(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $auth = auth()->guard('web');

        if ($auth->attempt(['password' => $request->password, 'email' => $request->email])) {
            $user = Auth::user();
            if ($user->hasRole(['super admin', 'admin'])) {
                Auth::logout();
                return sendError('You are blocked by admin', 400);
            }
            $user = User::where('user_id', Auth::user()->user_id)->with('roles')->first();
            // return sendSuccess('Logged In', $user);
            $session = $this->saveLoginUserDetail($user->user_id);
            $user->session_key = $session->session_key;
            return sendSuccess('Logged In', $user);
        } else {
            return sendError('Invalid email or password', 400);
        }
    }

    /**
     * save user session key here .
     * $user_id loginuser id
     * @return \Illuminate\Http\Response
     */
    private function saveLoginUserDetail($user_id) {
        $newLoginUser = new LoginUser();
        $newLoginUser->session_key = bcrypt($user_id);
        $newLoginUser->user_id = $user_id;

        $newLoginUser->save();
        return $newLoginUser;
    }

    /**
     * add new governorate.
     * @return \Illuminate\Http\Response
     */
    function addGovernerate(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'governerate_code' => 'required|unique:governerates,governerate_code',
                    'governerate_title' => 'required|unique:governerates,governerate_title',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
//::create new 
        $governerate = Governerate::create([
                    'governerate_code' => $request['governerate_code'],
                    'governerate_title' => $request['governerate_title'],
        ]);

        return sendSuccess('Governerate was created Successfully', $governerate);
    }

    function governerate(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $search = $request->search;
        $governerates = Governerate::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('governerate_title', 'like', "%$search%")->orwhere('governerate_code', 'like', "%$search%");
                    });
                })->skip($skip)->take($take)->orderBy('governerate_title')->get();
        return sendSuccess('Successfully retrieved Governerate', $governerates);
    }

    function villages(Request $request) {

        $villages = Village::OrderBy('village_name', 'asc')->get();
        return sendSuccess('Successfully retrieved villages', $villages);
    }

    function provinces(Request $request) {

        $provinces = Province::OrderBy('province_name', 'asc')->get();
        return sendSuccess('Successfully retrieved provinces', $provinces);
    }

}
