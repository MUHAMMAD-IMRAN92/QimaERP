<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Center;
use App\ResetPassword;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {

        return view('admin.user.alluser');
    }

    function getUserAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'user_id';
        //::count total record
        $total_members = User::where('user_id', '!=', Auth::user()->user_id)->count();
        $members = User::query();
        //::select columns
        $members = $members->select('user_id', 'first_name', 'last_name', 'email')->where('user_id', '!=', Auth::user()->user_id);
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function ($q) use ($search) {
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
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'last_name';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'email';
            } else {
                $column = 'first_name';
            }
        }
        $members = $members->skip($start)->take($length)->orderBy($column, $orderby)->with('roles')->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }

    public function adduser()
    {
        $data['role'] = Role::get();
        $data['center'] = Center::all();
        return view('admin.user.addnewuser', $data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);
        // dd($request->all());
        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        // dd($user);
        $user->save();
        DB::table('model_has_roles')->insert([
            'role_id' => $request->role_id,
            'model_id' => $user->user_id,
            'model_type' => 'App\User'
        ]);
        $data = User::where('user_id', $user->user_id)->with('roles')->first();
        if ($request->center_id) {


            DB::table('center_users')->insert([
                'center_id' => $request->center_id,
                'user_id' => $user->user_id,
                'role_name' => $data->roles['0']->name,
            ]);
        }
        return redirect('admin/allusers')->with('message', 'User Create Successfully');
    }

    public function edit($id)
    {
        $data['role'] = Role::get();
        $data['user'] = User::find($id);
        return view('admin.user.edituser', $data);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $updateuser = User::find($request->user_id);
        $updateuser->first_name = $request->first_name;
        $updateuser->last_name = $request->last_name;
        $updateuser->email = $request->email;
        $updateuser->update();

        DB::table('model_has_roles')->where('model_id', $request->user_id)->delete();


        $updateuser->assignRole($request->input('roles'));

        return redirect('admin/allusers')->with('update', 'User Update Successfully');
    }

    public function resetpassword($id)
    {

        $data['reset'] = User::find($id);
        // dd($data['reset']);
        return view('admin.user.Resetpassword', $data);
    }

    public function updatepassword(Request $request)
    {
        // $pass=hash::make($request->password);
        // dd($pass);
        $user = User::find($request->user_id);
        $hashedPassword = Auth::user()->password;

        if (Hash::check($request->oldpassword, $hashedPassword)) {

            if (!Hash::check($request->newpassword, $hashedPassword)) {

                $user = User::find(Auth::user()->user_id);
                $user->password = bcrypt($request->newpassword);
                User::where('user_id', Auth::user()->user_id)->update(array('password' => $user->password));

                session()->flash('message', 'password updated successfully');
                return redirect()->back();
            } else {
                session()->flash('new', 'new password can not be the old password!');
                return redirect()->back();
            }
        } else {
            session()->flash('old', 'old password doesnt matched ');
            return redirect()->back();
        }


        $user->update();
        return redirect('view');
    }

    public function delete(Request $request, $id)
    {
        User::where('user_id', $id)->delete();
    }
    public function resetView($id)
    {
        $user = User::find($id);
        return view('admin.reset_password', [
            'user' => $user
        ]);
    }
    public function postReset(Request $request, $id)
    {
        $user = User::find($id);
        $resetPassObj = ResetPassword::where('email', $user->email)->where('status', 1)->first();
        $newPass = $request->password;
        $confirmPass = $request->cnfpassword;
        if ($newPass == $confirmPass) {
            $user->update([
                'password' => Hash::make($confirmPass),
            ]);
            $resetPassObj->update([
                'status' => 0
            ]);
            return redirect()->route('dashboard')->with('msg', 'Password Update Successfully');
        } else {
            return back()->with('msg', 'Password Does Not Matched');
        }
    }
}
