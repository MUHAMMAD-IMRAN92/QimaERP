<?php

namespace App\Http\Controllers;

use App\Center;
use App\Farmer;
use App\Region;
use App\Village;
use App\Governerate;
use App\Transaction;
use App\TransactionDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{

    public function index()
    {
        $governorates = Governerate::all();
        $regions = Region::all();
        $farmers = Farmer::all();
        $villages = Village::all();
        $now = Carbon::now();
        $date = Carbon::today()->toDateString();
        $start = $now->firstOfMonth();
        // $chartTransactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE','%000%')
        //     ->get()
        //     ->groupBy(function ($transaction) {
        //         return $transaction->created_at->day;
        //     })->toArray();
        // $weights = TransactionDetail::whereHas('transection', function (Builder $query) {
        //     $query->where('sent_to', 2);
        // })->get()
        //     ->groupBy(function ($detail) {
        //         return $detail->created_at->day;
        //     })->map(function ($detailsGroup, $day) {
        //         $totalWeight = $detailsGroup->sum('container_weight');

        //         return [
        //             'day' => $day,
        //             'weight' => $totalWeight

        //         ];
        //     });



        // for ($i = 1; $i <= 30; $i++) {
        //     $weight= $weights->first(function($weight) use($i){
        //         return $weight['day'] == $i;
        //     });


        // }
        //  ->whereBetween('created_at', [$start, $date])
        $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        $totalWeight = 0;
        $totalPrice = 0;
        $farmerArray = collect();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('farmer_code', $batch_number)->first();
            if ($farmer) {

                $farmerArray->push($farmer->farmer_code);
            }
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }

            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');

                $price = Village::where('village_code',  $village_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }

        $governorates = $governorates->map(function ($governorate) {
            $governorateCode = $governorate->governerate_code;
            $governorate->regions = Region::where('region_code', 'LIKE', $governorateCode . '%')->get();
            $governorate->villages = Village::where('village_code', 'LIKE', $governorateCode . '%')->get();
            foreach ($governorate->villages as $village) {
                $villageCode = $village->village_code;
                $village->farmers = Farmer::where('farmer_code', 'LIKE', $villageCode . '%')->count();
                $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to' , 2)->get();

                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        $village->price  = Village::where('village_code',  $village_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    } else {
                        $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    }
                }
                $village->weight = round($weight, 2);
            }

            return $governorate;
        });

        $regionName = [];
        $regionQuantity = [];
        foreach ($regions as $region) {
            $regionCode = $region->region_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {

                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($regionName, $region->region_title);
            array_push($regionQuantity, $weight);
        }
        $yemenExport = TransactionDetail::whereHas('transaction', function ($q) {
            $q->where('is_parent', 0)
                ->where('sent_to', 39);
        })->sum('container_weight');
        return view('admin.region.allregion', [
            'governorates' =>   $governorates,
            'regions' => $regions,
            'villages' => $villages,
            'farmers' => $farmers,
            'total_coffee' => $totalWeight,
            'totalPrice' => $totalPrice,
            'regionName' => $regionName,
            'regionQuantity' => $regionQuantity,
            'readyForExport' => $yemenExport,
            'farmerCount' => $farmerArray->count(),
            // 'chartTransactions' =>  $chartTransactions

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
        $regions = Region::all();
        // $villages = Village::whereBetween('created_at', [$request->from, $request->to])->get();
        // $farmers = Farmer::whereBetween('created_at', [$request->from, $request->to])->get();
        $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$request->from, $request->to])->get();

        $totalWeight = 0;
        $totalPrice = 0;
        $farmerArray = collect();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('farmer_code', $batch_number)->first();
            if ($farmer) {

                $farmerArray->push($farmer->farmer_code);
            }
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }

            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');

                $price = Village::where('village_code',  $village_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $totalWeight += $weight;
        }
        $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($request) {
            $q->where('is_parent', 0)
                ->where('sent_to', 39)->whereBetween('created_at', [$request->from, $request->to]);
        })->sum('container_weight');
        $regionName = [];
        $regionQuantity = [];
        foreach ($regions as $region) {
            $regionCode = $region->region_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $regionCode . '%')->whereBetween('created_at', [$request->from, $request->to])->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {

                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($regionName, $region->region_title);
            array_push($regionQuantity, $weight);
        }
        $farmerCodes = collect();
        $regionCodes = collect();
        $govCodes = collect();
        $villageCode = collect();
        $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$request->from, $request->to])->get();
        foreach ($transactionsNew as $tran) {
            $batchNumber = $tran->batch_number;
            $bathchArr = explode('-', $batchNumber);
            $gov = array_shift($bathchArr);
            $region = array_shift($bathchArr);
            $village = array_shift($bathchArr);
            $farmer = array_shift($bathchArr);
            if (!$govCodes->contains($gov)) {

                $govCodes->push($gov);
            }
            if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                $regionCodes->push(implode('-', [$gov, $region]));
            }
            if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                $villageCode->push(implode('-', [$gov, $region, $village]));
            }
            if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
            }
        }
        $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
        $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $request) {
            $governorateCode = $governorate->governerate_code;
            $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
            $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
            foreach ($governorate->villages as $village) {
                $villageCode = $village->village_code;
                $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$request->from, $request->to])->get();

                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        $village->price  = Village::where('village_code',  $village_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    } else {
                        $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    }
                }
                $village->weight = round($weight, 2);
            }

            return $governorate;
        });
        // return  $governorates;
        return view('admin.region.views.filter_transctions', [
            'governorates' =>   $governorates,
            'regions' => $regions,
            // 'villages' => $villages,
            // 'farmers' => $farmers,
            'total_coffee' => $totalWeight,
            'totalPrice' => $totalPrice,
            'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
            'regionName' => $regionName,
            'regionQuantity' => $regionQuantity,

        ]);
    }
    public function regionByDays(Request $request)
    {
        $date = $request->date;

        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

            $farmers = Farmer::whereDate('created_at',  $date)->get();
            $villages = Village::whereDate('created_at',  $date)->get();
            $governorates = Governerate::whereDate('created_at',  $date)->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $date)->get();

            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {
                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($date) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereDate('created_at',  $date);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereDate('created_at',  $date)->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $date)->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $date) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereDate('created_at', $date)->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });

            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport,
                'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,

            ]);
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();

            $farmers = Farmer::whereDate('created_at',  $yesterday)->get();
            $villages = Village::whereDate('created_at',  $yesterday)->get();
            $governorates = Governerate::whereDate('created_at',  $yesterday)->get();
            $regions = Region::all();
            $transactions  = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $yesterday)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($yesterday) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereDate('created_at',  $yesterday);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereDate('created_at',  $yesterday)->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $yesterday)->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $yesterday) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereDate('created_at', $yesterday)->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,

            ]);
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;

            $farmers = Farmer::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();

            $villages = Village::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $governorates = Governerate::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($lastMonth,  $year) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $year, $lastMonth) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,
            ]);
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();


            $year = $date->year;


            $farmers = Farmer::whereYear('created_at', $year)->get();

            $villages = Village::whereYear('created_at', $year)->get();
            $governorates = Governerate::whereYear('created_at', $year)->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($year) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereYear('created_at', $year);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereYear('created_at', $year)->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $year) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,
            ]);
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year - 1;

            $farmers = Farmer::whereYear('created_at', $year)->get();
            $farmers = Farmer::whereYear('created_at', $year)->get();

            $villages = Village::whereYear('created_at', $year)->get();
            $governorates = Governerate::whereYear('created_at', $year)->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($year) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereYear('created_at', $year);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereYear('created_at', $year)->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $year) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereYear('created_at', $year)->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,

            ]);
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();



            $farmers = Farmer::whereBetween('created_at', [$start, $end])->get();



            $villages = Village::whereBetween('created_at', [$start, $end])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $end])->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $end])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($start, $end) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereBetween('created_at', [$start, $end]);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereBetween('created_at', [$start, $end])->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $end])->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $start, $end) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $end])->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,

            ]);
        } elseif ($date == 'monthToDate') {

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->firstOfMonth();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();


            $villages = Village::whereBetween('created_at', [$start, $date])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $date])->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');

                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }
                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($start, $date) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereBetween('created_at', [$start, $date]);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereBetween('created_at', [$start, $date])->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $start, $date) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,

            ]);
        } elseif ($date == 'yearToDate') {

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->startOfYear();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();



            $villages = Village::whereBetween('created_at', [$start, $date])->get();
            $governorates = Governerate::whereBetween('created_at', [$start, $date])->get();
            $regions = Region::all();
            $transactions = Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();
            $totalWeight = 0;
            $totalPrice = 0;
            $farmerArray = collect();
            if ($transactions) {

                foreach ($transactions as $transaction) {
                    $batch_number = Str::beforeLast($transaction->batch_number, '-');
                    $farmer = Farmer::where('farmer_code', $batch_number)->first();
                    if ($farmer) {

                        $farmerArray->push($farmer->farmer_code);
                    }
                    $weight = $transaction->details->sum('container_weight');
                    $price = 0;
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }

                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');

                        $price = Village::where('village_code',  $village_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    } else {
                        $price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($price) {
                            $price = $price->price_per_kg;
                        }
                    }

                    $totalPrice += $weight * $price;
                    $totalWeight += $weight;
                }
            }
            $yemenExport = TransactionDetail::whereHas('transaction', function ($q) use ($start, $date) {
                $q->where('is_parent', 0)
                    ->where('sent_to', 39)->whereBetween('created_at', [$start, $date]);
            })->sum('container_weight');
            $regionName = [];
            $regionQuantity = [];
            foreach ($regions as $region) {
                $regionCode = $region->region_code;
                $weight = 0;
                $transactions = Transaction::whereBetween('created_at', [$start, $date])->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
                foreach ($transactions as $transaction) {

                    $weight +=  $transaction->details->sum('container_weight');
                }
                array_push($regionName, $region->region_title);
                array_push($regionQuantity, $weight);
            }
            $farmerCodes = collect();
            $regionCodes = collect();
            $govCodes = collect();
            $villageCode = collect();
            $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();
            foreach ($transactionsNew as $tran) {
                $batchNumber = $tran->batch_number;
                $bathchArr = explode('-', $batchNumber);
                $gov = array_shift($bathchArr);
                $region = array_shift($bathchArr);
                $village = array_shift($bathchArr);
                $farmer = array_shift($bathchArr);
                if (!$govCodes->contains($gov)) {

                    $govCodes->push($gov);
                }
                if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                    $regionCodes->push(implode('-', [$gov, $region]));
                }
                if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                    $villageCode->push(implode('-', [$gov, $region, $village]));
                }
                if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                    $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
                }
            }
            $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
            $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $start, $date) {
                $governorateCode = $governorate->governerate_code;
                $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
                $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
                foreach ($governorate->villages as $village) {
                    $villageCode = $village->village_code;
                    $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                    $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->whereBetween('created_at', [$start, $date])->get();

                    $weight = 0;
                    foreach ($transactions as $transaction) {
                        $weight += $transaction->details->sum('container_weight');
                        $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                        $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($farmerPrice) {
                            $farmerPrice = $farmerPrice->price_per_kg;
                        }
                        if (!$farmerPrice) {
                            $village_code = Str::beforeLast($farmer_code, '-');
                            $village->price  = Village::where('village_code',  $village_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        } else {
                            $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                            if ($village->price) {
                                $village->price =  $village->price->price_per_kg;
                            }
                        }
                    }
                    $village->weight = round($weight, 2);
                }

                return $governorate;
            });
            return view('admin.region.views.filter_transctions', [
                'governorates' =>   $governorates,
                'regions' => $regions,
                'villages' => $villages,
                'farmers' => $farmers,
                'total_coffee' => $totalWeight,
                'totalPrice' => $totalPrice,
                'readyForExport' => $yemenExport, 'farmerCount' => $farmerArray->count(),
                'regionName' => $regionName,
                'regionQuantity' => $regionQuantity,
            ]);
        }
    }
    public function filterRegionByGovernrate(Request $request)
    {
        $id = $request->from;

        $governorate = Governerate::find($id);
        $governorateCode = $governorate->governerate_code;
        $regions = Region::where('region_code', 'LIKE', $governorateCode . '%')->get();
        $governorates = Governerate::all();
        $villages = Village::where('village_code', 'LIKE', $governorateCode . '%')->get();
        $farmers = Farmer::where('farmer_code', 'LIKE', $governorateCode . '%')->get();
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $governorateCode . '%')->where('sent_to', 2)->get();
        $total_coffee = 0;
        $totalPrice = 0;
        $farmerArray = collect();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('farmer_code', $batch_number)->first();
            if ($farmer) {

                $farmerArray->push($farmer->farmer_code);
            }

            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');

            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }

            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');

                $price = Village::where('village_code',  $village_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $total_coffee += $weight;
        }
        $readyForExport = TransactionDetail::whereHas('transaction', function ($q) {
            $q->where('is_parent', 0)
                ->where('sent_to', 39);
        })->sum('container_weight');
        $regionName = [];
        $regionQuantity = [];
        $regionsAll = Region::all();
        foreach ($regionsAll as $region) {
            $regionCode = $region->region_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'LIKE', $governorateCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {

                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($regionName, $region->region_title);
            array_push($regionQuantity, $weight);
        }
        $farmerCount = $farmerArray->count();

        $farmerCodes = collect();
        $regionCodes = collect();
        $govCodes = collect();
        $villageCode = collect();
        $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->where('batch_number', 'LIKE',  $governorateCode . '%')->get();
        foreach ($transactionsNew as $tran) {
            $batchNumber = $tran->batch_number;
            $bathchArr = explode('-', $batchNumber);
            $gov = array_shift($bathchArr);
            $region = array_shift($bathchArr);
            $village = array_shift($bathchArr);
            $farmer = array_shift($bathchArr);
            if (!$govCodes->contains($gov)) {

                $govCodes->push($gov);
            }
            if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                $regionCodes->push(implode('-', [$gov, $region]));
            }
            if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                $villageCode->push(implode('-', [$gov, $region, $village]));
            }
            if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
            }
        }
        $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
        $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes) {
            $governorateCode = $governorate->governerate_code;
            $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $governorateCode . '%')->get();
            $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $governorateCode . '%')->get();
            foreach ($governorate->villages as $village) {
                $villageCode = $village->village_code;
                $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->where('batch_number', 'LIKE',  $governorateCode . '%')->get();

                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        $village->price  = Village::where('village_code',  $village_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    } else {
                        $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    }
                }
                $village->weight = round($weight, 2);
            }

            return $governorate;
        });

        return response()->json([
            'view' => view('admin.region.views.filter_transctions', compact('regionQuantity', 'regionName', 'governorates',  'regions', 'villages', 'farmers',  'total_coffee', 'totalPrice', 'farmerCount', 'readyForExport'))->render(),
            'regions' => $regions
        ]);
    }
    public function filterRegionByRegions(Request $request)
    {
        $id = $request->from;

        $region = Region::find($id);
        $regionCode = $region->region_code;
        $regions = Region::all();
        $villages = Village::where('village_code', 'LIKE', $regionCode . '%')->get();
        $farmers = Farmer::where('farmer_code', 'LIKE', $regionCode . '%')->get();

        $governorates = Governerate::all();

        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $regionCode . '%')->where('sent_to', 2)->get();
        $total_coffee = 0;
        $totalPrice = 0;
        $farmerArray  = collect();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('farmer_code', $batch_number)->first();
            if ($farmer) {

                $farmerArray->push($farmer->farmer_code);
            }

            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');
            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }

            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');

                $price = Village::where('village_code',  $village_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $total_coffee += $weight;
        }
        $readyForExport = TransactionDetail::whereHas('transaction', function ($q) {
            $q->where('is_parent', 0)
                ->where('sent_to', 39);
        })->sum('container_weight');
        $regionName = [];
        $regionQuantity = [];
        foreach ($regions as $region) {
            $region_code = $region->region_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $region_code . '%')->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {

                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($regionName, $region->region_title);
            array_push($regionQuantity, $weight);
        }
        $farmerCount = $farmerArray->count();
        $farmerCodes = collect();
        $regionCodes = collect();
        $govCodes = collect();
        $villageCode = collect();
        $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->where('batch_number', 'LIKE',  $regionCode . '%')->get();
        foreach ($transactionsNew as $tran) {
            $batchNumber = $tran->batch_number;
            $bathchArr = explode('-', $batchNumber);
            $gov = array_shift($bathchArr);
            $region = array_shift($bathchArr);
            $village = array_shift($bathchArr);
            $farmer = array_shift($bathchArr);
            if (!$govCodes->contains($gov)) {

                $govCodes->push($gov);
            }
            if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                $regionCodes->push(implode('-', [$gov, $region]));
            }
            if (!$villageCode->contains(implode('-', [$gov, $region, $village]))) {
                $villageCode->push(implode('-', [$gov, $region, $village]));
            }
            if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
            }
        }
        $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
        $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCode, $farmerCodes, $regionCode) {
            $governorateCode = $governorate->governerate_code;
            $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', $regionCode . '%')->get();
            $governorate->villages = Village::whereIn('village_code', $villageCode)->where('village_code', 'LIKE', $regionCode . '%')->get();
            foreach ($governorate->villages as $village) {
                $villageCode = $village->village_code;
                $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->where('batch_number', 'LIKE',  $regionCode . '%')->get();

                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        $village->price  = Village::where('village_code',  $village_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    } else {
                        $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    }
                }
                $village->weight = round($weight, 2);
            }

            return $governorate;
        });

        return response()->json([
            'view' => view('admin.region.views.filter_transctions', compact('regionQuantity', 'regionName', 'governorates', 'regions', 'villages', 'farmers', 'total_coffee', 'totalPrice', 'readyForExport', 'farmerCount'))->render(),
            'villages' => $villages
        ]);
    }
    public function filterRegionByVillages(Request $request)
    {
        $id = $request->from;

        $village = Village::find($id);
        $villageCode = $village->village_code;
        $farmers = Farmer::all();
        $villages = Village::all();
        $regions = Region::all();
        $governorates = Governerate::all();

        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '%')->where('sent_to', 2)->get();
        $total_coffee = 0;
        $totalPrice = 0;
        $farmerArray = collect();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('farmer_code', $batch_number)->first();
            if ($farmer) {

                $farmerArray->push($farmer->farmer_code);
            }
            $weight = $transaction->details->sum('container_weight');
            $price = 0;
            $farmer_code = Str::beforeLast($transaction->batch_number, '-');
            $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
            if ($farmerPrice) {
                $farmerPrice = $farmerPrice->price_per_kg;
            }

            if (!$farmerPrice) {
                $village_code = Str::beforeLast($farmer_code, '-');

                $price = Village::where('village_code',  $village_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            } else {
                $price = Farmer::where('farmer_code', $farmer_code)->first();
                if ($price) {
                    $price = $price->price_per_kg;
                }
            }

            $totalPrice += $weight * $price;
            $total_coffee += $weight;
        }
        $readyForExport = TransactionDetail::whereHas('transaction', function ($q) {
            $q->where('is_parent', 0)
                ->where('sent_to', 39);
        })->sum('container_weight');
        $regionName = [];
        $regionQuantity = [];
        foreach ($regions as $region) {
            $regionCode = $region->region_code;
            $weight = 0;
            $transactions = Transaction::where('batch_number', 'LIKE', '%' .  $villageCode . '%')->where('batch_number', 'LIKE', '%' .  $regionCode . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->with('details')->get();
            foreach ($transactions as $transaction) {

                $weight +=  $transaction->details->sum('container_weight');
            }
            array_push($regionName, $region->region_title);
            array_push($regionQuantity, $weight);
        }
        $farmerCount = $farmerArray->count();
        $farmerCodes = collect();
        $regionCodes = collect();
        $govCodes = collect();
        $villageCodes = collect();
        $transactionsNew =  Transaction::with('details')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->where('batch_number', 'LIKE',  $villageCode . '%')->get();
        foreach ($transactionsNew as $tran) {
            $batchNumber = $tran->batch_number;
            $bathchArr = explode('-', $batchNumber);
            $gov = array_shift($bathchArr);
            $region = array_shift($bathchArr);
            $village = array_shift($bathchArr);
            $farmer = array_shift($bathchArr);
            if (!$govCodes->contains($gov)) {

                $govCodes->push($gov);
            }
            if (!$regionCodes->contains(implode('-', [$gov, $region]))) {
                $regionCodes->push(implode('-', [$gov, $region]));
            }
            if (!$villageCodes->contains(implode('-', [$gov, $region, $village]))) {
                $villageCodes->push(implode('-', [$gov, $region, $village]));
            }
            if (!$farmerCodes->contains(implode('-', [$gov, $region, $village, $farmer]))) {
                $farmerCodes->push(implode('-', [$gov, $region, $village, $farmer]));
            }
        }
        $governorates = Governerate::whereIn('governerate_code',  $govCodes)->get();
        $governorates = $governorates->map(function ($governorate) use ($regionCodes,   $villageCodes, $farmerCodes, $villageCode) {
            $governorateCode = $governorate->governerate_code;
            $governorate->regions = Region::whereIn('region_code', $regionCodes)->where('region_code', 'LIKE', Str::beforeLast($villageCode, '-') . '%')->get();
            $governorate->villages = Village::whereIn('village_code', $villageCodes)->where('village_code', 'LIKE', $villageCode . '%')->get();
            foreach ($governorate->villages as $village) {
                $villageCode = $village->village_code;
                $village->farmers = Farmer::whereIn('farmer_code', $farmerCodes)->where('farmer_code', 'LIKE', $villageCode . '%')->count();
                $transactions = Transaction::where('batch_number', 'LIKE',  $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();

                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                    $farmer_code = Str::beforeLast($transaction->batch_number, '-');
                    $farmerPrice = Farmer::where('farmer_code', $farmer_code)->first();
                    if ($farmerPrice) {
                        $farmerPrice = $farmerPrice->price_per_kg;
                    }
                    if (!$farmerPrice) {
                        $village_code = Str::beforeLast($farmer_code, '-');
                        $village->price  = Village::where('village_code',  $village_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    } else {
                        $village->price = Farmer::where('farmer_code', $farmer_code)->first();
                        if ($village->price) {
                            $village->price =  $village->price->price_per_kg;
                        }
                    }
                }
                $village->weight = round($weight, 2);
            }

            return $governorate;
        });
        return response()->json([
            'view' => view('admin.region.views.filter_transctions', compact('regionQuantity', 'regionName', 'governorates', 'regions', 'villages', 'farmers', 'total_coffee', 'totalPrice', 'farmerCount', 'readyForExport'))->render(),

        ]);
    }
}
