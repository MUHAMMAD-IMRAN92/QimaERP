<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionLog;
use App\BatchNumber;
use App\Transaction;
use App\Governerate;
use App\FileSystem;
use App\Container;
use App\LoginUser;
use App\Village;
use App\Region;
use App\Farmer;
use App\Center;
use App\User;
use App\Season;
use Storage;

class CommonController extends Controller {

    private $userId;
    private $user;
    private $app_lang;

    public function __construct() {
        set_time_limit(0);
        $headers = getallheaders();
        if (isset($headers['app_lang'])) {
            $this->app_lang = $headers['app_lang'];
        } else {
            $this->app_lang = 'en';
        }
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();
        if ($checksession) {
            $user = User::where('user_id', $checksession->user_id)->with('roles')->first();
            if ($user) {
                $this->user = $user;
                $this->userId = $user->user_id;
            } else {
                return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.SESSION_EXPIRED"), 404);
            }
        } else {
            return sendError(Config("statuscodes." . $this->app_lang . ".error_messages.SESSION_EXPIRED"), 404);
        }
    }

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
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_GOV"), []);
    }

    function governerate(Request $request) {

        $search = $request->search;
        $governerates = Governerate::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('governerate_title', 'like', "%$search%")->orwhere('governerate_code', 'like', "%$search%");
                    });
                })->orderBy('governerate_title')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_GOV"), $governerates);
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
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_REGION"), []);
    }

    function regions(Request $request) {
        $search = $request->search;
        $regions = Region::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('region_title', 'like', "%$search%")->orwhere('region_code', 'like', "%$search%");
                    });
                })->orderBy('region_title')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_REGION"), $regions);
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
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_VILLAGE"), []);
    }

    function villages(Request $request) {
        $search = $request->search;
        $villages = Village::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('village_title', 'like', "%$search%")->orwhere('village_title_ar', 'like', "%$search%")->orwhere('village_code', 'like', "%$search%");
                    });
                })->orderBy('village_title')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_VILLAGE"), $villages);
    }

    function farmers(Request $request) {
        $search = $request->search;
        $farmers = Farmer::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('farmer_code', 'like', "%$search%")->orwhere('farmer_name', 'like', "%$search%");
                    });
                })->orderBy('farmer_name')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_FARMER"), $farmers);
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
        $containersId = array();
        foreach ($containers as $key => $container) {
            $containerTypeCode = preg_replace('/[0-9]+/', '', $container->container_number);
            $containerType = searcharray($containerTypeCode, 'code', $containerTypeArray);
            $alreadyContainer = Container::where('container_number', $container->container_number)->first();
            if (!$alreadyContainer) {
                //::create new 
                $newcontainer = Container::create([
                            'container_number' => $container->container_number,
                            'container_type' => $containerType,
                            'capacity' => $container->capacity,
                            'created_by' => $container->created_by,
                            'is_local' => FALSE,
                            'local_code' => $container->local_code,
                ]);
                array_push($containersId, $newcontainer->container_id);
            } else {
                array_push($containersId, $alreadyContainer->container_id);
            }
        }
        $containersListing = Container::whereIn('container_id', $containersId)->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.ADD_CONTAINER"), $containersListing);
    }

    function containers(Request $request) {
        $containerNumber = $request->container_number;
        $containers = Container::when($containerNumber, function($q) use ($containerNumber) {
                    $q->where(function($q) use ($containerNumber) {
                        $q->where('container_number', 'like', "%$containerNumber%");
                    });
                })->orderBy('container_number')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_CONTAINER"), $containers);
    }

    function transactions(Request $request) {
        $search = $request->search;
        $transactions = Transaction::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('batch_number', 'like', "%$search%");
                    });
                })->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION"), $transactions);
    }

    function transactionsDetails(Request $request) {
        $search = $request->search;
        $transactionsDetails = TransactionDetail::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('container_number', 'like', "%$search%");
                    });
                })->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION_DETAIL"), $transactionsDetails);
    }

    function allBatches(Request $request) {
        $allBatches = array();
        $batches = BatchNumber::all();
        foreach ($batches as $key => $batche) {
            $batche->is_active = FALSE;
            //   $childBatch = $batche->childBatches;
            $batchData = ['batch' => $batche];
            array_push($allBatches, $batchData);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_BATCHES"), $allBatches);
    }

    function getContainerType(Request $request) {
        $containerTypeArray = containerType();
        $container=array();
        foreach ($containerTypeArray as $key => $containerTypeAr) {
           $data['code']=$containerTypeAr['code'];
           array_push($container, $data);
        }
        
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_CONTAINER"), $container);
    }

}
