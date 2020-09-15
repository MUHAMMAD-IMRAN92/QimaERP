<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Farmer;
use App\FileSystem;
class FarmerController extends Controller
{
    public function index(){
    	$data['farmer']=Farmer::all();
    	return view('admin.farmer.allfarmer',$data);
    }

   function getFarmerAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'farmer_id';
//::count total record
        $total_members = Farmer::count();
        $members = Farmer::query();
        //::select columns
        $members = $members->select('farmer_id', 'farmer_code', 'farmer_name', 'village_code', 'farmer_nicn');
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function($q)use ($search) {
                    $q->where('farmer_name', 'like', "%$search%")->orWhere('farmer_code', 'like', "%$search%")->orWhere('village_code', 'like', "%$search%")->orWhere('farmer_nicn', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'farmer_code';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'farmer_name';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 3) {
                $column = 'village_code';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 3) {
                $column = 'farmer_nicn';
            
            } else {
                $column = 'farmer_code';
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

    public function Edit($id){
       
        $data['farmer']=Farmer::find($id);
        return view('admin.farmer.editfarmer',$data);
    }

    public function update(Request $request){
        $validatedData = $request->validate([
                'farmer_nicn' => 'required|unique:farmers',
                    ]);
        if($request->picture_id != ''){
            $updateprofile=FileSystem::find($request->picture_id);
            if ($request->profile_picture) {
                $file = $request->profile_picture;
                $originalFileName = $file->getClientOriginalName();
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $request->file('profile_picture')->storeAs('images', $file_name);
                $updateprofile->user_file_name = $file_name;
            }
            $updateprofile->save();
             $profileImageId = $updateprofile->file_id;

        }else{

            if ($request->profile_picture) {
                $file = $request->profile_picture;
                $originalFileName = $file->getClientOriginalName();
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $request->file('profile_picture')->storeAs('images', $file_name);
                $userProfileImage = FileSystem::create([
                            'user_file_name' => $file_name,
                ]);
                $profileImageId = $userProfileImage->file_id;
            }


        }
            if($request->idcard_picture_id != ''){

            $updateid=FileSystem::find($request->idcard_picture_id);
            if ($request->idcard_picture) {
                $file = $request->idcard_picture;
                $originalFileName = $file->getClientOriginalName();
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $request->file('idcard_picture')->storeAs('images', $file_name);
                $updateid->user_file_name = $file_name;
            }
            $updateid->save();
             $idcardImageId = $updateid->file_id;

            }else{
                if ($request->idcard_picture) {
                $file = $request->idcard_picture;
                $originalFileName = $file->getClientOriginalName();
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $request->file('idcard_picture')->storeAs('images', $file_name);
                $userIdCardImage = FileSystem::create([
                            'user_file_name' => $file_name,
                ]);
                $idcardImageId = $userIdCardImage->file_id;
            }

            }
            
        $updatefarmer=Farmer::find($request->farmer_id);
        // dd($updatefarmer);
        $updatefarmer->farmer_name=$request->farmer_name;
        $updatefarmer->farmer_nicn=$request->farmer_nicn;
        if (FileSystem::find($request->profile_picture)) {
             $updatefarmer->picture_id=$profileImageId;
        }
       
        if (FileSystem::find($request->idcard_picture)) {
             $updatefarmer->idcard_picture_id= $idcardImageId;
        }
       
        $updatefarmer->save();
        return view('admin.farmer.allfarmer')->with('updatefarmer','farmer detail update Successfully');
    }
}
