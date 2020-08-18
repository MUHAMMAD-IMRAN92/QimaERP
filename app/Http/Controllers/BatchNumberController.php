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

    function getbatchAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'batch_id';
//::count total record
        $total_members = BatchNumber::count();
        $members = BatchNumber::query();
        //::select columns
        $members = $members->select('batch_id', 'batch_number');
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function($q)use ($search) {
                    $q->where('batch_number', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'batch_number';
            }else {
                $column = 'batch_id';
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
