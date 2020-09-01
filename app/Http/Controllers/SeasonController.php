<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index(){

    	return view('admin.season.allseason');
    }


    public function addseason(){

    	return view('admin.season.addnewseason');
    }

    public function store(Request $request){
    	dd($request->all());
    }
}
