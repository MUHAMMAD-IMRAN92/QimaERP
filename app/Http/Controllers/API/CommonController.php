<?php

namespace App\Http\Controllers\API;

use Storage;
use App\User;
use Exception;
use App\Center;
use App\Farmer;
use App\Region;
use App\Season;
use App\Village;
use App\Container;
use App\LoginUser;
use App\FileSystem;
use App\BatchNumber;
use App\Governerate;
use App\Transaction;
use App\ResetPassword;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Mail\reset_password;
use Dotenv\Result\Success;
use Illuminate\Support\Facades\Mail;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommonController extends Controller
{

    private $userId;
    private $user;
    private $app_lang;

    public function __construct(Request $request)
    {
        set_time_limit(0);

        $this->app_lang = $request->header('x-app-lang') ?? 'en';

        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            $this->userId = $request->user()->user_id;

            return $next($request);
        });
    }

    /**
     * add new governorate.
     * @return \Illuminate\Http\Response
     */
    function addGovernerate(Request $request)
    {
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

    function governerate(Request $request)
    {
        $search = $request->search;

        $governerates = Governerate::when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('governerate_title', 'like', "%$search%")->orwhere('governerate_code', 'like', "%$search%");
            });
        })->orderBy('governerate_title')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_GOV"), $governerates);
    }

    function addRegion(Request $request)
    {
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

    function regions(Request $request)
    {
        $search = $request->search;
        $regions = Region::when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('region_title', 'like', "%$search%")->orwhere('region_code', 'like', "%$search%");
            });
        })->orderBy('region_title')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_REGION"), $regions);
    }

    function addVillage(Request $request)
    {
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

    function villages(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles;
        $roleId = 0;
        foreach ($roles as $role) {
            $roleId = $role->id;
        }
        if ($roleId == 2) {
            $user = User::find($user->user_id);
            $villages =  $user->VillagesResposibleFor();
            if (count($villages) == 0) {
                $search = $request->search;
                $villages = Village::when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('village_title', 'like', "%$search%")->orwhere('village_title_ar', 'like', "%$search%")->orwhere('village_code', 'like', "%$search%");
                    });
                })->orderBy('village_title')->get();
            }
        } else {
            $search = $request->search;
            $villages = Village::when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('village_title', 'like', "%$search%")->orwhere('village_title_ar', 'like', "%$search%")->orwhere('village_code', 'like', "%$search%");
                });
            })->orderBy('village_title')->get();
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_VILLAGE"), $villages);
    }

    function farmers(Request $request)
    {
        $user = Auth::user();
        $roles = $user->roles;
        $roleId = 0;
        foreach ($roles as $role) {
            $roleId = $role->id;
        }
        if ($roleId == 2) {
            $user = User::find($user->user_id);
            $villages =  $user->VillagesResposibleFor();
            if (count($villages) == 0) {
                $search = $request->search;
                $farmers = Farmer::when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('farmer_code', 'like', "%$search%")->orwhere('farmer_name', 'like', "%$search%");
                    });
                })->orderBy('farmer_name')->get();
            }
            $farmers = [];
            foreach ($villages as $village) {
                $villagefarmer = Farmer::where('village_code', $village->village_code)->get();
                foreach ($villagefarmer as $farmer) {
                    array_push($farmers, $farmer);
                }
            }
        } else {
            $search = $request->search;
            $farmers = Farmer::when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('farmer_code', 'like', "%$search%")->orwhere('farmer_name', 'like', "%$search%");
                });
            })->orderBy('farmer_name')->get();
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_FARMER"), $farmers);
    }

    function addContainer(Request $request)
    {
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

    function containers(Request $request)
    {
        $containerNumber = $request->container_number;
        $containers = Container::when($containerNumber, function ($q) use ($containerNumber) {
            $q->where(function ($q) use ($containerNumber) {
                $q->where('container_number', 'like', "%$containerNumber%");
            });
        })->orderBy('container_number')->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_CONTAINER"), $containers);
    }

    function transactions(Request $request)
    {
        $search = $request->search;
        $transactions = Transaction::when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%$search%");
            });
        })->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION"), $transactions);
    }

    function transactionsDetails(Request $request)
    {
        $search = $request->search;
        $transactionsDetails = TransactionDetail::when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('container_number', 'like', "%$search%");
            });
        })->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION_DETAIL"), $transactionsDetails);
    }

    function allBatches(Request $request)
    {
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

    function getContainerType(Request $request)
    {
        // $containerTypeArray = containerType();
        // $container = array();
        // foreach ($containerTypeArray as $key => $containerTypeAr) {
        //     $data['code'] = $containerTypeAr['code'];
        //     array_push($container, $data);
        // }

        $data = array_values(containerType());

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_CONTAINER"), $data);
    }
    public function resetPassword(Request $request)
    {
        $user  = User::where('email', $request->email)->first();
        if ($user->count() > 0) {

            ResetPassword::create([
                'email' => $request->email
            ]);
            Mail::to('admin@superadmin.com')->send(new reset_password($user));

            return response()->json(['status' => 'Success', 'message' => 'Email sent to your admin succesfully']);
        } else {
            throw new Exception('User Not Found');
        }
    }
}
