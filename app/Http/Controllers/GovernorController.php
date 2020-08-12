<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Governerate;
use Auth;
class GovernorController extends Controller
{
    public function allgovernor(){
    	$data['governors']=Governerate::all();
    	return view('admin.allgovernor',$data);
    }

    public function addnewgovernor(){
    	return view('admin.addnewgovernor');
    }

    public function store(Request $request){
    	 $validatedData = $request->validate([
        'governerate_code' => 'required|unique:governerates',
        'governerate_title' => 'required|unique:governerates',
    ]);
    	$governor = new Governerate;
    	$governor->governerate_code=$request->governerate_code;
    	$governor->governerate_title=$request->governerate_title;
    	$governor->created_by=Auth::user()->user_id;
    	// dd($governor);
    	$governor->save();
    	return redirect('admin/allgovernor');
    }

    public function edit($id){
    	// dd($id);
    	$data['governor'] = Governerate::find($id);
    	return view('admin/editgovernor',$data);
    }
    public function update(Request $request){
    	$updategovernor = Governerate::find($request->governor_id);
    	$updategovernor->governerate_code=$request->governerate_code;
    	$updategovernor->governerate_title=$request->governerate_title;
    	// dd($updategovernor);
    	$updategovernor->update();
    	return redirect('admin/allgovernor');
    }
    public function delete($id){
    	$governor = Governerate::find($id);
        $governor->delete();
        return redirect('admin/allgovernor');
    }
}

