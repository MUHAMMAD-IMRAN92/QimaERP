<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BatchNumber;
class BatchNumberController extends Controller
{
    public function index(){
    	$data['batch']=BatchNumber::where('is_parent', '0')->get();
    	return view('admin.allbatchnumber',$data);
    }
}
