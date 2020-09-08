<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{
    
    public function index(){

    	return view('admin.user.alluser');
    }

     function getUserAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'user_id';
//::count total record
        $total_members = User::count();
        $members = User::query();
        //::select columns
        $members = $members->select('user_id', 'first_name', 'last_name', 'email');
        //::search with farmername or farmer_code or  village_code
       $members = $members->when($search, function($q)use ($search) {
                    $q->where('first_name', 'like', "%$search%")->orWhere('last_name', 'like', "%$search%")->orWhere('email', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'first_name';
            }elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'last_name';
            }elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'email';
            }else {
                $column = 'first_name';
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



    public function adduser(){

    	return view('admin.user.addnewuser');
    }
}
