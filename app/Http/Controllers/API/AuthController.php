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

    function addRegion(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'region_code' => 'required|unique:regions,region_code',
                    'region_title' => 'required|unique:regions,region_title',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
//::create new 
        $region = Region::create([
                    'region_code' => $request['region_code'],
                    'region_title' => $request['region_title'],
        ]);

        return sendSuccess('Region was created Successfully', $region);
    }

    function regions(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $search = $request->search;
        $regions = Region::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('region_title', 'like', "%$search%")->orwhere('region_code', 'like', "%$search%");
                    });
                })->skip($skip)->take($take)->orderBy('region_title')->get();
        return sendSuccess('Successfully retrieved region', $regions);
    }

    function addVillage(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'village_title' => 'required|unique:villages,village_title',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $lastVillage = Village::orderBy('created_at', 'desc')->first();

        $villageCode = '01';
        if (isset($lastVillage) && $lastVillage) {
            $length = strlen((string) $lastVillage->village_id);
            if ($length == '1') {
                $villageCode = '0' . ($lastVillage->village_id + 1);
            } else {
                $villageCode = ($lastVillage->village_id + 1);
            }
        }
//::create new 
        $village = Village::create([
                    'village_code' => $villageCode,
                    'village_title' => $request['village_title'],
        ]);

        return sendSuccess('Village was created Successfully', $village);
    }

  

    
     function villages(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $search = $request->search;
        $villages = Village::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('village_title', 'like', "%$search%")->orwhere('village_code', 'like', "%$search%");
                    });
                })->skip($skip)->take($take)->orderBy('village_title')->get();
        return sendSuccess('Successfully retrieved villages', $villages);
    }
    
    function provinces(Request $request) {

        $provinces = Province::OrderBy('province_name', 'asc')->get();
        return sendSuccess('Successfully retrieved provinces', $provinces);
    }

}
