<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Center;
class CenterController extends Controller
{
    public function index(){

    	return view('admin.allcenter');
    }

    public function addnewcenter(){
    	$data['user'] = User::role('Center Manager')->get();
    	return view('admin.addnewcenter',$data);
    }

    public function storecenter(Request $request){
    	 $validatedData = $request->validate([
        'center_code' => 'required|unique:centers',
        'center_name' => 'required|unique:centers',
    ]);
    	$center = new Center;
    	$center->center_code=$request->center_code;
    	$center->center_name=$request->center_name;
    	$center->center_manager_id=$request->center_manager_id;
    	 // dd($center);
    	$center->save();
    	return redirect('admin/allcenter');
    }

    function getCenterAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'center_id';
//::count total record
        $total_members = Center::count();
        $members = Center::query();
        //::select columns
        $members = $members->select('center_id', 'center_code', 'center_name');
        //::search with farmername or farmer_code or  village_code
       $members = $members->when($search, function($q)use ($search) {
                    $q->where('center_code', 'like', "%$search%")->orWhere('center_name', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'center_code';
            }elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'center_name';
            }else {
                $column = 'center_code';
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
}
