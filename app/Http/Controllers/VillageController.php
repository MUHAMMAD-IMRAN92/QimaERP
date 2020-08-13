<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Village;
class VillageController extends Controller
{
    public function index(){
    	$data['village']=Village::all();
    	return view('admin.allvillage',$data);
    }
}
