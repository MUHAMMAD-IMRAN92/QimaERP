<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\LoginUser;
use App\Farmer;
use App\Region;
use App\Village;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\VarDumper\Cloner\Data;

class AuthController extends Controller
{

    public function adminLogin()
    {

        //var_dump(Hash::make("123456"));exit;
        if (Auth::guard()->check()) {
            return redirect('admin/dashboard');
        }
        return view('admin.login');
    }

    public function dashboard()
    {
        $data['farmer'] = Farmer::orderBy('farmer_id', 'DESC')->limit(5)->get();
        $data['village'] = Village::orderBy('village_id', 'DESC')->limit(5)->get();
        $regions = Region::all();
        $regions = $regions->random(3);
        return view('dashboard', $data, compact('regions'));
    }

    public function adminPostLogin(Request $request, $remember = true)
    {
        $email = $request->email;
        $password = $request->password;
        if (Auth::guard()->attempt(['email' => $email, 'password' => $password], $remember)) {
            return redirect('admin/dashboard');
        }
        return redirect()->back()->with('error', 'Invalid email or password');
    }

    public function adminLogout()
    {
        Auth::logout();
        return redirect('admin/login');
    }
    public function byDate(Request $request){
       $data = $request->from;
       dd($data);
    }
}
