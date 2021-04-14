<?php

namespace App\Http\Controllers;

use App\Farmer;
use App\Region;
use App\Village;
use App\FileSystem;
use App\Governerate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FarmerController extends Controller
{

    public function index()
    {

        $governorates = Governerate::all();
        $regions = Region::all();
        $villages = Village::all();
        $farmers = Farmer::all();
        $farmers =  $farmers->map(function ($farmer) {
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->village_title = $farmer->getVillage()->village_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->image = $farmer->getImage();
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
        // dd($request->all());
        $validatedData = $request->validate([
            'farmer_nicn' => 'required|unique:farmers,farmer_id' . $request->farmer_ids,
        ]);

        $updatefarmer = Farmer::find($request->farmer_id);
        if ($request->profile_picture) {
            $file = $request->profile_picture;
            $originalFileName = $file->getClientOriginalName();
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('images', $file_name);
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
            $request->file('profile_picture')->storeAs('images', $file_name);
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
        $farmer->image = $farmer->getImage();
        return view('admin.farmer.farmer_profile', [
            'farmer' => $farmer
        ]);
    }
    public function filterByDate(Request $request)
    {
        $farmers = Farmer::whereBetween('created_at', [$request->from, $request->to])
            ->get();
        $farmers =  $farmers->map(function ($farmer) {
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->village_title = $farmer->getVillage()->village_title;
            return $farmer;
        });

        $farmers = $farmers->map(function ($farmer) {
            $farmer->image = $farmer->getImage();
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
        });


        return  $govRegions;
    }
    public function fiterVillages(Request $request)
    {
        $id = $request->from;
        $regionCode = Region::where('region_id', $id)->first()->region_code;
        $regionCode = explode('-', $regionCode)[1];

        $villages = Village::all();
        $regVillage = $villages->filter(function ($village) use ($regionCode) {
            return explode('-', $village->village_code)[1] == $regionCode;
        });

        return response()->json([
            'villages' => $regVillage,

        ]);
    }
    public function farmerByVillages(Request $request)
    {
        $id = $request->from;
        $villageCode = Village::where('village_id', $id)->first()->village_code;

        $farmers = Farmer::where('village_code', $villageCode)->get();


        $farmers =  $farmers->map(function ($farmer) {
            $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->region_title = $farmer->getRegion()->region_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->village_title = $farmer->getVillage()->village_title;
            return $farmer;
        });
        $farmers = $farmers->map(function ($farmer) {
            $farmer->image = $farmer->getImage();
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
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'yesterday') {
            $date = Carbon::now()->subDays(30)->toDateTimeString();
            $farmers = Farmer::whereDate('created_at', $date)->get();
            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'lastmonth') {

            $date = \Carbon\Carbon::now();

            $lastMonth =  $date->subMonth()->format('m');
            $year = $date->year;

            $farmers = Farmer::whereMonth('created_at', $lastMonth)->whereYear('created_at', $year)->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
                return $farmer;
            });
            return view('admin.farmer.allfarmer', [
                'farmers' => $farmers,
                'governorates' => $governorates,
                'regions' => $regions,
                'villages' => $villages

            ]);
        } elseif ($date == 'currentyear') {

            $date = \Carbon\Carbon::now();


            $year = $date->year;

            $farmers = Farmer::whereYear('created_at', $year)->get();

            $governorates = Governerate::all();
            $regions = Region::all();
            $villages = Village::all();
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
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
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
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
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
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
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
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
            $farmers =  $farmers->map(function ($farmer) {
                $farmer->governerate_title = $farmer->getgovernerate()->governerate_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->region_title = $farmer->getRegion()->region_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->village_title = $farmer->getVillage()->village_title;
                return $farmer;
            });
            $farmers = $farmers->map(function ($farmer) {
                $farmer->image = $farmer->getImage();
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
}
