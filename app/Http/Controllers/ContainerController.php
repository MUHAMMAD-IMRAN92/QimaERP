<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContainerController extends Controller
{
    public function index(){
    	return view('admin.container.allcontainer');
    }

    public function addcontainer(){
    	
    	return view('admin.container.addcontiner');
    }
}
