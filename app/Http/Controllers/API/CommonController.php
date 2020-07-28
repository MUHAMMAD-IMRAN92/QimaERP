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
                            'is_local' => TRUE,
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
                    'region_code' => 'required|unique:regions,region_code',
                    'region_title' => 'required|unique:regions,region_title',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
//::create new 
        $region = Region::create([
                    'region_code' => $request['region_code'],
                    'region_title' => $request['region_title'],
        ]);

        return sendSuccess('Region was created Successfully', $region);
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
                    'village_title' => 'required|unique:villages,village_title',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $lastVillage = Village::orderBy('created_at', 'desc')->first();

        $villageCode = '01';
        if (isset($lastVillage) && $lastVillage) {
            $length = strlen((string) $lastVillage->village_id);
            if ($length == '1') {
                $villageCode = '0' . ($lastVillage->village_id + 1);
            } else {
                $villageCode = ($lastVillage->village_id + 1);
            }
        }
//::create new 
        $village = Village::create([
                    'village_code' => $villageCode,
                    'village_title' => $request['village_title'],
        ]);

        return sendSuccess('Village was created Successfully', $village);
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
            $file = $request->profile_picture;
            $originalFileName = $file->getClientOriginalName();
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $request->file('profile_picture')->storeAs('images', $file_name);
            $userProfileImage = FileSystem::create([
                        'user_file_name' => $originalFileName,
                        'system_file_name' => $file_name,
            ]);
            $profileImageId = $userProfileImage->file_id;
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

        $farmerCode = '001';
        if (isset($lastFarmer) && $lastFarmer) {
            $length = strlen((string) $lastFarmer->farmer_id);
            if ($length == '1') {
                $farmerCode = '00' . ($lastFarmer->farmer_id + 1);
            } elseif ($length == '2') {
                $farmerCode = '0' . ($lastFarmer->farmer_id + 1);
            } else {
                $farmerCode = ($lastFarmer->farmer_id + 1);
            }
        }

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
