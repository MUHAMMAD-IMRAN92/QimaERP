<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Governerate;
use App\Container;
use App\FileSystem;
use App\Village;
use App\Region;
use App\Farmer;
use Storage;

class CommonController extends Controller {

    /**
     * add new governorate.
     * @return \Illuminate\Http\Response
     */
    function addGovernerate(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'governerates' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $governerates = json_decode($request['governerates']);
        foreach ($governerates as $key => $governerate) {
            $systemExist = Governerate::Where('governerate_code', $governerate->governerate_code)->where('governerate_title', $governerate->governerate_title)->first();
            if (!$systemExist) {
                //::create new 
                $governerate = Governerate::create([
                            'governerate_code' => $governerate->governerate_code,
                            'governerate_title' => $governerate->governerate_title,
                            'created_by' => $governerate->created_by,
                            'is_local' => FALSE,
                            'local_code' => $governerate->local_code,
                ]);
            }
        }
        return sendSuccess('Governerate was created Successfully', []);
    }

    function governerate(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $search = $request->search;
        $governerates = Governerate::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('governerate_title', 'like', "%$search%")->orwhere('governerate_code', 'like', "%$search%");
                    });
                })->skip($skip)->take($take)->orderBy('governerate_title')->get();
        return sendSuccess('Successfully retrieved Governerate', $governerates);
    }

    function addRegion(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'regions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $regions = json_decode($request['regions']);
        foreach ($regions as $key => $region) {
            $systemExist = Region::Where('region_code', $region->region_code)->where('region_title', $region->region_title)->first();
            if (!$systemExist) {
//::create new 
                $region = Region::create([
                            'region_code' => $region->region_code,
                            'region_title' => $region->region_title,
                            'created_by' => $region->created_by,
                            'is_local' => FALSE,
                            'local_code' => $region->local_code,
                ]);
            }
        }
        return sendSuccess('Region was created Successfully', []);
    }

    function regions(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $search = $request->search;
        $regions = Region::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('region_title', 'like', "%$search%")->orwhere('region_code', 'like', "%$search%");
                    });
                })->skip($skip)->take($take)->orderBy('region_title')->get();
        return sendSuccess('Successfully retrieved region', $regions);
    }

    function addVillage(Request $request) {

        //::validation
        $validator = Validator::make($request->all(), [
                    'villages' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $currentVillageCode = 1;
        $lastVillage = Village::orderBy('created_at', 'desc')->first();
        if (isset($lastVillage) && $lastVillage) {
            $currentVillageCode = ($lastVillage->village_id + 1);
        }
        $villages = json_decode($request['villages']);
        foreach ($villages as $key => $village) {
            $systemExist = Village::Where('village_code', 'like', "%$village->village_code%")->where('village_title', $village->village_title)->first();
            if (!$systemExist) {
                $twoDigitCode = sprintf("%02d", ($currentVillageCode));
                //::create new 
                $village = Village::create([
                            'village_code' => $village->village_code . '-' . $twoDigitCode,
                            'village_title' => $village->village_title,
                            'created_by' => $village->created_by,
                            'is_local' => FALSE,
                            'local_code' => $village->local_code,
                ]);
                $currentVillageCode++;
            }
        }
        return sendSuccess('Village was created Successfully', []);
    }

    function villages(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $search = $request->search;
        $villages = Village::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('village_title', 'like', "%$search%")->orwhere('village_code', 'like', "%$search%");
                    });
                })->skip($skip)->take($take)->orderBy('village_title')->get();
        return sendSuccess('Successfully retrieved villages', $villages);
    }

    function addFarmer(Request $request) {

        //::validation
        $validator = Validator::make($request->all(), [
                    'farmer_name' => 'required|max:100',
                    'governerate_code' => 'required|exists:governerates,governerate_code',
                    'region_code' => 'required|exists:regions,region_code',
                    'village_code' => 'required|exists:villages,village_code',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $profileImageId = null;
        $idcardImageId = null;
        if ($request->profile_picture) {
           $ext= (explode(".",$request['profile_picture_system_name']));
   
            $originalFileName = $request['profile_picture_system_name'];
            $file_name = time() . '.' . $ext[1];
             Storage::disk('images')->put($file_name, base64_decode($request['profile_picture']));
           // $imageFileFromB64->file('profile_picture')->storeAs('images', $file_name);
            die("x");
//            $userProfileImage = FileSystem::create([
//                        'user_file_name' => $originalFileName,
//                        'system_file_name' => $file_name,
//            ]);
//            $profileImageId = $userProfileImage->file_id;
        }

        if ($request->idcard_picture) {
            $file = $request->idcard_picture;
            $originalFileName = $file->getClientOriginalName();
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('images', $file_name);
            $userIdCardImage = FileSystem::create([
                        'user_file_name' => $originalFileName,
                        'system_file_name' => $file_name,
            ]);
            $idcardImageId = $userIdCardImage->file_id;
        }
        $lastFarmer = Farmer::orderBy('created_at', 'desc')->first();
        $currentFarmerCode = 1;
        if (isset($lastFarmer) && $lastFarmer) {
            $currentFarmerCode = ($lastFarmer->farmer_id + 1);
        }
        var_dump(sprintf("%03d", $currentFarmerCode));
        exit;
//::create new 
        $farmer = Farmer::create([
                    'farmer_code' => $request['governerate_code'] . '-' . $request['region_code'] . '-' . $request['village_code'] . '-' . $farmerCode,
                    'farmer_name' => $request['farmer_name'],
                    'governerate_code' => $request['governerate_code'],
                    'region_code' => $request['region_code'],
                    'village_code' => $request['village_code'],
                    'picture_id' => $profileImageId,
                    'idcard_picture_id' => $idcardImageId,
        ]);

        return sendSuccess('Farmer was created Successfully', $farmer);
    }

    function addContainer(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'container_type' => ['required', Rule::in(['1', '2', '3', '4', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18'])],
                    'capacity' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $containerTypeArray = containerType();
        $selectedContainerType = $containerTypeArray[$request['container_type']];
        $containerTypeCode = $selectedContainerType['code'];

        $containerNumber = $containerTypeCode . '1';
        $lastContainer = Container::where('container_number', 'like', "%$containerTypeCode%")->orderBy('created_at', 'desc')->first();
        if ($lastContainer) {
            $lastContainerNumber = $lastContainer->container_number;
            $containerNumber = ++$lastContainerNumber;
        }
        //::create new 
        $container = Container::create([
                    'container_number' => $containerNumber,
                    'container_type' => $request['container_type'],
                    'capacity' => $request['capacity'],
        ]);
        return sendSuccess('Container was created Successfully', $container);
    }

    function containers(Request $request) {
        $skip = 0;
        if ($request->skip) {
            $skip = $request->skip * 15;
        }
        $take = 15;
        $containerNumber = $request->container_number;
        $containers = Container::when($containerNumber, function($q) use ($containerNumber) {
                    $q->where(function($q) use ($containerNumber) {
                        $q->where('container_number', 'like', "%$containerNumber%");
                    });
                })->skip($skip)->take($take)->orderBy('container_number')->get();
        return sendSuccess('Successfully retrieved containera', $containers);
    }

}
