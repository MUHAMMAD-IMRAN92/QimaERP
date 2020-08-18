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

    function getGovernorAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'governerate_id';
//::count total record
        $total_members = Governerate::count();
        $members = Governerate::query();
        //::select columns
        $members = $members->select('governerate_id', 'governerate_code', 'governerate_title');
        //::search with farmername or farmer_code or  governerate_code
        $members = $members->when($search, function($q)use ($search) {
                    $q->where('governerate_code', 'like', "%$search%")->orWhere('governerate_title', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'governerate_code';
            }elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'governerate_title';
            }else {
                $column = 'governerate_id';
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

