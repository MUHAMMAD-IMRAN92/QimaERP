<?php

namespace App\Http\Controllers;

use App\Farmer;
use App\Region;
use App\Village;
use App\Transaction;
use Facade\FlareClient\Stacktrace\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{

    public function index()
    {
        $data = Village::all();
        return view('admin.village.allvillage', compact('data'));
    }

    function getVillageAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'DESC';
        $column = 'village_id';
        //::count total record
        $total_members = Village::count();
        $members = Village::query();
        //::select columns
        $members = $members->select('village_id', 'village_code', 'village_title', 'village_title_ar');
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function ($q) use ($search) {
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
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 2) {
                $column = 'village_title';
            } else {
                $column = 'village_code';
            }
        }
        $members = $members->skip($start)->take($length)->orderBy($column, $orderby)->get();
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $members,
        );
        //:: return json
        return json_encode($data);
    }

    public function addnewvillage()
    {
        $data['region'] = Region::all();
        return view('admin.village.addvillage', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_code' => 'required|max:100|unique:villages,village_code',
            'village_title' => 'required|max:100|unique:villages,village_title',
            'village_title_ar' => 'required|max:100|unique:villages,village_title_ar',

        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // $lastVillage = Village::orderBy('created_at', 'DESC')->first();
        // if (isset($lastVillage) && $lastVillage) {
        //     $currentVillageCode = ($lastVillage->village_id + 1);
        // }

        $max_id = Village::max('village_id');

        $village = new Village();
        $village->village_code = $request->region_code . '-' . sprintf("%02d", ($max_id + 1));
        $village->village_title = $request->village_title;
        $village->village_title_ar = $request->village_title_ar;
        $village->created_by = Auth::user()->user_id;
        $village->price_per_kg = $request->price_per_kg;

        $village->local_code = '';
        // dd($village->village_id);
        $village->save();
        return redirect('admin/allvillage');
    }

    public function edit($id)
    {
        // dd($id);
        $data['village'] = Village::find($id);
        $code = $data['village']->village_code;
        $data['farmers'] = Farmer::where('farmer_code', 'LIKE', $code . '%')->get();
        $data['transaction'] = Transaction::where('batch_number', 'LIKE', $code . '%')->get();
        return view('admin.village.editvillage', $data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'village_title' => 'required|max:100',
            'village_title_ar' => 'required|max:100',
            'village_id' => 'required',
        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $v_code = $request->code . '-' . $request->village_code;
        $r_village = Village::find($request->village_id);
        if ($r_village->village_code != $v_code) {
            $village = Village::where('village_code', 'LIKE', '%' . $request->village_code . '%')->first();
            if ($village) {
                return back()->with('msg', 'Village Code Already Exit');
            }
        }

        // $alreadyvillage = Village::where('village_code', $request->code . '-' . $request->village_code)->first();
        if ($r_village) {
            $updatevillage = Village::find($request->village_id);
            $code = $updatevillage->village_code;
            $farmers = Farmer::where('farmer_code', 'LIKE', $code . '%')->get();
            foreach ($farmers as $farmer) {
                $code = $farmer->farmer_code;
                $arr =  explode('-', $code);
                $newFarmerCode = data_set($arr, [2], $request->village_code);
                $newFarmerCode =  implode('-', $newFarmerCode);
                $farmer->update([
                    'farmer_code' => $newFarmerCode
                ]);
            }
            $updatevillage->village_title = $request->village_title;
            $updatevillage->village_title_ar = $request->village_title_ar;
            $updatevillage->price_per_kg = $request->price_per_kg;
            $updatevillage->village_code = $request->code . '-' . $request->village_code;

            // dd($updatevillage);
            $updatevillage->update();
            return back()->with('msg', 'Village Update Successfully!');
        }
    }
    public function villageProfile(Village $village)
    {
        $village = $village->gov_region();
        $villageCode = $village->village_code;
        $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
        if ($village->first_purchase) {
            $village->first_purchase = $village->first_purchase['created_at'];
        }
        $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
        if ($village->last_purchase) {
            $village->last_purchase = $village->last_purchase['created_at'];
        }
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->get();
        $quantity = 0;
        foreach ($transactions as $transaction) {
            $quantity += $transaction->details->sum('container_weight');
        }
        $village->quantity = $quantity;
        $price = 0;
        foreach ($transactions as $transaction) {
            $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

            $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first();
            if ($farmerPrice) {
                $farmerPrice =  $farmerPrice['price_per_kg'];
            }
            if (!$farmerPrice) {

                $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                $vilagePrice = Village::where('village_code', $villageCode)->first();
                if ($vilagePrice) {
                    $vilagePrice =  $vilagePrice->price_per_kg;
                }
                foreach ($transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $vilagePrice;
                }
            } else {
                foreach ($transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $farmerPrice;
                }
            }
        }
        $village->price = $price;

        return   view('admin.village.village_profile', [
            'village' =>  $village,
        ]);
    }
    public function filter_village_profile(Request $request, $id)
    {
        $village = Village::find($id);

        $villageCode = $village->village_code;
        $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
        if ($village->first_purchase) {
            $village->first_purchase = $village->first_purchase->created_at;
        }
        $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first()['created_at'];
        if ($village->last_purchase) {
            $village->last_purchase = $village->last_purchase->created_at;
        }
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereBetween('created_at', [$request->from, $request->to])->get();
        $quantity = 0;
        foreach ($transactions as $transaction) {
            $quantity += $transaction->details->sum('container_weight');
        }
        $village->quantity = $quantity;
        $price = 0;
        foreach ($transactions as $transaction) {
            $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

            $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
            if (!$farmerPrice) {

                $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                foreach ($transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $vilagePrice;
                }
            } else {
                foreach ($transactions as $transaction) {
                    $quantity = $transaction->details->sum('container_weight');
                    $price +=  $quantity * $farmerPrice;
                }
            }
        }
        $village->price = $price;

        return   view('admin.village.views.filter_transctions', [
            'village' =>  $village,
        ])->render();
    }
    public function village_profile_days_filter(Request $request, $id)
    {

        if ($request->date == 'today') {

            $date = Carbon::today()->toDateString();

            $village = Village::find($id);
            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->where('created_at', $date)->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();

            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->where('created_at', $yesterday)->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'weekToDate') {
            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY);
            $end = $now->endOfWeek(Carbon::SATURDAY);

            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereBetween('created_at', [$start, $end])->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'monthToDate') {
            $now = Carbon::now();
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->firstOfMonth();
            $farmer = Farmer::find($id);
            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereBetween('created_at', [$start, $date])->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'lastmonth') {
            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;
            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'yearToDate') {
            $now = Carbon::now();
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->startOfYear();
            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereBetween('created_at', [$start, $date])->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'currentyear') {
            $date = Carbon::now();

            $year = $date->year;
            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereYear('created_at', $year)->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        } elseif ($request->date == 'lastyear') {
            $date = Carbon::now();


            $year = $date->year - 1;
            $village = Village::find($id);

            $villageCode = $village->village_code;
            $village->first_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->first();
            if ($village->first_purchase) {
                $village->first_purchase = $village->first_purchase->created_at;
            }
            $village->last_purchase =  Transaction::where('batch_number', 'LIKE', $villageCode . '-' . '%')->latest()->first();
            if ($village->last_purchase) {
                $village->last_purchase = $village->last_purchase->created_at;
            }

            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $villageCode . '-' . '%')->where('batch_number', 'NOT LIKE', '%000%')->where('sent_to', 2)->whereYear('created_at', $year)->get();
            $quantity = 0;
            foreach ($transactions as $transaction) {
                $quantity += $transaction->details->sum('container_weight');
            }
            $village->quantity = $quantity;
            $price = 0;
            foreach ($transactions as $transaction) {
                $farmerCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2] . '-' . explode('-', $transaction->batch_number)[3];

                $farmerPrice = Farmer::where('farmer_code', $farmerCode)->first()['price_per_kg'];
                if (!$farmerPrice) {

                    $villageCode = explode('-', $transaction->batch_number)[0] . '-' . explode('-', $transaction->batch_number)[1] . '-' . explode('-', $transaction->batch_number)[2];
                    $vilagePrice = Village::where('village_code', $villageCode)->first()->price_per_kg;
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $vilagePrice;
                    }
                } else {
                    foreach ($transactions as $transaction) {
                        $quantity = $transaction->details->sum('container_weight');
                        $price +=  $quantity * $farmerPrice;
                    }
                }
            }
            $village->price = $price;

            return   view('admin.village.views.filter_transctions', [
                'village' =>  $village,
            ])->render();
        }
    }
}
