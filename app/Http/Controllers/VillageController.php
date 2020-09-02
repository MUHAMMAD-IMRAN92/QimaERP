<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Village;
use App\Region;
class VillageController extends Controller
{
    public function index(){
    	$data['village']=Village::all();
    	return view('admin.village.allvillage',$data);
    }

    function getVillageAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'village_id';
//::count total record
        $total_members = Village::count();
        $members = Village::query();
        //::select columns
        $members = $members->select('village_id', 'village_code', 'village_title');
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function($q)use ($search) {
                    $q->where('village_code', 'like', "%$search%")->orWhere('village_title', 'like', "%$search%");
                });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'village_code';
            }elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'village_title';
            }else {
                $column = 'village_code';
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

    public function addnewvillage(){
        $data['region']=Region::all();
        return view('admin.village.addvillage',$data);
    }

    public function store(Request $request){

         $validatedData = $request->validate([
        'region_code' => 'required',
        'village_title' => 'required',
            ]);

        $currentVillageCode = 1;
        $lastVillage = Village::orderBy('created_at', 'q')->first();
        if (isset($lastVillage) && $lastVillage) {
            $currentVillageCode = ($lastVillage->village_id + 1);
        }
        // dd( $currentVillageCode);
        $twoDigitCode = sprintf("%02d", ($currentVillageCode));
        // dd($twoDigitCode);
        // $last= $twoDigitCode+1;
        $village=new Village();
        $village->village_code=$request->region_code.'-'.$twoDigitCode;
        $village->village_title=$request->village_title;
        // dd($village->village_id);
        $village->save();
        return redirect('admin/allvillage');


    }

    public function edit($id){
        // dd($id);
        $data['village']=Village::find($id);
        return view('admin.village.editvillage',$data);
    }


    public function update(Request $request){
        $updatevillage= Village::find($request->village_id);
        $updatevillage->village_title=$request->village_title;
        // dd($updatevillage);
        $updatevillage->update();
        return redirect('admin/allvillage')->with('update', 'Village Update Successfully!');
    }
}
