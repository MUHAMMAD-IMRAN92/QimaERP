<?php

namespace App\Http\Controllers;

use App\Center;
use App\Farmer;
use App\Region;
use App\Village;
use App\Governerate;
use App\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{

    public function index()
    {
        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();
        $transactions = Transaction::with('details')->where('sent_to', 2)->get();
        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first()->price_per_kg;
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }

        $governorates = $governorates->map(function ($governorate) {
            $governorateCode = $governorate->governerate_code;

            $governorate->regions = Region::where('region_code', 'LIKE', $governorateCode . '%')->get();

            foreach ($governorate->regions as $region) {
                $regionCode = $region->region_code;
                $governorate->villages = Village::where('village_code', 'LIKE', $regionCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $village->village_code . '%')->where('sent_to', 2)->get();
                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $village->weight =   $weight;

                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                        $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first()->price_per_kg;
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first()->price_per_kg;
                        }
                        $farmers = Farmer::where('farmer_code', 'LIKE',  $village->village_code . '%')->get();
                        $village->farmers = count($farmers);
                        return $governorate;
                    }
                    return  $governorate;
                }
                return $governorate;
            }

            return $governorate;
        });

        return view('admin.region.allregion', [
            'governorates' =>   $governorates,
            'regions' => $regions,
            'villages' => $villages,
            'total_coffee' => $totalWeight,
            'totalPrice' => $totalPrice

        ]);
    }

    public function addnewregion()
    {
        $data['governor'] = Governerate::all();
        $data['center'] = Center::all();
        return view('admin.region.addnewregion', $data);
    }

    function getRegionAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'DESC';
        $column = 'region_id';
        //::count total record
        $total_members = Region::count();
        $members = Region::query();
        //::select columns
        $members = $members->select('region_id', 'region_code', 'region_title');
        //::search with farmername or farmer_code or  region_code
        $members = $members->when($search, function ($q) use ($search) {
            $q->where('region_code', 'like', "%$search%")->orWhere('region_title', 'like', "%$search%");
        });
        if ($request->has('order') && !is_null($request['order'])) {
            $orderBy = $request->get('order');
            $orderby = 'asc';
            if (isset($orderBy[0]['dir'])) {
                $orderby = $orderBy[0]['dir'];
            }
            if (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 1) {
                $column = 'region_code';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'region_title';
            } else {
                $column = 'region_code';
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_code' => 'required|max:100|unique:regions,region_code',
            'region_title' => 'required|max:100|unique:regions,region_title',
        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $region = new Region;
        $region->region_code = $request->governerate_code . '-' . $request->region_code;
        $region->region_title = $request->region_title;
        $region->center_id = $request->center_id;
        $region->local_code = '';
        $region->is_local = 0;
        $region->created_by = Auth::user()->user_id;
        // dd($region);
        $region->save();
        Session::flash('message', 'Region Has Been Added Successfully.');
        return redirect('admin/allregion');
    }

    public function edit($id)
    {
        $data['center'] = Center::all();
        $data['region'] = Region::find($id);
        return view('admin.region.editregion', $data);
    }

    public function delete($id)
    {
        $region = Region::find($id);
        $region->delete();
        Session::flash('message', 'Region Has Been Deleted Successfully.');
        return redirect('admin/allregion');
    }
    public function regionByDate(Request $request)
    {
        $governorates = Governerate::whereBetween('created_at', [$request->from, $request->to])->get();
        $regions = Region::whereBetween('created_at', [$request->from, $request->to])->get();
        $villages = Village::whereBetween('created_at', [$request->from, $request->to])->get();
        $transactions = Transaction::with('details')->where('sent_to', 2)->whereBetween('created_at', [$request->from, $request->to])->get();

        $totalWeight = 0;
        $totalPrice = 0;
        foreach ($transactions as $transaction) {
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = optional(Farmer::where('farmer_code', $farmer_code)->first())->price_per_kg;
            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');
                $price = Village::where('village_code',  $village_code)->first()->price_per_kg;
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first()->price_per_kg;
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }

        return view('admin.region.views.filter_transctions', [
            'governorates' =>   $governorates,
            'regions' => $regions,
            'villages' => $villages,
            'total_coffee' => $totalWeight,
            'totalPrice' => $totalPrice

        ]);
    }
}
