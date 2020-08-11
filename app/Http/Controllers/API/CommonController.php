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
use App\BatchNumber;
use App\Transaction;
use App\TransactionDetail;
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
        $search = $request->search;
        $governerates = Governerate::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('governerate_title', 'like', "%$search%")->orwhere('governerate_code', 'like', "%$search%");
                    });
                })->orderBy('governerate_title')->get();
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
        $search = $request->search;
        $regions = Region::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('region_title', 'like', "%$search%")->orwhere('region_code', 'like', "%$search%");
                    });
                })->orderBy('region_title')->get();
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
            if ($village->is_local == '1') {
                $code = explode("-", $village->village_code);
                $explodeCode = $code[0] . '-' . $code[1];
                $systemExist = Village::Where('local_code', 'like', "%$explodeCode%")->where('village_title', $village->village_title)->first();
                if (!$systemExist) {
                    $twoDigitCode = sprintf("%02d", ($currentVillageCode));
                    //::create new 
                    $village = Village::create([
                                'village_code' => $explodeCode . '-' . $twoDigitCode,
                                'village_title' => $village->village_title,
                                'created_by' => $village->created_by,
                                'is_local' => FALSE,
                                'local_code' => $village->local_code,
                    ]);
                    $currentVillageCode++;
                } else {
                    $systemExist->local_code = $systemExist->local_code . ',' . $village->local_code;
                    $systemExist->save();
                }
            }
        }
        return sendSuccess('Village was created Successfully', []);
    }

    function villages(Request $request) {
        $search = $request->search;
        $villages = Village::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('village_title', 'like', "%$search%")->orwhere('village_code', 'like', "%$search%");
                    });
                })->orderBy('village_title')->get();
        return sendSuccess('Successfully retrieved villages', $villages);
    }

    function addFarmer(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'farmer_name' => 'required|max:100',
                    'farmer_nicn' => 'required',
                    'created_by' => 'required',
                    'village_code' => 'required',
                    'local_code' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }

        $farmer = Farmer::where('farmer_nicn', $request['farmer_nicn'])->first();
        if (!$farmer) {
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
                $request->file('idcard_picture')->storeAs('images', $file_name);
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
            $currentFarmerCode = sprintf("%03d", $currentFarmerCode);
            $village = Village::where('local_code', 'like', "%$request->village_code%")->first();
//::create new 
            $farmer = Farmer::create([
                        'farmer_code' => $village->village_code . '-' . $currentFarmerCode,
                        'farmer_name' => $request['farmer_name'],
                        'village_code' => $request['village_code'],
                        'picture_id' => $profileImageId,
                        'idcard_picture_id' => $idcardImageId,
                        'farmer_nicn' => $request['farmer_nicn'],
                        'local_code' => $request['local_code'],
                        'is_local' => 0,
                        'created_by' => $request['created_by'],
            ]);
        } else {
            $farmer->local_code = $farmer->local_code . ',' . $request->local_code;
            $farmer->save();
        }
        return sendSuccess('Farmer was created Successfully', $farmer);
    }

    function farmers(Request $request) {
        $search = $request->search;
        $farmers = Farmer::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('farmer_code', 'like', "%$search%")->orwhere('farmer_name', 'like', "%$search%");
                    });
                })->orderBy('farmer_name')->get();
        return sendSuccess('Successfully retrieved farmers', $farmers);
    }

    function addContainer(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'containers' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $containerTypeArray = containerType();
        $containers = json_decode($request['containers']);
        foreach ($containers as $key => $container) {
            $containerTypeCode = preg_replace('/[0-9]+/', '', $container->container_code);
            $containerType = searcharray($containerTypeCode, 'code', $containerTypeArray);
            //::create new 
            $container = Container::create([
                        'container_number' => $container->container_code,
                        'container_type' => $containerType,
                        'capacity' => $container->capacity,
                        'created_by' => $container->created_by,
                        'is_local' => FALSE,
                        'local_code' => $container->local_code,
            ]);
        }
        return sendSuccess('Container was created Successfully', $container);
    }

    function containers(Request $request) {
        $containerNumber = $request->container_number;
        $containers = Container::when($containerNumber, function($q) use ($containerNumber) {
                    $q->where(function($q) use ($containerNumber) {
                        $q->where('container_number', 'like', "%$containerNumber%");
                    });
                })->orderBy('container_number')->get();
        return sendSuccess('Successfully retrieved containera', $containers);
    }

    function addBatchNumberWithTransaction(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'batch_number' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $lastBID = 0;
        $lastTID = 0;
        $lastBatchNumber = BatchNumber::latest('batch_id')->first();
        $lastTransactionNumber = Transaction::latest('transaction_id')->first();
        if ($lastBatchNumber) {
            $lastBID = $lastBatchNumber->batch_id;
        }
        if ($lastTransactionNumber) {
            $lastTID = $lastTransactionNumber->batch_id;
        }
        $batch_numbers = json_decode($request['batch_number']);
        $childBatchNumberArray = array();
        $childTransactionArray = array();
        foreach ($batch_numbers->child_batch as $key => $childBatch) {
            $removeLocalId = explode("-", $childBatch->batch_code);
            array_pop($removeLocalId);
            $farmerCode = implode("-", $removeLocalId) . '_' . $childBatch->created_by;
            $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
            $lastBID = $lastBID + 1;
            $newBatch = BatchNumber::create([
                        'batch_number' => $farmer->farmer_code.'-'.$lastBID,
                        'is_parent' => 0,
                        'created_by' => $childBatch->created_by,
                        'is_local' => FALSE,
                        'local_code' => $childBatch->local_code,
            ]);
        }
        return sendSuccess('Village was created Successfully', $batch_numbers->child_batch);
        var_dump($batch_numbers);
        exit;
        return sendSuccess('Village was created Successfully', []);
    }

}
