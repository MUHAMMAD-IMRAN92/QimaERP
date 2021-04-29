<?php

namespace App\Http\Controllers;

use App\Farmer;
use App\Region;
use App\Village;
use App\FileSystem;
use App\Governerate;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use PhpParser\Node\Stmt\Return_;

class FarmerController extends Controller
{

    public function index()
    {

        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();
        $farmers = Farmer::all();

        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            $farmer->village_title = $farmer->getVillage()->village_title;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();

            $farmer->quantity = $farmer->quntity();
            $farmer->price = $farmer->price()->price_per_kg;

            return $farmer;
        });

        return view('admin.farmer.allfarmer', [
            'farmers' => $farmers,
            'governorates' => $governorates,
            'regions' => $regions,
            'villages' => $villages
        ]);
    }

    function getFarmerAjax(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $orderby = 'DESC';
        $column = 'farmer_id';
        //::count total record
        $total_members = Farmer::count();
        $members = Farmer::query();
        //::select columns
        $members = $members->select('farmer_id', 'farmer_code', 'farmer_name', 'village_code', 'farmer_nicn', 'is_status');
        //::search with farmername or farmer_code or  village_code
        $members = $members->when($search, function ($q) use ($search) {
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
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 4) {
                $column = 'farmer_nicn';
            } elseif (isset($orderBy[0]['column']) && $orderBy[0]['column'] == 5) {
                $column = 'is_status';
            } else {
                $column = 'farmer_code';
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

    public function Edit($id)
    {

        $data['farmer'] = Farmer::find($id);
        return view('admin.farmer.editfarmer', $data);
    }

    public function update(Request $request)
    {
        //  dd($request->all());
        $validatedData = $request->validate([
            'farmer_nicn' => 'required|unique:farmers,farmer_id' . $request->farmer_ids,
        ]);

        $updatefarmer = Farmer::find($request->farmer_id);
        if ($request->profile_picture) {
            $file = $request->profile_picture;
            $originalFileName = $file->getClientOriginalName();
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('public/images', $file_name);
            if ($request->picture_id != '') {
                $userProfileImage = FileSystem::find($request->picture_id);
                $userProfileImage->user_file_name = $file_name;
                $userProfileImage->save();
                // $profileImageId = $userProfileImage->file_id;
            } else {

                $userProfileImage = FileSystem::create([
                    'user_file_name' => $file_name,
                ]);
            }
            $profileImageId = $userProfileImage->file_id;
            $updatefarmer->picture_id = $profileImageId;
        }

        if ($request->idcard_picture) {
            $file = $request->idcard_picture;
            $originalFileName = $file->getClientOriginalName();
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('idcard_picture')->storeAs('images', $file_name);
            if ($request->idcard_picture_id != '') {
                $userIdCardImage = FileSystem::find($request->idcard_picture_id);
                $userIdCardImage->user_file_name = $file_name;
                $userIdCardImage->save();
                // $idcardImageId = $userIdCardImage->file_id;
            } else {

                $userIdCardImage = FileSystem::create([
                    'user_file_name' => $file_name,
                ]);
            }
            $idcardImageId = $userIdCardImage->file_id;
            $updatefarmer->idcard_picture_id = $idcardImageId;
        }

        // dd($updatefarmer);
        $updatefarmer->farmer_name = $request->farmer_name;
        $updatefarmer->farmer_nicn = $request->farmer_nicn;
        $updatefarmer->price_per_kg = $request->price_per_kg;

        $updatefarmer->save();
        Session::flash('updatefarmer', 'farmer was updated Successfully.');
        return redirect('admin/allfarmer');
        //  return view('admin.farmer.allfarmer')->with('updatefarmer', 'farmer detail update Successfully');
    }

    public function updatestatus($id)
    {
        $status = Farmer::find($id);
        if ($status->is_status == '0') {
            $status->is_status = '1';
        } else {
            $status->is_status = '0';
        }
        $status->save();
        return redirect()->back();
    }

    public function create()
    {
        $data['villages'] = Village::all();
        return view('admin.farmer.add_farmer', $data);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'village_code' => 'required',
            'farmer_name' => 'required',
            'farmer_nicn' => 'required',
        ]);
        if ($validator->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $profileImageId = null;
        $idcardImageId = null;
        if ($request->profile_picture) {
            $file = $request->profile_picture;
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('public/images', $file_name);
            $userProfileImage = FileSystem::create([
                'user_file_name' => $file_name,
            ]);
            $profileImageId = $userProfileImage->file_id;
        }

        if ($request->idcard_picture) {
            $file = $request->idcard_picture;
            $id_card_file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('idcard_picture')->storeAs('images', $id_card_file_name);
            $userIdCardImage = FileSystem::create([
                'user_file_name' => $id_card_file_name,
            ]);
            $idcardImageId = $userIdCardImage->file_id;
        }

        // $lastFarmer = Farmer::orderBy('farmer_id', 'desc')->first();
        // $currentFarmerCode = 1;
        // if (isset($lastFarmer) && $lastFarmer) {
        //     $currentFarmerCode = ($lastFarmer->farmer_id + 1);
        // }
        // $currentFarmerCode = sprintf("%03d", $currentFarmerCode);

        $max_id = Farmer::max('farmer_id');

        $code = $request->village_code . '-' . sprintf("%03d", $max_id + 1);
        $alreadyFarmer = Farmer::create([
            'farmer_code' => $code,
            'farmer_name' => $request->farmer_name,
            'village_code' => $request->village_code,
            'picture_id' => $profileImageId,
            'idcard_picture_id' => $idcardImageId,
            'farmer_nicn' => $request->farmer_nicn,
            'local_code' => $code . '_' . Auth::user()->user_id . '-F-' . strtotime("now"),
            'is_local' => 0,
            'is_status' => 1,
            'price_per_kg' => $request->price_per_kg,
            'created_by' => Auth::user()->user_id,
        ]);

        return redirect('admin/allfarmer');
    }

    public function delete(Request $request, $id)
    {
        Farmer::where('farmer_id', $id)->delete();
    }
    public function farmerProfile(Farmer $farmer)
    {


        $governorate = $farmer->getgovernerate();
        $region = $farmer->getRegion();
        $village = $farmer->getVillage();
        $farmer->governerate_title =   $governorate->governerate_title;
        $farmer->region_title = $region->region_title;
        $farmer->village_title = $village->village_title;
        $farmer->first_purchase = $farmer->getfirstTransaction();
        $farmer->last_purchase = $farmer->getlastTransaction();
        $farmer->quantity = $farmer->quntity();
        $farmer->price = $farmer->price()->price_per_kg;
        $farmer = $farmer->transactions();
        $farmer->image = $farmer->getImage();
        $farmer->cnicImage = $farmer->cnic();

        return view('admin.farmer.farmer_profile', [
            'farmer' => $farmer
        ]);
    }
    public function filterByDate(Request $request)
    {
        $farmers = Farmer::whereBetween('created_at', [$request->from, $request->to])
            ->get();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });

        return view('admin.farmer.views.index', compact('farmers'))->render();
    }

    public function fiterByRegion(Request $request)
    {
        $id = $request->from;
        $governorateCode = Governerate::where('governerate_id', $id)->first()->governerate_code;

        $regions = Region::all();

        $govRegions = $regions->filter(function ($region) use ($governorateCode) {
            return explode('-', $region->region_code)[0] == $governorateCode;
        })->values();
        $farmers = Farmer::where('farmer_code', 'LIKE',   $governorateCode . '%')->get();

        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            $farmer->village_title = $farmer->getVillage()->village_title;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();

            $farmer->quantity = $farmer->quntity();
            $farmer->price = $farmer->price()->price_per_kg;

            return $farmer;
        });
        return response()->json([
            'regions' => $govRegions,
            'view' =>  view('admin.farmer.views.index', compact('farmers'))->render(),
        ]);
    }
    public function fiterVillages(Request $request)
    {
        $id = $request->from;

        $regionCode = Region::where('region_id' , $id)->first()->region_code;
        $region_Code = explode('-', $regionCode)[1];
        $villages = Village::all();
        $villages = $villages->filter(function ($village) use ($region_Code) {
            return explode('-', $village->village_code)[1] == $region_Code;
        })->values();
        $farmers = Farmer::where('farmer_code', 'LIKE', $regionCode . '%')->get();
       
        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            $farmer->village_title = $farmer->getVillage()->village_title;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();

            $farmer->quantity = $farmer->quntity();
            $farmer->price = $farmer->price()->price_per_kg;

            return $farmer;
        });
        return response()->json([
            'villages' => $villages,
            'view' =>  view('admin.farmer.views.index', compact('farmers'))->render(),
        ]);
    }
    public function farmerByVillages(Request $request)
    {
        $id = $request->from;
        $villageCode = Village::where('village_id', $id)->first()->village_code;

        $farmers = Farmer::where('village_code', $villageCode)->get();


        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            $farmer->village_title = $farmer->getVillage()->village_title;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();

            $farmer->quantity = $farmer->quntity();
            $farmer->price = $farmer->price()->price_per_kg;

            return $farmer;
        });

        return view('admin.farmer.views.index', compact('farmers'))->render();
    }
    public function famerByDate(Request $request)
    {
        $date = $request->date;
        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

            $farmers = Farmer::whereDate('created_at',  $date)->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });


            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();

            $farmers = Farmer::whereDate('created_at', $yesterday)->get();
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'lastmonth') {

            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;

            $farmers = Farmer::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'currentyear') {

            $date = Carbon::now();


            $year = $date->year;

            $farmers = Farmer::whereYear('created_at', $year)->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'lastyear') {

            $date = Carbon::now();


            $year = $date->year - 1;

            $farmers = Farmer::whereYear('created_at', $year)->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'weekToDate') {

            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY);
            $end = $now->endOfWeek(Carbon::SATURDAY);



            $farmers = Farmer::whereBetween('created_at', [$start, $end])->get();


            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'monthToDate') {

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->firstOfMonth();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'yearToDate') {

            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->startOfYear();

            $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                $farmer->village_title = $farmer->getVillage()->village_title;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
    
                $farmer->quantity = $farmer->quntity();
                $farmer->price = $farmer->price()->price_per_kg;
    
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        }
    }
    public function filter_farmer_profile(Request $request, $id)
    {
        $farmer = Farmer::find($id);

        $farmerCode = $farmer->farmer_code;
        $farmer->price = $farmer->price()->price_per_kg;
        $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
        $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$request->from, $request->to])
            ->first()['created_at'];
        $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$request->from, $request->to])
            ->latest()->first()['created_at'];
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
            ->whereBetween('created_at', [$request->from, $request->to])
            ->where('sent_to', 2)
            ->get();
        $sum = 0;
        foreach ($transactions as $transaction) {
            $sum += $transaction->details->sum('container_weight');
        }
        $farmer->quantity = $sum;
        $farmer->price = $farmer->price()->price_per_kg;

        return view('admin.farmer.views.filter_transctions', [
            'farmer' => $farmer
        ])->render();
    }
    public function filter_farmer_profile_by_date(Request $request, $id)
    {
        if ($request->date == 'today') {
            $date = Carbon::today()->toDateString();
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;
            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->where('created_at', $date)
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->where('created_at', $date)
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->where('created_at', $date)
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;
            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'yesterday') {
            $now = Carbon::now();
            $yesterday = Carbon::yesterday();
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;

            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->where('created_at', $yesterday)
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->where('created_at', $yesterday)
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->where('created_at', $yesterday)
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;;

            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'weekToDate') {
            $now = Carbon::now();
            $start = $now->startOfWeek(Carbon::SUNDAY);
            $end = $now->endOfWeek(Carbon::SATURDAY);

            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;

            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$start, $end])
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$start, $end])
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->whereBetween('created_at', [$start, $end])
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;
            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'monthToDate') {
            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->firstOfMonth();
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;


            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$start, $date])
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$start, $date])
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->whereBetween('created_at', [$start, $date])
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;

            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'lastmonth') {
            $date = Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;


            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;
            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'yearToDate') {
            $now = Carbon::now();
            $date = Carbon::today()->toDateString();
            $start = $now->startOfYear();
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;


            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$start, $date])
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$start, $date])
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->whereBetween('created_at', [$start, $date])
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;
            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'currentyear') {
            $date = Carbon::now();

            $year = $date->year;
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;


            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereYear('created_at', $year)
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereYear('created_at', $year)
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->whereYear('created_at', $year)
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;
            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        } elseif ($request->date == 'lastyear') {
            $date = Carbon::now();


            $year = $date->year - 1;
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;


            $farmer->price = Village::where('village_code', $farmerCode)->first()['price_per_kg'];
            $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereYear('created_at', $year)
                ->first()['created_at'];
            $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereYear('created_at', $year)
                ->latest()->first()['created_at'];
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', "$farmerCode%")
                ->whereYear('created_at', $year)
                ->where('sent_to', 2)
                ->get();
            $sum = 0;
            foreach ($transactions as $transaction) {
                $sum += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $sum;
            $farmer->price = $farmer->price()->price_per_kg;
            return view('admin.farmer.views.filter_transctions', [
                'farmer' => $farmer
            ])->render();
        }
    }
}
