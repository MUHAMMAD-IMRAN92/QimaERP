<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionLog;
use App\BatchNumber;
use App\Transaction;
use App\FileSystem;
use App\LoginUser;
use App\Village;
use App\Farmer;
use App\User;
use Storage;

class CoffeeBuyer extends Controller {

    private $userId;
    private $user;

    public function __construct() {
        set_time_limit(0);
        $headers = getallheaders();
        $checksession = LoginUser::where('session_key', $headers['session_token'])->first();

        if ($checksession) {
            $user = User::where('user_id', $checksession->user_id)->with('roles')->first();
            if ($user) {
                $this->user = $user;
                $this->userId = $user->user_id;
            } else {
                return sendError('Session Expired', 404);
            }
        } else {
            return sendError('Session Expired', 404);
        }
    }

    function farmer(Request $request) {
        $farmerName = $request->farmer_name;
        $governerateCode = $request->governerate_code;
        $regionCode = $request->region_code;
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::when($farmerName, function($q) use ($farmerName) {
                    $q->where(function($q) use ($farmerName) {
                        $q->where('farmer_name', 'like', "%$farmerName%");
                    });
                })->when($governerateCode, function($q) use ($governerateCode) {
                    $q->where(function($q) use ($governerateCode) {
                        $q->where('governerate_code', 'like', "%$governerateCode%");
                    });
                })->when($regionCode, function($q) use ($regionCode) {
                    $q->where(function($q) use ($regionCode) {
                        $q->where('region_code', 'like', "%$regionCode%");
                    });
                })->where('is_status', 1)->with('governerate', 'region', 'village')->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'system_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`system_file_name`),IFNULL(`system_file_name`,'" . $user_image . "')) as system_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'system_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`system_file_name`),IFNULL(`system_file_name`,'" . $user_image . "')) as system_file_name"));
                    }])->orderBy('farmer_name')->get();
        return sendSuccess('Successfully retrieved farmers', $farmers);
    }

    function batches(Request $request) {
        $search = $request->search;
        $batches = BatchNumber::when($search, function($q) use ($search) {
                    $q->where(function($q) use ($search) {
                        $q->where('batch_number', 'like', "%$search%");
                    });
                })->where('is_parent', 0)->where('created_by', $this->userId)->get();
        return sendSuccess('Successfully retrieved batches', $batches);
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

    function addCoffeeWithBatchNumber(Request $request) {
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
        //::last batch number id
        $lastBatchNumber = BatchNumber::latest('batch_id')->first();
        //::last transaction id
        $lastTransactionNumber = Transaction::latest('transaction_id')->first();
        if ($lastBatchNumber) {
            $lastBID = $lastBatchNumber->batch_id;
        }
        if ($lastTransactionNumber) {
            $lastTID = $lastTransactionNumber->batch_id;
        }
        $batch_numbers = json_decode($request['batch_number']);
        //::insert child batches id
        $childBatchNumberArray = array();
        //::insert child transactions id
        $childTransactionArray = array();
        //::Add child batch number
        foreach ($batch_numbers->child_batch as $key => $childBatch) {
            $removeLocalId = explode("-", $childBatch->batch_code);
            //::remove last index of array
            array_pop($removeLocalId);
            $farmerCode = implode("-", $removeLocalId) . '_' . $childBatch->created_by;
            $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
            $lastBID = $lastBID + 1;
            $newBatch = BatchNumber::create([
                        'batch_number' => $farmer->farmer_code . '-' . $lastBID,
                        'is_parent' => 0,
                        'is_mixed' => 0,
                        'created_by' => $childBatch->created_by,
                        'is_local' => FALSE,
                        'is_mixed' => 0,
                        'local_code' => $childBatch->local_code,
            ]);
            //::child transactions
            if (isset($childBatch->transactions) && $childBatch->transactions) {
                $newTransaction = Transaction::create([
                            'batch_number' => $newBatch->batch_number,
                            'is_parent' => 0,
                            'is_mixed' => 0,
                            'created_by' => $childBatch->transactions->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => $childBatch->transactions->transaction_type,
                            'is_mixed' => 0,
                            'local_code' => $childBatch->transactions->local_code,
                            'transaction_status' => $childBatch->transactions->transaction_status,
                ]);
                if (isset($childBatch->transactions->transaction_log) && $childBatch->transactions->transaction_log) {
                    $transactionLog = TransactionLog::create([
                                'transaction_id' => $newTransaction->transaction_id,
                                'action' => $childBatch->transactions->transaction_log->action,
                                'created_by' => $childBatch->transactions->transaction_log->created_by,
                                'entity_id' => $childBatch->transactions->transaction_log->entity_id,
                                'type' => $childBatch->transactions->transaction_log->type,
                                'local_created_at' => $childBatch->transactions->transaction_log->local_created_at,
                    ]);
                }
                //::child transactions details
                if (isset($childBatch->transactions->transactions_detail) && $childBatch->transactions->transactions_detail) {
                    $transactionsDetails = $childBatch->transactions->transactions_detail;
                    foreach ($transactionsDetails as $key => $transactionsDetail) {
                        TransactionDetail::create([
                            'transaction_id' => $newTransaction->transaction_id,
                            'container_number' => $transactionsDetail->container_number,
                            'created_by' => $childBatch->transactions->created_by,
                            'is_local' => FALSE,
                            'weight' => $transactionsDetail->container_weight,
                        ]);
                    }
                }
            }
            array_push($childBatchNumberArray, $newBatch->batch_id);
            array_push($childTransactionArray, $newTransaction->transaction_id);
        }
        //::add parent batch
        $removeLocalId = explode("-", $batch_numbers->batch_code);
        //::remove last index of array
        array_pop($removeLocalId);
        if ($removeLocalId[3] == '000') {
            $parentBatchCode = implode("-", $removeLocalId) . '-' . ($lastBID + 1);
        } else {

            $farmerCode = implode("-", $removeLocalId) . '_' . $batch_numbers->created_by;
            $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
            $parentBatchCode = $farmer->farmer_code . '-' . ($lastBID + 1);
        }
        $parentBatch = BatchNumber::create([
                    'batch_number' => $parentBatchCode,
                    'is_parent' => 0,
                    'is_mixed' => $batch_numbers->is_mixed,
                    'created_by' => $batch_numbers->created_by,
                    'is_local' => FALSE,
                    'local_code' => $batch_numbers->local_code,
        ]);
        if (isset($batch_numbers->transactions) && $batch_numbers->transactions) {
            $parentTransaction = Transaction::create([
                        'batch_number' => $parentBatch->batch_number,
                        'is_parent' => 0,
                        'is_mixed' => $batch_numbers->transactions->is_mixed,
                        'created_by' => $batch_numbers->transactions->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $batch_numbers->transactions->transaction_type,
                        'local_code' => $batch_numbers->transactions->local_code,
                        'transaction_status' => $batch_numbers->transactions->transaction_status,
            ]);


            if (isset($batch_numbers->transactions->transaction_log) && $batch_numbers->transactions->transaction_log) {
                $transactionLog = TransactionLog::create([
                            'transaction_id' => $parentTransaction->transaction_id,
                            'action' => $batch_numbers->transactions->transaction_log->action,
                            'created_by' => $batch_numbers->transactions->transaction_log->created_by,
                            'entity_id' => $batch_numbers->transactions->transaction_log->entity_id,
                            'type' => $batch_numbers->transactions->transaction_log->type,
                            'local_created_at' => $batch_numbers->transactions->transaction_log->local_created_at,
                ]);
            }


            if (isset($batch_numbers->transactions->transactions_detail) && $batch_numbers->transactions->transactions_detail) {
                $transactionsDetails = $batch_numbers->transactions->transactions_detail;
                foreach ($transactionsDetails as $key => $transactionsDetail) {
                    TransactionDetail::create([
                        'transaction_id' => $parentTransaction->transaction_id,
                        'container_number' => $transactionsDetail->container_number,
                        'created_by' => $batch_numbers->transactions->created_by,
                        'is_local' => FALSE,
                        'weight' => $transactionsDetail->container_weight,
                    ]);
                }
            }
        }
        BatchNumber::whereIn('batch_id', $childBatchNumberArray)->update(['is_parent' => $parentBatch->batch_id]);
        Transaction::whereIn('transaction_id', $childTransactionArray)->update(['is_parent' => $parentTransaction->transaction_id]);
        $currentBatch = BatchNumber::where('batch_id', $parentBatch->batch_id)->with('childBatchNumber.transaction.transactionDetail')->with('transaction.transactionDetail')->first();
        return sendSuccess('Coffee was added Successfully', $currentBatch);
    }

    function addCoffeeWithOutBatchNumber(Request $request) {
        //::validation
        $validator = Validator::make($request->all(), [
                    'transaction' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $transactionIds = array();
        $transactions = json_decode($request['transaction']);
        foreach ($transactions as $key => $transaction) {
            $newTransaction = Transaction::create([
                        'batch_number' => $transaction->batch_number,
                        'is_parent' => 0,
                        'is_mixed' => 0,
                        'created_by' => $transaction->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $transaction->transaction_type,
                        'is_mixed' => 0,
                        'local_code' => $transaction->local_code,
                        'transaction_status' => $transaction->transaction_status,
            ]);
            if (isset($transaction->transaction_log) && $transaction->transaction_log) {
                $transactionLog = TransactionLog::create([
                            'transaction_id' => $newTransaction->transaction_id,
                            'action' => $transaction->transaction_log->action,
                            'created_by' => $transaction->transaction_log->created_by,
                            'entity_id' => $transaction->transaction_log->entity_id,
                            'type' => $transaction->transaction_log->type,
                            'local_created_at' => $transaction->transaction_log->local_created_at,
                ]);
            }
            //::child transactions details
            if (isset($transaction->transactions_detail) && $transaction->transactions_detail) {
                $transactionsDetails = $transaction->transactions_detail;
                foreach ($transactionsDetails as $key => $transactionsDetail) {
                    TransactionDetail::create([
                        'transaction_id' => $newTransaction->transaction_id,
                        'container_number' => $transactionsDetail->container_number,
                        'created_by' => $transaction->created_by,
                        'is_local' => FALSE,
                        'weight' => $transactionsDetail->container_weight,
                    ]);
                }
            }

            array_push($transactionIds, $newTransaction->transaction_id);
        }
        $currentBatch = Transaction::whereIn('transaction_id', $transactionIds)->with('transactionDetail')->get();
        return sendSuccess('Coffee was added Successfully', $currentBatch);
    }

    function coffeeBuyerCoffee(Request $request) {
        $transactions = Transaction::where('is_parent', 0)->where('created_by', $this->userId)->where('transaction_status', 'created')->doesntHave('isReference')->with('childTransation.transactionDetail','transactionDetail')->orderBy('transaction_id', 'desc')->get();
        return sendSuccess('Transactions retrieved successfully', $transactions);
    }

}
