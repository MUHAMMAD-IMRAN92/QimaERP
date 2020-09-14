<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TransactionDetail;
use App\TransactionInvoice;
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
                })->where('is_status', 1)->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
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
                    'farmers' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return sendError($errors, 400);
        }
        $formaersId = array();
        $farmers = json_decode($request['farmers']);
        $i = 1;
        $x = 500;
        foreach ($farmers as $key => $farmer) {
            $alreadyFarmer = Farmer::where('farmer_nicn', $farmer->farmer_id_card_no)->first();
            if (!$alreadyFarmer) {
                $profileImageId = null;
                $idcardImageId = null;
                if ($farmer->farmer_picture) {
                    $destinationPath = 'storage/app/images/';
                    $file = base64_decode($farmer->farmer_picture);
                    $file_name = time() . $i . getFileExtensionForBase64($file);
                    file_put_contents($destinationPath . $file_name, $file);
                    $userProfileImage = FileSystem::create([
                                'user_file_name' => $file_name,
                    ]);
                    $profileImageId = $userProfileImage->file_id;
                }

                if ($farmer->farmer_id_card_picture) {
                    $destinationPath = 'storage/app/images/';
                    $idfile = base64_decode($farmer->farmer_id_card_picture);
                    $id_card_file_name = time() . $x . getFileExtensionForBase64($idfile);
                    file_put_contents($destinationPath . $id_card_file_name, $idfile);
                    $userIdCardImage = FileSystem::create([
                                'user_file_name' => $id_card_file_name,
                    ]);
                    $idcardImageId = $userIdCardImage->file_id;
                }
                $lastFarmer = Farmer::orderBy('created_at', 'desc')->first();
                $currentFarmerCode = 1;
                if (isset($lastFarmer) && $lastFarmer) {
                    $currentFarmerCode = ($lastFarmer->farmer_id + 1);
                }
                $currentFarmerCode = sprintf("%03d", $currentFarmerCode);
                $village = Village::where('village_code', 'like', "%$farmer->farmer_village%")->first();
//::create new 
                $alreadyFarmer = Farmer::create([
                            'farmer_code' => $village->village_code . '-' . $currentFarmerCode,
                            'farmer_name' => $farmer->farmer_name,
                            'village_code' => $farmer->farmer_village,
                            'picture_id' => $profileImageId,
                            'idcard_picture_id' => $idcardImageId,
                            'farmer_nicn' => $farmer->farmer_id_card_no,
                            'local_code' => $farmer->local_code,
                            'is_local' => 0,
                            'created_by' => $farmer->created_id,
                ]);
            } else {
                $alreadyFarmer->local_code = $alreadyFarmer->local_code . ',' . $farmer->local_code;
                $alreadyFarmer->save();
            }
            array_push($formaersId, $alreadyFarmer->farmer_id);
            $i++;
            $x++;
        }
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $farmers = Farmer::whereIn('farmer_id', $formaersId)->with(['profileImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->with(['idcardImage' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->get();

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

        return sendSuccess('Farmer was created Successfully', $farmers);
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

        // $lastBID = 0;
        $lastTID = 0;
        //::last batch number id
        //  $lastBatchNumber = BatchNumber::orderBy('created_at', 'desc')->first();
        //::last transaction id
        $lastTransactionNumber = Transaction::orderBy('created_at', 'desc')->first();
//        if ($lastBatchNumber) {
//            $lastBID = $lastBatchNumber->batch_id;
//        }
        if ($lastTransactionNumber) {
            $lastTID = $lastTransactionNumber->transaction_id;
        }
        $batches_numbers = json_decode($request['batch_number']);

        $season = Season::where('status', 0)->first();
        $batchesArray = array();
        foreach ($batches_numbers as $key => $batch_numbers) {
            //::insert child batches id
            $childBatchNumberArray = array();
            //::insert child transactions id
            $childTransactionArray = array();
            //::Add child batch number
            foreach ($batch_numbers->child_batch as $key => $childBatch) {
                $newLastBID = 1;
                $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
                if ($lastBatchNumber) {
                    $newLastBID = ($lastBatchNumber->batch_id + 1);
                }
                $removeLocalId = explode("-", $childBatch->batch->batch_number);
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
                            'batch_number' => $farmer->farmer_code . '-' . $newLastBID,
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
            $newLastBID = 1;
            $lastBatchNumber = BatchNumber::orderBy('batch_id', 'desc')->first();
            if ($lastBatchNumber) {
                $newLastBID = ($lastBatchNumber->batch_id + 1);
            }
            array_pop($removeLocalId);
            if ($removeLocalId[3] == '000') {
                $parentBatchCode = implode("-", $removeLocalId) . '-' . ($newLastBID);
            } else {
                //$farmerCode = implode("-", $removeLocalId) . '_' . $batch_numbers->batch->created_by;
                $farmerCode = implode("-", $removeLocalId);
                if ($batch_numbers->batch->is_server_id == 1) {
                    $farmer = Farmer::where('farmer_code', $farmerCode)->first();
                } else {
                    $farmer = Farmer::where('local_code', 'like', "%$farmerCode%")->first();
                }
                $parentBatchCode = $farmer->farmer_code . '-' . ($newLastBID);
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

                if (isset($batch_numbers->transactions->transactions_invoices) && $batch_numbers->transactions->transactions_invoices) {
                    $transactionsInvoices = $batch_numbers->transactions->transactions_invoices;
                    $i = 1;
                    foreach ($transactionsInvoices as $key => $transactionsInvoice) {

                        if ($transactionsInvoice->invoice_image) {
                            $destinationPath = 'storage/app/images/';
                            $file = base64_decode($transactionsInvoice->invoice_image);
                            $file_name = time() . $i . getFileExtensionForBase64($file);
                            file_put_contents($destinationPath . $file_name, $file);
                            $userProfileImage = FileSystem::create([
                                        'user_file_name' => $file_name,
                            ]);
                            TransactionInvoice::create([
                                'transaction_id' => $parentTransaction->transaction_id,
                                'created_by' => $transactionsInvoice->created_by,
                                'invoice_id' => $userProfileImage->file_id,
                            ]);
                        }
                        $i++;
                    }
                }
            }
            array_push($batchesArray, $parentBatch->batch_id);
            BatchNumber::whereIn('batch_id', $childBatchNumberArray)->update(['is_parent' => $parentBatch->batch_id]);
            Transaction::whereIn('transaction_id', $childTransactionArray)->update(['is_parent' => $parentTransaction->transaction_id]);
        }

        $dataArray = array();
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $currentBatchesData = BatchNumber::whereIn('batch_id', $batchesArray)->with('childBatchNumber.latestTransation.transactionDetail')->with(['latestTransation' => function($query) use($user_image, $user_image_path) {
                        $query->with('transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
                                $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                            }]);
                    }])->get();
        foreach ($currentBatchesData as $key => $currentBatchData) {
            $patentTransactions = null;
            $patentTransactionsDetail = null;
            $childBatches = array();
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
                if (isset($patentTransactions->transactions_invoices)) {
                    foreach ($patentTransactions->transactions_invoices as $key => $transactions_invoices) {
                        $transactions_invoices->invoice_image = '';
                        if (isset($transactions_invoices->invoice)) {
                            $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
                        }
                        $transactions_invoices->makeHidden('invoice');
                    }
                }
            }
            $transactions_invoices = $patentTransactions->transactions_invoices;
            $currentBatch->makeHidden('transactions_invoices');
            $currentBatchData->makeHidden('latestTransation');
            //  $currentBatchData->makeHidden('transaction');
            $transactionData = ['transaction' => $patentTransactions, 'transactions_detail' => $patentTransactionsDetail, 'transactions_invoices' => $transactions_invoices];

            $data = ['batch' => $currentBatchData, 'child_batch' => $childBatches, 'transactions' => $transactionData];
            array_push($dataArray, $data);
        }
        return sendSuccess('Coffee was added Successfully', $dataArray);
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

        $allTransactions = array();
        $allTransactionsData = json_decode($request['transaction']);
        foreach ($allTransactionsData as $key => $transactions) {
            $newTransactionid = null;
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

            if (isset($transactions->transactions_invoices) && $transactions->transactions_invoices) {
                $transactionsInvoices = $transactions->transactions_invoices;
                $i = 1;
                foreach ($transactionsInvoices as $key => $transactionsInvoice) {

                    if ($transactionsInvoice->invoice_image) {
                        $destinationPath = 'storage/app/images/';
                        $file = base64_decode($transactionsInvoice->invoice_image);
                        $file_name = time() . $i . getFileExtensionForBase64($file);
                        file_put_contents($destinationPath . $file_name, $file);
                        $userProfileImage = FileSystem::create([
                                    'user_file_name' => $file_name,
                        ]);
                        TransactionInvoice::create([
                            'transaction_id' => $newTransaction->transaction_id,
                            'created_by' => $transactionsInvoice->created_by,
                            'invoice_id' => $userProfileImage->file_id,
                        ]);
                    }
                    $i++;
                }
            }
            $user_image = asset('storage/app/images/demo_user_image.png');
            $user_image_path = asset('storage/app/images/');
            $currentBatch = Transaction::where('transaction_id', $newTransactionid)->with('transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
                            $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                        }])->first();
            $transationsDetail = $currentBatch->transactionDetail;
            if (isset($currentBatch->transactions_invoices)) {
                foreach ($currentBatch->transactions_invoices as $key => $transactions_invoices) {
                    $transactions_invoices->invoice_image = '';
                    if (isset($transactions_invoices->invoice)) {
                        $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
                    }
                    $transactions_invoices->makeHidden('invoice');
                }
            }
            $currentBatch->makeHidden('transactionDetail');
            $transactions_invoices = $currentBatch->transactions_invoices;
            $currentBatch->makeHidden('transactions_invoices');
            $transations = $currentBatch;

            $data = ['transactions' => $transations, 'transactions_detail' => $transationsDetail, 'transactions_invoices' => $transactions_invoices];
            array_push($allTransactions, $data);
        }
        return sendSuccess('Coffee was added Successfully', $allTransactions);
    }

    function coffeeBuyerCoffee(Request $request) {
        $allTransactions = array();
        $user_image = asset('storage/app/images/demo_user_image.png');
        $user_image_path = asset('storage/app/images/');
        $transactions = Transaction::where('is_parent', 0)->where('created_by', $this->userId)->where('transaction_status', 'created')->doesntHave('isReference')->with('childTransation.transactionDetail', 'transactionDetail')->with(['transactions_invoices.invoice' => function($query) use($user_image, $user_image_path) {
                        $query->select('file_id', 'user_file_name', \DB::raw("IFNULL(CONCAT('" . $user_image_path . "/',`user_file_name`),IFNULL(`user_file_name`,'" . $user_image . "')) as user_file_name"));
                    }])->orderBy('transaction_id', 'desc')->get();
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
            if (isset($transaction->transactions_invoices)) {
                foreach ($transaction->transactions_invoices as $key => $transactions_invoices) {
                    $transactions_invoices->invoice_image = '';
                    if (isset($transactions_invoices->invoice)) {
                        $transactions_invoices->invoice_image = $transactions_invoices->invoice->user_file_name;
                    }
                    $transactions_invoices->makeHidden('invoice');
                }
            }
            $transactionDetail = $transaction->transactionDetail;
            $transactions_invoices = $transaction->transactions_invoices;
            $transaction->makeHidden('transactionDetail');
            $transaction->makeHidden('childTransation');
            $transaction->makeHidden('transactions_invoices');
            $data = ['transactions' => $transaction, 'child_transaction' => $childTransactions, 'transactions_detail' => $transactionDetail, 'transactions_invoices' => $transactions_invoices];
            array_push($allTransactions, $data);
        }

        return sendSuccess('Transactions retrieved successfully', $allTransactions);
    }

}
