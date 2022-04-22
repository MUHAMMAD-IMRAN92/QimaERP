<?php

namespace App\Http\Controllers;

use App\Exports\FarmersExport;
use App\Farmer;
use Illuminate\Support\Str;
use App\Region;
use App\Village;
use App\FileSystem;
use App\Governerate;
use App\Transaction;
use App\TransactionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class FarmerController extends Controller
{

    public function index()
    {

        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();
        // $farmers = Farmer::where('status', 1)->paginate(10);

        // $data['farmers'] = $farmers->map(function ($farmer) {
        //     // $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
        //     // $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
        //     $farmer->image = $farmer->getImage();
        //     // $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
        //     $farmer->first_purchase = $farmer->getfirstTransaction();
        //     $farmer->last_purchase = $farmer->getlastTransaction();
        //     $farmer->quantity = $farmer->quntity();
        //     $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;
        //     $farmer->paidprice = $farmer->paidPriceFromInvoice();

        //     return $farmer;
        // });

        // return $farmers;
        return view('admin.farmer.allfarmer', [
            // 'farmers' => $farmers,
            'governorates' => $governorates,
            'regions' => $regions,
            'villages' => $villages
        ]);
    }
    function getAllFarmers(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->search['value'];
        $total_members = Farmer::where('status', 1)->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('farmer_name', 'like', "%$search%")->orwhere('farmer_code', 'like', "%$search%");
            });
        })->count();
        $faqs = Farmer::where('status', 1)->when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('farmer_name', 'like', "%$search%")->orwhere('farmer_code', 'like', "%$search%");
            });
        })->orderBy('farmer_name', 'asc')->skip((int) $start)->take((int) $length);
        $all_faqs = $faqs->get();
        $all_faqs = $all_faqs->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
            $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
            $farmer->quantity = $farmer->quntity();
            $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;
            $farmer->paidprice = $farmer->paidPriceFromInvoice();
            return $farmer;
        });
        $data = array(
            'draw' => $draw,
            'recordsTotal' => $total_members,
            'recordsFiltered' => $total_members,
            'data' => $all_faqs,
        );
        return json_encode($data);
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
        $code = $data['farmer']->farmer_code;

        $data['transaction'] = Transaction::where('batch_number', 'LIKE', $code . '%')->get();
        return view('admin.farmer.editfarmer', $data);
    }

    public function update(Request $request)
    {
        // $request->all();
        $validatedData =  Validator::make($request->all(), [
            'farmer_nicn' => 'required|unique:farmers,farmer_id' . $request->farmer_ids,
            'farmer_code' => 'numeric',
        ]);
        if ($validatedData->fails()) {
            //::validation failed
            return redirect()->back()->withErrors($validatedData)->withInput();
        }
        $f_code = $request->code . '-'  . $request->farmer_code;
        $updatefarmer = Farmer::findOrFail($request->farmer_id);
        if ($updatefarmer->farmer_code != $f_code) {
            $existingFarmer = Farmer::where('farmer_code', 'LIKE', '%' . $request->farmer_code . '%')->first();
            if ($existingFarmer) {
                return back()->with(['msg' => 'Farmer Code Already Exits']);
            }
        }

        if ($updatefarmer) {
            // $updatefarmer = Farmer::find($request->farmer_id);
            if ($request->profile_picture) {
                $file = $request->profile_picture;
                $originalFileName = $file->getClientOriginalName();
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $path =   $request->file('profile_picture')->storeAs('images', $file_name, 's3');
                Storage::disk('s3')->setVisibility($path, 'public');

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
                $path =   $request->file('idcard_picture')->storeAs('images', $file_name, 's3');
                Storage::disk('s3')->setVisibility($path, 'public');

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
            $updatefarmer->farmer_code = $f_code;
            $updatefarmer->farmer_nicn = $request->farmer_nicn;
            $updatefarmer->price_per_kg = $request->price_per_kg;
            $updatefarmer->ph_no = $request->ph_no;
            $updatefarmer->reward = $request->reward;
            $updatefarmer->cup_profile = $request->cup_prof;
            $updatefarmer->cupping_score = $request->cup_score;
            $updatefarmer->farmer_info = $request->info;
            $updatefarmer->no_of_trees = $request->tree;
            $updatefarmer->house_hold_size = $request->house_hold;
            $updatefarmer->farm_size = $request->farm_size;
            $updatefarmer->altitude = $request->alt;
            $updatefarmer->save();
            //    return redirect('admin/allfarmer');
            return back()->with(['msg' => 'farmer was updated Successfully.']);
        }
        //  else {
        //     // $updatefarmer = Farmer::find($request->farmer_id);
        //     if ($request->profile_picture) {
        //         $file = $request->profile_picture;
        //         $originalFileName = $file->getClientOriginalName();
        //         $file_name = time() . '.' . $file->getClientOriginalExtension();
        //         $request->file('profile_picture')->storeAs('public/images', $file_name);
        //         if ($request->picture_id != '') {
        //             $userProfileImage = FileSystem::find($request->picture_id);
        //             $userProfileImage->user_file_name = $file_name;
        //             $userProfileImage->save();
        //             // $profileImageId = $userProfileImage->file_id;
        //         } else {

        //             $userProfileImage = FileSystem::create([
        //                 'user_file_name' => $file_name,
        //             ]);
        //         }
        //         $profileImageId = $userProfileImage->file_id;
        //         $updatefarmer->picture_id = $profileImageId;
        //     }

        //     if ($request->idcard_picture) {
        //         $file = $request->idcard_picture;
        //         $originalFileName = $file->getClientOriginalName();
        //         $file_name = time() . '.' . $file->getClientOriginalExtension();
        //         $request->file('idcard_picture')->storeAs('images', $file_name);
        //         if ($request->idcard_picture_id != '') {
        //             $userIdCardImage = FileSystem::find($request->idcard_picture_id);
        //             $userIdCardImage->user_file_name = $file_name;
        //             $userIdCardImage->save();
        //             // $idcardImageId = $userIdCardImage->file_id;
        //         } else {

        //             $userIdCardImage = FileSystem::create([
        //                 'user_file_name' => $file_name,
        //             ]);
        //         }
        //         $idcardImageId = $userIdCardImage->file_id;
        //         $updatefarmer->idcard_picture_id = $idcardImageId;
        //     }

        //     // dd($updatefarmer);
        //     $updatefarmer->farmer_name = $request->farmer_name;
        //     $updatefarmer->farmer_code = $request->code . '-'  . $request->farmer_code;
        //     $updatefarmer->farmer_nicn = $request->farmer_nicn;
        //     $updatefarmer->price_per_kg = $request->price_per_kg;

        //     $updatefarmer->save();
        //     Session::flash('updatefarmer', 'farmer was updated Successfully.');
        //     return redirect('admin/allfarmer');
        // }

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
        $data['villages'] = Village::where('status', 1)->get();
        return view('admin.farmer.add_farmer', $data);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'village_code' => 'required',
            'farmer_name' => 'required',
            'farmer_nicn' => 'required | unique:farmers',
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
            $path = $request->file('profile_picture')->storeAs('images', $file_name, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');
            $userProfileImage = FileSystem::create([
                'user_file_name' => $file_name,
            ]);
            $profileImageId = $userProfileImage->file_id;
        }

        if ($request->idcard_picture) {
            $file = $request->idcard_picture;
            $id_card_file_name = time() . '.' . $file->getClientOriginalExtension();
            $path = $request->file('idcard_picture')->storeAs('images', $id_card_file_name, 's3');
            Storage::disk('s3')->setVisibility($path, 'public');

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
            'ph_no' => $request->ph_no,
            'reward' => $request->reward,
            'cup_profile' => $request->cup_prof,
            'cupping_score' => $request->cup_score,
            'farmer_info' => $request->info,
            'no_of_trees' => $request->tree,
            'house_hold_size' => $request->house_hold,
            'farm_size' => $request->farm_size,
            'altitude' => $request->alt,
        ]);

        return redirect('admin/allfarmer');
    }

    public function delete(Request $request, $id)
    {
        Farmer::where('farmer_id', $id)->delete();
    }
    public function farmerProfile($id)
    {

        $farmer = Farmer::find($id);
        $governorate = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
        $region = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
        $village = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
        $farmer->governerate_title =  $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
        $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
        $farmer->village_title =  $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
        $farmer->first_purchase = $farmer->getfirstTransaction();
        $farmer->last_purchase = $farmer->getlastTransaction();
        $farmer->quantity = $farmer->quntity();
        $farmer->price = $farmer->price()->price_per_kg;
        $farmer = $farmer->transactions();
        $farmer->image = $farmer->getImage();
        $farmer->cnicImage = $farmer->cnic();
        $farmer->cropsterReports = $farmer->cropsterReports();

        return view('admin.farmer.farmer_profile', [
            'farmer' => $farmer
        ]);
    }
    public function filterByDate(Request $request)
    {

        $farmers = collect();
        $transactions = Transaction::with('details')->whereBetween('created_at', [$request->from, $request->to])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
            if ($farmer) {
                if (!$farmers->contains($farmer)) {
                    $farmers->push($farmer);
                }
            }
        }
        // $farmers = Farmer::whereBetween('created_at', [$request->from, $request->to])
        //     ->get();
        $farmers = $farmers->map(function ($farmer) use ($request) {
            $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
            $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
            $farmer->paidprice = $farmer->paidPriceFromInvoice();
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->whereBetween('created_at', [$request->from, $request->to])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            $farmer->quantity = $weight;
            $farmer->price = $farmer->price()->price_per_kg;

            return $farmer;
        });

        return view('admin.farmer.views.index', compact('farmers'))->render();
    }

    public function fiterByRegion(Request $request)
    {
        $id = $request->from;
        $governorateCode = Governerate::where('governerate_id', $id)->first()->governerate_code;

        $regions = Region::where('status', 1)->get();

        $govRegions = $regions->filter(function ($region) use ($governorateCode) {
            return explode('-', $region->region_code)[0] == $governorateCode;
        })->values();
        // $farmers = Farmer::where('farmer_code', 'LIKE',   $governorateCode . '%')->get();

        $farmers = collect();
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE',   $governorateCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('farmer_code', $batch_number)->first();
            if ($farmer) {
                if (!$farmers->contains($farmer)) {
                    $farmers->push($farmer);
                }
            }
        }
        $farmers = $farmers->map(function ($farmer) use ($governorateCode) {
            $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
            $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
            $farmer->paidprice = $farmer->paidPriceFromInvoice();
            // $farmer->quantity = $farmer->quntity();
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE',   $governorateCode . '%')->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            $farmer->quantity =   $weight;
            $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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

        $regionCode = Region::where('status', 1)->where('region_id', $id)->first()->region_code;
        $region_Code = explode('-', $regionCode)[1];
        $villages = Village::where('status', 1)->get();
        $villages = $villages->filter(function ($village) use ($region_Code) {
            return explode('-', $village->village_code)[1] == $region_Code;
        })->values();
        // $farmers = Farmer::where('farmer_code', 'LIKE', $regionCode . '%')->get();
        $farmers = collect();
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE',   $regionCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
            if ($farmer) {
                if (!$farmers->contains($farmer)) {
                    $farmers->push($farmer);
                }
            }
        }
        $farmers = $farmers->map(function ($farmer) use ($regionCode) {
            $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
            $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
            $farmer->paidprice = $farmer->paidPriceFromInvoice();
            // $farmer->quantity = $farmer->quntity();
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE',   $regionCode . '%')->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            $farmer->quantity =   $weight;
            $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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
        $villageCode = Village::where('status', 1)->where('village_id', $id)->first()->village_code;

        // $farmers = Farmer::where('village_code', $villageCode)->get();
        $farmers = collect();
        $transactions = Transaction::with('details')->where('batch_number', 'LIKE',   $villageCode . '%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
        foreach ($transactions as $transaction) {
            $batch_number = Str::beforeLast($transaction->batch_number, '-');
            $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
            if ($farmer) {
                if (!$farmers->contains($farmer)) {
                    $farmers->push($farmer);
                }
            }
        }

        $farmers = $farmers->map(function ($farmer) use ($villageCode) {
            $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
            $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
            $farmer->image = $farmer->getImage();
            $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
            $farmer->paidprice = $farmer->paidPriceFromInvoice();
            // $farmer->quantity = $farmer->quntity();
            $transactions = Transaction::with('details')->where('batch_number', 'LIKE',   $villageCode . '%')->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            $weight = 0;
            foreach ($transactions as $transaction) {
                $weight += $transaction->details->sum('container_weight');
            }
            $farmer->quantity =   $weight;
            $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

            return $farmer;
        });

        return view('admin.farmer.views.index', compact('farmers'))->render();
    }
    public function famerByDate(Request $request)
    {
        $date = $request->date;
        if ($date == 'today') {
            $date = Carbon::today()->toDateString();

            // $farmers = Farmer::whereDate('created_at',  $date)->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereDate('created_at',  $date)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($date) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereDate('created_at',  $date)->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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

            // $farmers = Farmer::whereDate('created_at', $yesterday)->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereDate('created_at',  $yesterday)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($yesterday) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereDate('created_at',  $yesterday)->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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

            // $farmers = Farmer::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($lastMonth, $year) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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

            // $farmers = Farmer::whereYear('created_at', $year)->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereYear('created_at', $year)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($year) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereYear('created_at', $year)->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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

            // $farmers = Farmer::whereYear('created_at', $year)->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereYear('created_at', $year)->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($year) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereYear('created_at', $year)->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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
            $start = $now->startOfWeek(Carbon::SATURDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::FRIDAY)->toDateString();



            // $farmers = Farmer::whereBetween('created_at', [$start, $end])->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $end])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($start, $end) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $end])->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->firstOfMonth();

            // $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $date])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($start, $date) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $date])->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->startOfYear();

            // $farmers = Farmer::whereBetween('created_at', [$start, $date])->get();
            $farmers = collect();
            $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $date])->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
            foreach ($transactions as $transaction) {
                $batch_number = Str::beforeLast($transaction->batch_number, '-');
                $farmer = Farmer::where('status', 1)->where('farmer_code', $batch_number)->first();
                if ($farmer) {
                    if (!$farmers->contains($farmer)) {
                        $farmers->push($farmer);
                    }
                }
            }
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();

            $farmers = $farmers->map(function ($farmer) use ($start, $date) {
                $farmer->region_title = $farmer->getRegion() ? $farmer->getRegion()->region_title : null;
                $farmer->village_title = $farmer->getVillage() ? $farmer->getVillage()->village_title : null;
                $farmer->image = $farmer->getImage();
                $farmer->governerate_title = $farmer->getgovernerate() ? $farmer->getgovernerate()->governerate_title : null;
                $farmer->first_purchase = $farmer->getfirstTransaction();
                $farmer->last_purchase = $farmer->getlastTransaction();
                $farmer->paidprice = $farmer->paidPriceFromInvoice();
                // $farmer->quantity = $farmer->quntity();
                $transactions = Transaction::with('details')->whereBetween('created_at', [$start, $date])->where('batch_number', 'LIKE', $farmer->farmer_code . '-%')->where('sent_to', 2)->where('batch_number', 'NOT LIKE', '%000%')->get();
                $weight = 0;
                foreach ($transactions as $transaction) {
                    $weight += $transaction->details->sum('container_weight');
                }
                $farmer->quantity =   $weight;
                $farmer->price = $farmer->price() ? $farmer->price()->price_per_kg : null;

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
        $farmer->price = Village::where('status', 1)->where('village_code', $farmerCode)->first();
        if ($farmer->price) {
            $farmer->price =  $farmer->price['price_per_kg'];
        }
        $farmer->first_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$request->from, $request->to])
            ->first();
        if ($farmer->first_purchase) {
            $farmer->first_purchase =  $farmer->first_purchase['created_at'];
        }
        $farmer->last_purchase = Transaction::with('details')->where('batch_number', 'LIKE',  $farmerCode . '%')->whereBetween('created_at', [$request->from, $request->to])
            ->latest()->first();
        if ($farmer->last_purchase) {
            $farmer->last_purchase =  $farmer->last_purchase['created_at'];
        }
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
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');

            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');

            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $start = $now->startOfWeek(Carbon::SUNDAY)->toDateString();
            $end = $now->endOfWeek(Carbon::SATURDAY)->toDateString();

            $farmer = Farmer::find($id);
            $farmerCode = $farmer->farmer_code;
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');


            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->firstOfMonth();
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');


            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');


            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $date = Carbon::tomorrow()->toDateString();
            $start = $now->startOfYear();
            $farmer = Farmer::find($id);

            $farmerCode = $farmer->farmer_code;
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');


            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');


            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
            $farmerVillageCode = Str::beforeLast($farmer->farmer_code, '-');


            $farmer->price = Village::where('status', 1)->where('village_code', $farmerVillageCode)->first()['price_per_kg'];
            $farmer->first_purchase = $farmer->getfirstTransaction();
            $farmer->last_purchase = $farmer->getlastTransaction();
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
    public function farmerInvoice($id)
    {
        $farmer = Farmer::find($id);
        $inoviceName = [];
        // $farmer->invoice = $farmer->farmerInvoice();
        $farmerCode  = $farmer->farmer_code;
        $transactions = Transaction::where('sent_to', 2)->where('batch_number', 'LIKE',   '%' . $farmerCode . '%')->get();
        foreach ($transactions as $transaction) {
            $transInvoices = TransactionInvoice::where('transaction_id', $transaction->transaction_id)->get();
            foreach ($transInvoices as  $transInvoice) {
                $inovice = $transInvoice->invoice_id;
                if ($file = FileSystem::where('file_id', $inovice)->first()) {
                    $inovice = $file->user_file_name;
                    array_push($inoviceName,  $inovice);
                }
            }
        }

        return view('admin.farmer.views.invoice ', [
            'invoices' =>  $inoviceName
        ])->render();
    }
    public function farmeridCard($id)
    {
        $imageName = null;
        $inoviceName = [];
        $farmer = Farmer::find($id);
        if ($file = FileSystem::where('file_id', $farmer->idcard_picture_id)->first()) {
            $farmer->cnicImage = $file->user_file_name;
            // array_push($farmer->cnicImage,  $imageName);
        }
        return view('admin.farmer.views.idcard ', [
            'farmer' =>  $farmer
        ])->render();
    }
    public function transaction_invoice($id)
    {
        return $id;
    }
    public function download()
    {
        return Excel::download(new FarmersExport, 'farmers.xlsx');
    }
}
