<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Center;
use App\CenterUser;
use Spatie\Permission\Models\Role;
class CenterController extends Controller
{
    public function index(){

    	return view('admin.center.allcenter');
    }

    public function Addcenterdetail(){
        $data['Center']=Center::all();
        $data['role'] = Role::with('users.center_user.center')->whereNotIn('name', ['Coffee Buyer','Super Admin','Coffee Buying Manager'])->get();
        return view('admin.center.addcenterdetail',$data);
    }

    public function addnewcenter(){
    	$data['role'] = Role::with('users.center_user.center')->whereNotIn('name', ['Coffee Buyer','Super Admin','Coffee Buying Manager'])->get();
    	return view('admin.center.addnewcenter',$data);
    }

    public function centerdetail($id){
        $data['detail']=Center::find($id);
        $data['userrole']=User::whereHas('center_user', function($q) use($id) {
                    $q->where('center_id','=', $id);
                })->with('roles')->get();
        // $data['role'] = Role::whereDoesntHave("users", function($subQuery) {
        //                 $subQuery->whereIn('name', ['Coffee Buyer','Super Admin','Coffee Buying Manager']);
        //               })->get();

        $data['role'] = Role::with('users.center_user.center')->whereNotIn('name', ['Coffee Buyer','Super Admin','Coffee Buying Manager'])->get();
        // $data['center_users'] = CenterUser::where('center_id',$id)->where('role_name','Center Manager')->pluck('user_id')->toArray();
        return view('admin.center.centerdetail',$data);
        // dd($data['detail']);
    }

    public function updatecenterrole(Request $request){
        //  dd($request->all());
        // if(CenterUser::where('user_id',$request->center_manager_id)){
        //     echo "id match";
        // }else{
        //     echo "pass";
        // }
        $validatedData = $request->validate([
                'role' => 'required',
                    ]);
       
        $userId=$request->role;
        $centerid=$request->center_id;
       
        // print_r($data);exit();
            CenterUser::where('center_id',$request->center_id)->delete();
            foreach ($userId as $rowid) {
                $data = User::where('user_id',$rowid)->with('roles')->first();
                $centeruser =New CenterUser;
                $centeruser->center_id=$centerid;
                $centeruser->user_id=$rowid;
                $centeruser->role_name=$data->roles['0']->name;
                $centeruser->save();
                // dd($centeruser);
            }
        return redirect()->back()->with('message','Role Updated Successfully');
    }

    public function storecenter(Request $request){
    	 $validatedData = $request->validate([
        'center_code' => 'required|unique:centers',
        'center_name' => 'required|unique:centers',
    ]);
    	$center = new Center;
    	$center->center_code=$request->center_code;
    	$center->center_name=$request->center_name;
    	// $center->center_manager_id=$request->center_manager_id;
    	 // dd($center);
    	$center->save();

        // $userId=$request->center_manager_id;
        // $centerid=$center->center_id;

        // foreach ($userId as $rowid) {
        //     $centeruser = New CenterUser;
        //     $centeruser->center_id=$centerid;
        //     $centeruser->user_id=$rowid;
        //     $centeruser->role_name='Center Manager';
        //     $centeruser->save();
        // }

        // $centeruser = new CenterUser;
        // $centeruser->center_id=$centerid
        // $centeruser->user_id=$userId;
        // $centeruser->role_name='Center Manager';
        // $centeruser->save();
        // User::whereIn('user_id', $userId)->update(['table_id' => $centerid ,'table_name' => 'center']);
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

    public function edit($id){

        $data['center']=Center::find($id);
        $data['user'] = User::role('Center Manager')->get();
        $data['center_users'] = CenterUser::where('center_id',$id)->where('role_name','Center Manager')->pluck('user_id')->toArray();
        return view('admin.center.editcenter',$data);
        // dd($center);
    }


    public function update(Request $request){

        $validatedData = $request->validate([
        'center_name' => 'required',
        'center_manager_id' => 'required',
    ]);
        $centerupdate=Center::find($request->center_id);
        $centerupdate->center_code=$request->center_code;
        $centerupdate->center_name=$request->center_name;
        $centerupdate->save();

        // $userId=$request->center_manager_id;
        // // dd($userId);
        // $centerid=$centerupdate->center_id;
        // foreach ($userId as $rowid) {
        //     $centeruser = New CenterUser;
        //     $centeruser->center_id=$centerid;
        //     $centeruser->user_id=$rowid;
        //     $centeruser->role_name='Center Manager';
        //     $centeruser->save();
        // }
        // User::whereIn('user_id', $userId)->update(['table_id' => $centerid ,'table_name' => 'center']);
        return redirect('admin/allcenter');
    }
}
