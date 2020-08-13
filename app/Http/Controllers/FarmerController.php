<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Farmer;
class FarmerController extends Controller
{
    public function index(){
    	$data['farmer']=Farmer::all();
    	return view('admin.allfarmer',$data);
    }
}
