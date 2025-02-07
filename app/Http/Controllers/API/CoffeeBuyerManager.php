<?php

namespace App\Http\Controllers\API;

use App\User;
use Throwable;
use App\Center;
use App\Farmer;
use App\LoginUser;
use Carbon\Carbon;
use App\BatchNumber;
use App\Transaction;
use App\TransactionLog;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class CoffeeBuyerManager extends Controller
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
    function farmer(Request $request)
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
            $farmers = [];
            $user_image = Storage::disk('s3')->url('images/demo_user_image.png');
            $user_image_path = Storage::disk('s3')->url('images');
            foreach ($villages as $village) {
                $villagefarmer = Farmer::where('status', 1)->where('village_code', $village->village_code)->with(['profileImage' => function ($query) use ($user_image, $user_image_path) {
                    $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                }])->with(['idcardImage' => function ($query) use ($user_image, $user_image_path) {
                    $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                }])->orderBy('farmer_name')->get();
                foreach ($villagefarmer as $farmer) {
                    array_push($farmers, $farmer);
                }
                foreach ($farmers as $key => $farmer) {
                    $farmer->farmer_id_card_picture = '';
                    $farmer->farmer_picture = '';
                    if (isset($farmer->idcardImage) && isset($farmer->idcardImage->user_file_name)) {
                        $farmer->farmer_id_card_picture = $farmer->idcardImage->user_file_name;
                    }
                    if (isset($farmer->profileImage) && isset($farmer->profileImage->user_file_name)) {
                        $farmer->farmer_picture = $farmer->profileImage->user_file_name;
                    }
                    $farmer->farmer_village = $farmer->village_code;
                    $farmer->farmer_id_card_no = $farmer->farmer_nicn;
                    $farmer->makeHidden('idcardImage');
                    $farmer->makeHidden('profileImage');
                    $farmer->makeHidden('village_code');
                    $farmer->makeHidden('farmer_nicn');
                    $farmer->makeHidden('idcard_picture_id');
                    $farmer->makeHidden('picture_id');
                }
            }
            if (count($villages) == 0) {

                $farmerName = $request->farmer_name;
                $villageCode = $request->village_code;
                $farmerCode = $request->farmer_code;
                $farmerNicn = $request->farmer_nicn;

                $user_image = Storage::disk('s3')->url('images/demo_user_image.png');
                $user_image_path = Storage::disk('s3')->url('images');
                $farmers = Farmer::where('status', 1)->when($farmerName, function ($q) use ($farmerName) {
                    $q->where(function ($q) use ($farmerName) {
                        $q->where('farmer_name', 'like', "%$farmerName%");
                    });
                })->when($villageCode, function ($q) use ($villageCode) {
                    $q->where(function ($q) use ($villageCode) {
                        $q->where('village_code', 'like', "%$villageCode%");
                    });
                })->when($farmerCode, function ($q) use ($farmerCode) {
                    $q->where(function ($q) use ($farmerCode) {
                        $q->where('farmer_code', 'like', "%$farmerCode%");
                    });
                })->with(['profileImage' => function ($query) use ($user_image, $user_image_path) {
                    $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                }])->with(['idcardImage' => function ($query) use ($user_image, $user_image_path) {
                    $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                }])->orderBy('farmer_name')->get();

                foreach ($farmers as $key => $farmer) {
                    $farmer->farmer_id_card_picture = '';
                    $farmer->farmer_picture = '';
                    if (isset($farmer->idcardImage) && isset($farmer->idcardImage->user_file_name)) {
                        $farmer->farmer_id_card_picture = $farmer->idcardImage->user_file_name;
                    }
                    if (isset($farmer->profileImage) && isset($farmer->profileImage->user_file_name)) {
                        $farmer->farmer_picture = $farmer->profileImage->user_file_name;
                    }
                    $farmer->farmer_village = $farmer->village_code;
                    $farmer->farmer_id_card_no = $farmer->farmer_nicn;
                    $farmer->makeHidden('idcardImage');
                    $farmer->makeHidden('profileImage');
                    $farmer->makeHidden('village_code');
                    $farmer->makeHidden('farmer_nicn');
                    $farmer->makeHidden('idcard_picture_id');
                    $farmer->makeHidden('picture_id');
                }
            }
        } else {

            $farmerName = $request->farmer_name;
            $villageCode = $request->village_code;
            $farmerCode = $request->farmer_code;
            $farmerNicn = $request->farmer_nicn;

            $user_image = Storage::disk('s3')->url('images/demo_user_image.png');
            $user_image_path = Storage::disk('s3')->url('images');
            $farmers = Farmer::where('status', 1)->when($farmerName, function ($q) use ($farmerName) {
                $q->where(function ($q) use ($farmerName) {
                    $q->where('farmer_name', 'like', "%$farmerName%");
                });
            })->when($villageCode, function ($q) use ($villageCode) {
                $q->where(function ($q) use ($villageCode) {
                    $q->where('village_code', 'like', "%$villageCode%");
                });
            })->when($farmerCode, function ($q) use ($farmerCode) {
                $q->where(function ($q) use ($farmerCode) {
                    $q->where('farmer_code', 'like', "%$farmerCode%");
                });
            })->with(['profileImage' => function ($query) use ($user_image, $user_image_path) {
                $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
            }])->with(['idcardImage' => function ($query) use ($user_image, $user_image_path) {
                $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
            }])->orderBy('farmer_name')->get();

            foreach ($farmers as $key => $farmer) {
                $farmer->farmer_id_card_picture = '';
                $farmer->farmer_picture = '';
                if (isset($farmer->idcardImage) && isset($farmer->idcardImage->user_file_name)) {
                    $farmer->farmer_id_card_picture = $farmer->idcardImage->user_file_name;
                }
                if (isset($farmer->profileImage) && isset($farmer->profileImage->user_file_name)) {
                    $farmer->farmer_picture = $farmer->profileImage->user_file_name;
                }
                $farmer->farmer_village = $farmer->village_code;
                $farmer->farmer_id_card_no = $farmer->farmer_nicn;
                $farmer->makeHidden('idcardImage');
                $farmer->makeHidden('profileImage');
                $farmer->makeHidden('village_code');
                $farmer->makeHidden('farmer_nicn');
                $farmer->makeHidden('idcard_picture_id');
                $farmer->makeHidden('picture_id');
            }
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_FARMER"), $farmers);
    }

    function sentTransactions(Request $request)
    {
        //::validation
        \Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'transactions' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $sentTransactions = json_decode($request['transactions']);

        $alreadySentCoffee = array();
        $sentCoffeeArray = array();
        foreach ($sentTransactions as $key => $sentTransaction) {
            if (isset($sentTransaction->transactions) && $sentTransaction->transactions) {
                if ($sentTransaction->transactions->is_update_center == TRUE) {

                    $updateCenter = Transaction::where('transaction_id', $sentTransaction->transactions->transaction_id)->with('log')->first();
                    if ($updateCenter) {
                        $updateCenter->log->entity_id = $sentTransaction->transactions->center_id;
                        $updateCenter->log->center_name = $sentTransaction->transactions->center_name;
                        $updateCenter->sent_to = $sentTransaction->transactions->sent_to;
                        $updateCenter->transaction_type = 2;
                        $updateCenter->local_created_at = toSqlDT($sentTransaction->transactions->local_created_at);
                        $updateCenter->local_updated_at = toSqlDT($sentTransaction->transactions->local_updated_at);
                        $updateCenter->save();
                        $updateCenter->log->save();
                        array_push($sentCoffeeArray, $sentTransaction->transactions->transaction_id);
                    }
                } else {

                    try {
                        $alreadyExistTransaction = Transaction::where('reference_id', $sentTransaction->transactions->reference_id)->first();
                        // adding because we get same reference id and transaction id
                        $same_check = 0;
                        if ($sentTransaction->transactions->reference_id == $sentTransaction->transactions->transaction_id) {
                            $same_check = 1;
                        }
                        \Log::info("SAME CHECK - " . $sentTransaction->transactions->reference_id . " - " . $sentTransaction->transactions->session_no . " - " . $sentTransaction->transactions->transaction_id . " " . $same_check);
                        if ($alreadyExistTransaction && $same_check == 0) {
                            $sentTransaction->transactions->already_sent = true;
                            $sentTransaction->transactions->created_at =  toSqlDT($sentTransaction->transactions->created_at);
                            $sentTransaction->transactions->local_created_at = toSqlDT($sentTransaction->transactions->local_created_at);
                            $sentTransaction->transactions->local_updated_at = toSqlDT($sentTransaction->transactions->local_updated_at);

                            foreach ($sentTransaction->transactions_detail as $detail) {
                                $detail->created_at = toSqlDT($detail->created_at);
                            }
                            array_push($alreadySentCoffee, $sentTransaction);
                        } else {
                            DB::beginTransaction();
                            \Log::info("GOT IT - " . $sentTransaction->transactions->reference_id . " - " . $sentTransaction->transactions->session_no . " - " . $sentTransaction->transactions->transaction_id . " " . $same_check);
                            $transaction = Transaction::create([
                                'batch_number' => $sentTransaction->transactions->batch_number,
                                'is_parent' => $sentTransaction->transactions->is_parent,
                                'is_mixed' => $sentTransaction->transactions->is_mixed,
                                'created_by' => $sentTransaction->transactions->created_by,
                                'is_local' => FALSE,
                                'transaction_type' => 2,
                                'local_code' => $sentTransaction->transactions->local_code,
                                'transaction_status' => 'sent',
                                'reference_id' => $sentTransaction->transactions->reference_id,
                                'is_server_id' => 1,
                                'is_new' => $sentTransaction->transactions->is_new,
                                'sent_to' => 3,
                                'is_sent' => 0,
                                'session_no' => $sentTransaction->transactions->session_no,
                                'local_created_at' => toSqlDT($sentTransaction->transactions->local_created_at),
                                'local_updated_at' => toSqlDT($sentTransaction->transactions->local_updated_at)

                            ]);
                            \Log::info("New Transaction Obj - " . $transaction->transaction_id . " - " . $transaction);

                            $transactionLog = TransactionLog::create([
                                'transaction_id' => $transaction->transaction_id,
                                'action' => 'sent',
                                'created_by' => $sentTransaction->transactions->created_by,
                                'entity_id' => $sentTransaction->transactions->center_id,
                                'center_name' => $sentTransaction->transactions->center_name,
                                'local_updated_at' => toSqlDT($sentTransaction->transactions->local_updated_at),
                                'local_created_at' =>  toSqlDT($sentTransaction->transactions->local_created_at),
                                'type' => 'center',
                            ]);
                            \Log::info("New TransactionLog Obj - " .  $transactionLog);

                            $transactionContainers = $sentTransaction->transactions_detail;
                            foreach ($transactionContainers as $key => $transactionContainer) {
                                $details =    TransactionDetail::create([
                                    'transaction_id' => $transaction->transaction_id,
                                    'container_number' => $transactionContainer->container_number,
                                    'created_by' => $sentTransaction->transactions->created_by,
                                    'is_local' => FALSE,
                                    'container_weight' => $transactionContainer->container_weight,
                                    'weight_unit' => $transactionContainer->weight_unit,
                                    'center_id' => $transactionContainer->center_id,
                                    'reference_id' => 0,
                                ]);
                            }
                            \Log::info("New Details Obj - " .  $details);

                            array_push($sentCoffeeArray, $transaction->transaction_id);
                            DB::commit();
                            \Log::info("DB committed Here!");
                        }
                    } catch (Throwable $th) {
                        DB::rollback();
                        return Response::json(array('status' => 'error', 'message' => $th->getMessage(), '  data' => [
                            'line' => $th->getLine()
                        ]), 499);
                    }
                }
            }
        }

        $currentlySentCoffees = Transaction::whereIn('transaction_id', $sentCoffeeArray)->with('transactionDetail', 'log')->get();
        $dataArray = array();
        foreach ($currentlySentCoffees as $key => $currentlySentCoffee) {
            $currentlySentCoffee->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $currentlySentCoffee->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $currentlySentCoffee->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

            $transactionsDetail = $currentlySentCoffee->transactionDetail;

            $currentlySentCoffee->center_id = $currentlySentCoffee->log->entity_id;
            $currentlySentCoffee->center_name = $currentlySentCoffee->log->center_name;
            $currentlySentCoffee->makeHidden('transactionDetail');
            $currentlySentCoffee->makeHidden('log');
            $currentlySentCoffee->already_sent = FALSE;
            $sentCoffee = ['transactions' => $currentlySentCoffee, 'transactions_detail' => $transactionsDetail];
            array_push($dataArray, $sentCoffee);
        }

        $data = array_merge($dataArray, $alreadySentCoffee);

        if (count($alreadySentCoffee) > 0) {
            return sendSuccess(Config("statuscodes." . $this->app_lang . ".error_messages.TRANSACTION_SENT_ALREADY"), $data);
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.SENT_COFFEE"), $data);
    }

    function centers(Request $request)
    {
        $search = $request->search;
        $centers = Center::when($search, function ($q) use ($search) {
            $q->where(function ($q) use ($search) {
                $q->where('center_code', 'like', "%$search%");
            });
        })->get();
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_CENTER"), $centers);
    }

    function coffeeBuyerManagerCoffee(Request $request)
    {
        $allTransactions = array();
        $transactions = Transaction::where('is_parent', 0)
            ->where('transaction_status', 'created')
            ->doesntHave('isReference')
            ->with('childTransation.transactionDetail', 'transactionDetail', 'log')
            ->orderBy('transaction_id', 'desc')
            ->get();

        foreach ($transactions as $key => $transaction) {
            $childTransactions = array();

            if ($transaction->childTransation) {
                foreach ($transaction->childTransation as $key => $childTransation) {
                    $childTransation->buyer_name = '';
                    $checkBatch = BatchNumber::where('batch_number', $childTransation->batch_number)->with('buyer')->first();
                    if ($checkBatch && isset($checkBatch->buyer)) {
                        $childTransation->buyer_name = $checkBatch->buyer->first_name . ' ' . $checkBatch->buyer->last_name;
                    }
                    $childTransationDetail = $childTransation->transactionDetail;
                    $childTransation->makeHidden('transactionDetail');
                    $childData = ['transactions' => $childTransation, 'transactions_detail' => $childTransationDetail];
                    array_push($childTransactions, $childData);
                }
            }

            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

            $transaction->center_name = $transaction->log ? $transaction->log->center_name : '';
            $transaction->center_id = $transaction->log ? $transaction->log->entity_id : 0;
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $transaction->makeHidden('log');
            $data = ['transactions' => $transaction, 'child_transation' => $childTransactions, 'transactions_detail' => $transactionDetail];
            array_push($allTransactions, $data);
        }

        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION"), $allTransactions);
    }

    function coffeeBuyerManagerSentCoffeeTransaction(Request $request)
    {
        $allTransactions = array();
        $transactions = Transaction::where('created_by', $this->userId)->where('is_parent', 0)->where('transaction_status', 'sent')->whereHas('transactionDetail', function ($q) {
            $q->where('container_status', 0);
        }, '>', 0)->with(['transactionDetail' => function ($query) {
            $query->where('container_status', 0);
        }])->with('log')->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $transaction->buyer_name = '';
            $parentCheckBatch = BatchNumber::where('batch_number', $transaction->batch_number)->with('buyer')->first();
            if ($parentCheckBatch && isset($parentCheckBatch->buyer)) {
                $transaction->buyer_name = $parentCheckBatch->buyer->first_name . ' ' . $parentCheckBatch->buyer->last_name;
            }

            $transaction->center_name = $transaction->log->center_name;
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transactions' => $transaction, 'transactions_detail' => $transactionDetail];
            array_push($allTransactions, $data);
        }
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.RETRIEVED_TRANSACTION"), $allTransactions);
    }

    function approvedFarmer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'farmer_code' => 'required|array',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        Farmer::wherein('farmer_code', $request['farmer_code'])->update(['is_status' => 1]);
        return sendSuccess(Config("statuscodes." . $this->app_lang . ".success_messages.APPROVED_FARMER"), []);
    }
}
