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
    function getRegionAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'region_id';
//::count total record
        $total_members = Region::count();
        $members = Region::query();
        //::select columns
        $members = $members->select('region_id', 'region_code', 'region_title');
        //::search with farmername or farmer_code or  region_code
        $members = $members->when($search, function($q)use ($search) {
                    $q->where('region_code', 'like', "%$search%")->orWhere('region_title', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'region_code';
            }elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'region_title';
            }else {
                $column = 'region_code';
            }
        }
        $members = $members->orderBy($column, $orderby)->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
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
