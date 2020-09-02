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
use App\Season;
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
        $villageCode = $request->village_code;
        $farmerCode = $request->farmer_code;
        $farmerNicn = $request->farmer_nicn;
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::when($farmerName, function($q) use ($farmerName) {
                    $q->where(function($q) use ($farmerName) {
                        $q->where('farmer_name', 'like', "%$farmerName%");
                    });
                })->when($villageCode, function($q) use ($villageCode) {
                    $q->where(function($q) use ($villageCode) {
                        $q->where('village_code', 'like', "%$villageCode%");
                    });
                })->when($farmerCode, function($q) use ($farmerCode) {
                    $q->where(function($q) use ($farmerCode) {
                        $q->where('farmer_code', 'like', "%$farmerCode%");
                    });
                })->when($farmerNicn, function($q) use ($farmerNicn) {
                    $q->where(function($q) use ($farmerNicn) {
                        $q->where('farmer_nicn', 'like', "%$farmerNicn%");
                    });
                })->where('is_status', 1)->with('village')->with(['profileImage' => function($query) use($user_image, $user_image_path) {
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
        $lastBatchNumber = BatchNumber::orderBy('created_at', 'desc')->first();
        //::last transaction id
        $lastTransactionNumber = Transaction::orderBy('created_at', 'desc')->first();
        if ($lastBatchNumber) {
            $lastBID = $lastBatchNumber->batch_id;
        }
        if ($lastTransactionNumber) {
            $lastTID = $lastTransactionNumber->transaction_id;
        }
        $batch_numbers = json_decode($request['batch_number']);
        //::insert child batches id
        $childBatchNumberArray = array();
        //::insert child transactions id
        $childTransactionArray = array();
        $season = Season::where('status', 0)->first();
        //::Add child batch number
        foreach ($batch_numbers->child_batch as $key => $childBatch) {
            $removeLocalId = explode("-", $childBatch->batch->batch_number);
            $lastBID = ($lastBID + 1);
            //::remove last index of array
            array_pop($removeLocalId);

            // $farmerCode = implode("-", $removeLocalId) . '_' . $childBatch->batch->created_by;
            $farmerCode = implode("-", $removeLocalId);
            if ($childBatch->batch->is_server_id == 1) {
                $farmer = Farmer::where('farmer_code', $farmerCode)->first();
            } else {
                $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
            }
            $newBatch = BatchNumber::create([
                        'batch_number' => $farmer->farmer_code . '-' . $lastBID,
                        'is_parent' => 0,
                        'is_mixed' => 0,
                        'created_by' => $childBatch->batch->created_by,
                        'is_local' => FALSE,
                        'is_mixed' => 0,
                        'local_code' => $childBatch->batch->local_code,
                        'is_server_id' => $childBatch->batch->is_server_id,
                        'season_id' => $season->season_id,
                        'season_status' => $season->status,
            ]);
            //::child transactions
            if (isset($childBatch->transactions) && isset($childBatch->transactions->transaction) && $childBatch->transactions->transaction) {
                $newTransaction = Transaction::create([
                            'batch_number' => $newBatch->batch_number,
                            'is_parent' => 0,
                            'is_mixed' => 0,
                            'created_by' => $childBatch->transactions->transaction->created_by,
                            'is_local' => FALSE,
                            'transaction_type' => $childBatch->transactions->transaction->transaction_type,
                            'is_mixed' => 0,
                            'local_code' => $childBatch->transactions->transaction->local_code,
                            'transaction_status' => 'created',
                            'is_server_id' => $childBatch->transactions->transaction->is_server_id,
                            'is_new' => $childBatch->transactions->transaction->is_new,
                            'sent_to' => 2,
                ]);

                $transactionLog = TransactionLog::create([
                            'transaction_id' => $newTransaction->transaction_id,
                            'action' => 'created',
                            'created_by' => $childBatch->transactions->transaction->created_by,
                            'entity_id' => $childBatch->transactions->transaction->created_by,
                            'type' => 'coffee_buyer',
                            'local_created_at' => $childBatch->transactions->transaction->created_at,
                ]);
                //::child transactions details
                if (isset($childBatch->transactions->transactions_detail) && $childBatch->transactions->transactions_detail) {
                    $transactionsDetails = $childBatch->transactions->transactions_detail;
                    foreach ($transactionsDetails as $key => $transactionsDetail) {
                        TransactionDetail::create([
                            'transaction_id' => $newTransaction->transaction_id,
                            'container_number' => $transactionsDetail->container_number,
                            'created_by' => $transactionsDetail->created_by,
                            'is_local' => FALSE,
                            'container_weight' => $transactionsDetail->container_weight,
                            'weight_unit' => $transactionsDetail->weight_unit,
                        ]);
                    }
                }
            }
            array_push($childBatchNumberArray, $newBatch->batch_id);
            array_push($childTransactionArray, $newTransaction->transaction_id);
        }
        //::add parent batch
        $removeLocalId = explode("-", $batch_numbers->batch->batch_number);

        //::remove last index of array

        array_pop($removeLocalId);
        if ($removeLocalId[3] == '000') {
            $parentBatchCode = implode("-", $removeLocalId) . '-' . ($lastBID + 1);
        } else {
            //$farmerCode = implode("-", $removeLocalId) . '_' . $batch_numbers->batch->created_by;
            $farmerCode = implode("-", $removeLocalId);
            if ($batch_numbers->batch->is_server_id == 1) {
                $farmer = Farmer::where('farmer_code', $farmerCode)->first();
            } else {
                $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
            }
            $parentBatchCode = $farmer->farmer_code . '-' . ($lastBID + 1);
        }
        $parentBatch = BatchNumber::create([
                    'batch_number' => $parentBatchCode,
                    'is_parent' => 0,
                    'is_mixed' => $batch_numbers->batch->is_mixed,
                    'created_by' => $batch_numbers->batch->created_by,
                    'is_local' => FALSE,
                    'local_code' => $batch_numbers->batch->local_code,
                    'is_server_id' => $batch_numbers->batch->is_server_id,
                    'season_id' => $season->season_id,
                    'season_status' => $season->status,
        ]);
        if (isset($batch_numbers->transactions) && isset($batch_numbers->transactions->transaction) && $batch_numbers->transactions->transaction) {
            $parentTransaction = Transaction::create([
                        'batch_number' => $parentBatch->batch_number,
                        'is_parent' => 0,
                        'is_mixed' => $batch_numbers->transactions->transaction->is_mixed,
                        'created_by' => $batch_numbers->transactions->transaction->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $batch_numbers->transactions->transaction->transaction_type,
                        'local_code' => $batch_numbers->transactions->transaction->local_code,
                        'transaction_status' => 'created',
                        'is_server_id' => $batch_numbers->transactions->transaction->is_server_id,
                        'is_new' => $batch_numbers->transactions->transaction->is_new,
                        'sent_to' => 2,
            ]);


            $transactionLog = TransactionLog::create([
                        'transaction_id' => $parentTransaction->transaction_id,
                        'action' => 'created',
                        'created_by' => $batch_numbers->transactions->transaction->created_by,
                        'entity_id' => $batch_numbers->transactions->transaction->created_by,
                        'type' => 'coffee_buyer',
                        'local_created_at' => $batch_numbers->transactions->transaction->created_at,
            ]);



            if (isset($batch_numbers->transactions->transactions_detail) && $batch_numbers->transactions->transactions_detail) {
                $transactionsDetails = $batch_numbers->transactions->transactions_detail;
                foreach ($transactionsDetails as $key => $transactionsDetail) {
                    TransactionDetail::create([
                        'transaction_id' => $parentTransaction->transaction_id,
                        'container_number' => $transactionsDetail->container_number,
                        'created_by' => $transactionsDetail->created_by,
                        'is_local' => FALSE,
                        'container_weight' => $transactionsDetail->container_weight,
                        'weight_unit' => $transactionsDetail->weight_unit,
                    ]);
                }
            }
        }
        BatchNumber::whereIn('batch_id', $childBatchNumberArray)->update(['is_parent' => $parentBatch->batch_id]);
        Transaction::whereIn('transaction_id', $childTransactionArray)->update(['is_parent' => $parentTransaction->transaction_id]);

        $patentTransactions = null;
        $patentTransactionsDetail = null;
        $childBatches = array();
        $currentBatchData = BatchNumber::where('batch_id', $parentBatch->batch_id)->with('childBatchNumber.latestTransation.transactionDetail')->with('latestTransation.transactionDetail')->first();
        if ($currentBatchData->is_mixed == 1) {
            if (isset($currentBatchData->childBatchNumber) && $currentBatchData->childBatchNumber) {
                foreach ($currentBatchData->childBatchNumber as $key => $childBatchNumber) {
                    $childPatentTransactions = null;
                    $childPatentTransactionsDetail = null;
                    if (isset($childBatchNumber->latestTransation) && $childBatchNumber->latestTransation) {
                        if (isset($childBatchNumber->latestTransation->transactionDetail) && $childBatchNumber->latestTransation->transactionDetail) {
                            $childPatentTransactionsDetail = $childBatchNumber->latestTransation->transactionDetail;
                            $childBatchNumber->latestTransation->makeHidden('transactionDetail');
                        }
                        $childPatentTransactions = $childBatchNumber->latestTransation;
                        $childBatchNumber->makeHidden('latestTransation');
                    }
                    $childtransactionData = ['transaction' => $childPatentTransactions, 'transactions_detail' => $childPatentTransactionsDetail];
                    $dataPush = ['batch' => $childBatchNumber, 'transactions' => $childtransactionData];
                    array_push($childBatches, $dataPush);
                }
            }
            $currentBatchData->makeHidden('childBatchNumber');
        } else {
            $currentBatchData->makeHidden('childBatchNumber');
        }
        if (isset($currentBatchData->latestTransation) && $currentBatchData->latestTransation) {
            if (isset($currentBatchData->latestTransation->transactionDetail) && $currentBatchData->latestTransation->transactionDetail) {
                $patentTransactionsDetail = $currentBatchData->latestTransation->transactionDetail;
                $currentBatchData->latestTransation->makeHidden('transactionDetail');
            }
            $patentTransactions = $currentBatchData->latestTransation;
        }
        $currentBatchData->makeHidden('latestTransation');
        //  $currentBatchData->makeHidden('transaction');
        $transactionData = ['transaction' => $patentTransactions, 'transactions_detail' => $patentTransactionsDetail];

        $data = ['batch' => $currentBatchData, 'child_batch' => $childBatches, 'transactions' => $transactionData];
        $lastBID = 0;
        $lastTID = 0;
        return sendSuccess('Coffee was added Successfully', $data);

//        $currentBatch = BatchNumber::where('batch_id', $parentBatch->batch_id)->with('childBatchNumber.transaction.transactionDetail')->with('transaction.transactionDetail')->first();
//        return sendSuccess('Coffee was added Successfully', $currentBatch);
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
        $newTransactionid = null;
        $transactions = json_decode($request['transaction']);

        if (isset($transactions->transactions) && $transactions->transactions) {
            $batchCode = $transactions->transactions->batch_number;
            if ($transactions->transactions->is_server_id == 0) {
                $currentBatch = BatchNumber::where('local_code', 'like', "$batchCode%")->first();
                $batchCode = $currentBatch->batch_number;
            }
            $newTransaction = Transaction::create([
                        'batch_number' => $batchCode,
                        'is_parent' => 0,
                        'is_mixed' => 0,
                        'created_by' => $transactions->transactions->created_by,
                        'is_local' => FALSE,
                        'transaction_type' => $transactions->transactions->transaction_type,
                        'is_mixed' => 0,
                        'local_code' => $transactions->transactions->local_code,
                        'transaction_status' => 'created',
                        'is_server_id' => $transactions->transactions->is_server_id,
                        'is_new' => $transactions->transactions->is_new,
                        'sent_to' => 2,
            ]);
            $newTransactionid = $newTransaction->transaction_id;
            $transactionLog = TransactionLog::create([
                        'transaction_id' => $newTransaction->transaction_id,
                        'action' => 'created',
                        'created_by' => $transactions->transactions->created_by,
                        'entity_id' => $transactions->transactions->created_by,
                        'type' => 'coffee_buyer',
                        'local_created_at' => $transactions->transactions->created_at,
            ]);
        }
        //::child transactions details
        if (isset($transactions->transactions_detail) && $transactions->transactions_detail) {
            $transactionsDetails = $transactions->transactions_detail;
            foreach ($transactionsDetails as $key => $transactionsDetail) {
                TransactionDetail::create([
                    'transaction_id' => $newTransactionid,
                    'container_number' => $transactionsDetail->container_number,
                    'created_by' => $transactionsDetail->created_by,
                    'is_local' => FALSE,
                    'container_weight' => $transactionsDetail->container_weight,
                    'weight_unit' => $transactionsDetail->weight_unit,
                ]);
            }
        }

        $currentBatch = Transaction::where('transaction_id', $newTransactionid)->with('transactionDetail')->first();
        $transationsDetail = $currentBatch->transactionDetail;
        $currentBatch->makeHidden('transactionDetail');
        $transations = $currentBatch;
        $data = ['transactions' => $transations, 'transactions_detail' => $transationsDetail];
        return sendSuccess('Coffee was added Successfully', $data);

        return sendSuccess('Coffee was added Successfully', $transationsDetail);
    }

    function coffeeBuyerCoffee(Request $request) {
        $allTransactions = array();

        $transactions = Transaction::where('is_parent', 0)->where('created_by', $this->userId)->where('transaction_status', 'created')->doesntHave('isReference')->with('childTransation.transactionDetail', 'transactionDetail')->orderBy('transaction_id', 'desc')->get();
        foreach ($transactions as $key => $transaction) {
            $childTransactions = array();
            if ($transaction->childTransation) {
                foreach ($transaction->childTransation as $key => $childTransation) {
                    $childTransationDetail = $childTransation->transactionDetail;
                    $childTransation->makeHidden('transactionDetail');
                    $childData = ['transactions' => $childTransation, 'transactions_detail' => $childTransationDetail];
                    array_push($childTransactions, $childData);
                }
            }
            $transactionDetail = $transaction->transactionDetail;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $data = ['transactions' => $transaction, 'child_transaction' => $childTransactions, 'transactions_detail' => $transactionDetail];
            array_push($allTransactions, $data);
        }

        return sendSuccess('Transactions retrieved successfully', $allTransactions);
    }

}
