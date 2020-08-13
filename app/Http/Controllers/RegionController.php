<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Governerate;
use App\Region;
use Auth;
use Session;
class RegionController extends Controller
{
    public function index(){
    	$data['region']=Region::all();
    	return view('admin.allregion',$data);
    }

    public function addnewregion(){
    	$data['governor']=Governerate::all();
    	return view('admin.addnewregion',$data);
    }

    public function store(Request $request){
    	 $validatedData = $request->validate([
        'region_code' => 'required|unique:regions',
        'region_title' => 'required|unique:regions',
    ]);
    	$region = new Region;
    	$region->region_code=$request->governerate_code.'-'.$request->region_code;
    	$region->region_title=$request->region_title;
    	$region->created_by=Auth::user()->user_id;
    	// dd($region);
    	$region->save();
    	Session::flash('message', 'Region Has Been Stored Successfully.');
    	return redirect('admin/allregion');
    }

     public function edit($id){
     	// $data['governor']=Governerate::all();
    	$data['region']=Region::find($id);
    	return view('admin.editregion',$data);
    }
     public function delete($id){
     	$region = Region::find($id);
        $region->delete();
        Session::flash('message', 'Region Has Been Deleted Successfully.');  
        return redirect('admin/allregion');
    }
}
