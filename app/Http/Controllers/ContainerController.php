<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Container;
use Auth;
use Carbon\Carbon;
class ContainerController extends Controller
{
    public function index(){
    	$data['container']=Container::all();
    	return view('admin.container.allcontainer',$data);
    }

    public function addcontainer(){
    	$data['array'] = containerType();
    	// var_dump($data['array']);
    	// die();
    	// dd($containerTypeArray);
    	return view('admin.container.addcontiner',$data);
    }

    public function store(Request $request){

         $checkcontainer= Container::where('container_number',$request->codetype.$request->number)->first();
            if(!$checkcontainer){
                
            
    	// dd($request->all());
    	// if(Container::where('container_number', '=', $request->codetype.$request->number)->first()){
	    	 $validatedData = $request->validate([
                'container_type' => 'required',
                'number' => 'required',
                'capacity' => 'required',
		    ]);
			$current_timestamp = Carbon::now()->timestamp;
			// dd($current_timestamp);
	    	// dd($request->all());
	    	$containerTypeArray = containerType();
	    	 $containerType = searcharray($request->codetype, 'code', $containerTypeArray);
	    	 // var_dump($containerType);
	    	 // exit;
	    	$container=new Container;
	    	$container->container_number=$request->codetype.$request->number;
	    	$container->container_type=$containerType;
	    	$container->capacity=$request->capacity;
	    	$container->created_by=Auth::user()->user_id;
	    	$container->is_local="1";
	    	$container->local_code=$request->codetype.$request->number.'-'.Auth::user()->user_id.'-'.'C'.'-'.$current_timestamp;
    	
    	$container->save();
    	return redirect('admin/allcontainer')->with('success', 'Container Number Added Successfully');

    	}else{
    		
    		return redirect()->back()->with('message', 'Container Number Already Exists');
    	}



    }
}
