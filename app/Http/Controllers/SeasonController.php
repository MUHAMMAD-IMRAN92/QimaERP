<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Season;
use App\BatchNumber;
use App\Farmer;

class SeasonController extends Controller
{
    public function index()
    {

        return view('admin.season.allseason');
    }


    public function addseason()
    {

        return view('admin.season.addnewseason');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'season_title' => 'required',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'status' => 'required',
        ]);
        // dd($request->all());
        $season = new Season();
        $season->season_title = $request->season_title;
        $season->start_date = date('Y-m-d', strtotime($request->start_date));
        if ($request->end_date != '') {
            $season->end_date = date('Y-m-d', strtotime($request->end_date));
        }
        $season->status = $request->status;
        $season->save();
        return redirect('admin/allseason');
    }

    function getSeasonAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'ASC';
        $column = 'season_id';
        //::count total record
        $total_members = Season::count();
        $members = Season::query();
        //::select columns
        $members = $members->select('season_id', 'season_title', 'start_date', 'end_date');
        //::search with season_title or start_date or  end_date
        $members = $members->when($search, function ($q) use ($search) {
            $q->where('season_title', 'like', "%$search%")->orWhere('start_date', 'like', "%$search%")->orWhere('end_date', 'like', "%$search%");
        });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'season_title';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'start_date';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 3) {
                $column = 'end_date';
            } else {
                $column = 'season_title';
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



    public function edit($id)
    {
        $data['season'] = Season::find($id);
        // dd($data);
        return view('admin/season/editseason', $data);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'season_title' => 'required',
            'start_date' => 'required',
            'status' => 'required',
        ]);
        $seasonupdate = Season::find($request->season_id);
        // dd($seasonupdate);
        $seasonupdate->season_title = $request->season_title;
        $seasonupdate->start_date = date('Y-m-d', strtotime($request->start_date));
        if ($request->end_date != '') {
            $seasonupdate->end_date = date('Y-m-d', strtotime($request->end_date));
        }
        $seasonupdate->status = $request->status;
        $seasonupdate->update();
        return view('admin.season.allseason');
        // dd($seasonupdate);
    }

    public function delete($id)
    {
        die();
        $destroy = Season::find($id);
        // dd($destroy);
        $destroy->delete();
        return redirect()->back()->with('destroy', 'Season deleted Successfully!');
    }

    public function seasonclose($id)
    {
        // dd($id);
        $seasonend = Season::find($id);
        $seasonend->status = 1;
        $seasonend->save();
        $conatiner = BatchNumber::where('season_id', $id)->get();
        foreach ($conatiner as $member) {
            $member->season_status = 1;
            $member->save();
        }
        return redirect()->back()->with('close', 'Season Close Successfully!');
    }
    public function endSeason($id)
    {
        $farmer = Farmer::find($id);
        $farmer->update([
            'season_no' => $farmer->season_no + 1,
        ]);
        return back()->with('msg', 'Current Season of this farmer has ended successfully!');
    }
}
