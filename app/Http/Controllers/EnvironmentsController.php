<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Environment;
use Auth;

class EnvironmentsController extends Controller {

    public function index() {
        return view('admin.environment.index');
    }

    function getEnvironmentsAjax(Request $request) {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];

//::count total record
        $total_members = Environment::count();
        $members = Environment::query();
        //::select columns
        $members = $members->select('environment_id', 'environment_name');
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function($q)use ($search) {
            $q->where('environment_name', 'like', "%$search%");
        });

        $members = $members->skip($start)->take($length)->orderBy('environment_id', 'DESC')->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }

    public function create() {
        return view('admin.environment.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
                    'environment_name' => 'required|max:100|unique:environments,environment_name',
        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            Environment::create([
                'environment_name' => $request['environment_name'],
            ]);
            \Session::flash('message', 'Environment was created successfully');
            return redirect('admin/environments');
        } catch (\PDOException $e) {
            \Session::flash('message', 'Internal Server Error. Please try again');
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id) {
        $data = array();
        $data['environment'] = Environment::where('environment_id', $id)->first();
        if ($data['environment']) {
            return view('admin.environment.edit', $data);
        } else {
            \Session::flash('message', 'Internal Server Error. Please try again');
            return redirect()->back();
        }
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
                    'environment_name' => "required|max:100|unique:environments,environment_name,$id,environment_id",
        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $environment = Environment::where('environment_id', $id)->first();
        if ($environment) {
            $environment->environment_name = $request['environment_name'];
            $environment->save();
            \Session::flash('message', 'Environment was updated successfully');
            return redirect('admin/environments');
        } else {
            \Session::flash('message', 'Internal Server Error. Please try again');
            return redirect()->back();
        }
    }

}
